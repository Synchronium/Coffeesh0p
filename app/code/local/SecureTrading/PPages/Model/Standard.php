<?php

/**
 * SecureTrading STPP Shopping Carts
 * Magento 1.7.0.1
 * Module Version 2.5.9
 * Last Updated 26 March 2013
 * Written by Peter Barrow for SecureTrading Ltd.
 * http://www.securetrading.com
 */

?><?php

if (realpath(dirname(__FILE__) . '/../lib/securetrading_stpp/STPPLoader.php')) {
	require_once(realpath(dirname(__FILE__) . '/../lib/securetrading_stpp/STPPLoader.php'));
	require_once(realpath(dirname(__FILE__) . '/../lib/MagentoPPages.class.php'));
}
else { // Compiler enabled.
	require_once(realpath(dirname(__FILE__) . '/../../app/code/local/SecureTrading/PPages/lib/securetrading_stpp/STPPLoader.php'));
	require_once(realpath(dirname(__FILE__) . '/../../app/code/local/SecureTrading/PPages/lib/MagentoPPages.class.php'));
}

class SecureTrading_PPages_Model_Standard extends Mage_Payment_Model_Method_Abstract {

    protected $_isGateway                   = FALSE;
    protected $_canOrder                    = TRUE;
    protected $_canAuthorize                = FALSE;
    protected $_canCapture                  = FALSE;
    protected $_canCapturePartial           = FALSE;
    protected $_canRefund                   = FALSE;
    protected $_canRefundInvoicePartial     = FALSE;
    protected $_canVoid                     = FALSE;
    protected $_canUseInternal              = TRUE;
    protected $_canUseCheckout              = TRUE;
    protected $_canUseForMultishipping      = TRUE;
    protected $_isInitializeNeeded          = TRUE;
    protected $_canFetchTransactionInfo     = FALSE;
    protected $_canReviewPayment            = FALSE;
    protected $_canCreateBillingAgreement   = FALSE;
    protected $_canManageRecurringProfiles  = FALSE;
	
	protected $_code = 'PPages_standard';
	protected $_formBlockType = 'PPages/Standard_form';
    protected $_allowCurrencyCode = array();
	
	protected $redirectUrl; // SecureTrading specific variable.  Will be set to either checkout/onepage/success or checkout/multishipping/success.
	const PAYMENT_TYPE_AUTH_ONLY = 'AUTH';
	const PAYMENT_TYPE_AUTH_CAPTURE = 'AUTH_CAPTURE';
	
	/**
	 * Constructor.  Set accepted currency codes to model.
	 */
	public function __construct() {
		$ppages = new MagentoPPages();
		$this->_allowCurrencyCode = $ppages->getAcceptedCurrencyArray();
	}
	
	/**
	 * Not required.  Return FALSE.
	 */
	public function getDebugFlag() {
		return FALSE;
	}
	
	/**
	 * Calling initialize and setting the state in here allows us to set our own order status and skip the code block that would attempt to call capture(), authorize(), etc.
	 */
	public function initialize($paymentAction, $stateObject) {
		$stateObject->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT);
		$stateObject->setStatus(TRUE);
        return $this;
    }
	
	/**
	 * Returns a URL to a method in our controller.  This controller will handle the process of redirecting to the SecureTrading gateway.
	 */
	public function getOrderPlaceRedirectUrl() {
		return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB, Mage::app()->getStore()->isCurrentlySecure()) . 'index.php/securetrading/standard/gateway';
	}
	
	/**
	 * Return hidden form that will automatically redirect (using JS) to the SecureTrading gateway.  Called from our Redirect block.
	 */
	public function prepareStandardCheckoutForm() {

		$orderIncrementId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
        
		$requestObject = $this->prepareOrderInformation($orderIncrementId);
		
		$ppages = new MagentoPPages();
		
		ob_start();
		$ppages->runPaymentPages($requestObject, TRUE);
		$contents = ob_get_contents();
		ob_end_clean();
		
		return $contents;
	}
	
	/**
	 * Return hidden form that will automatically redirect (using JS) to the SecureTrading gateway.  Called from our Multishipping block.
	 */
	public function prepareMultishippingCheckoutForm() {
	
		// Load all order IDs.  Set in our multishipping model.
		$orderIds = $_SESSION['st_ms_order_ids'];
		unset($_SESSION['st_ms_order_ids']);
		
		// Calculate the order total by adding the cost of each individual order:
		$amount = 0;
		
		foreach($orderIds as $orderIncrementId) {
			$order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
			$amount += $order->getTotalDue();
		}
		
		// Force the checkout_sidebar block to remove the 'You have \d+ items in your cart' message after checkout has been completed and the user chooses to visit his (now empty) cart again.
		$quote = Mage::getSingleton('checkout/cart')->getQuote();
		$quote->setItemQty(0)->save();
		Mage::getSingleton('checkout/cart')->setQuote($quote)->save();
		
		// Prepare the request object:
		$requestObject = $this->prepareOrderInformation($orderIds[0], $orderIds, $amount); // Build array with first order ID (so first shipping address will go to gateway)
		
		$ppages = new MagentoPPages();
		
		ob_start();
		$ppages->runPaymentPages($requestObject, TRUE);
		$contents = ob_get_contents();
		ob_end_clean();
		
		return $contents;
	}
	
	/**
	 * Return hidden form that will automatically redirect (using JS) to the SecureTrading gateway.  Called from our Redirect block.
	 */
	public function prepareAdminCheckoutForm() {

		$emailConfirmation = Mage::getSingleton('checkout/session')->getEmailConfirmation() === FALSE ? FALSE : TRUE;
		Mage::getSingleton('checkout/session')->setEmailConfirmation(NULL);
		
		$orderIncrementId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
        
		$requestObject = $this->prepareOrderInformation($orderIncrementId, FALSE, NULL, TRUE, $emailConfirmation);
		
		$ppages = new MagentoPPages();
		
		ob_start();
		$ppages->runPaymentPages($requestObject, TRUE);
		$contents = ob_get_contents();
		ob_end_clean();
		
		return $contents;
	}
	
	/**
	 * Form stdClass $requestObject to pass through the STPP Shopping Cart Framework.
	 */
	protected function prepareOrderInformation($orderIncrementId, $multiShippingOrderIds = FALSE, $amount = NULL, $adminOrder = FALSE, $emailConfirmation = TRUE) {
		
		// Exit if we have been passed an array of Multishipping order IDs but not the total cost of all these orders.  This will only happen due to an error in client code so simple exit is okay. 
		if ($multiShippingOrderIds && !$amount) {
			exit('Multishipping order numbers have been supplied but the amount has not.');
		}
		
		$order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
		
		// The amount is passed as the third paramater if multishipping is enabled (the total cost of all multishiping orders.  It can be retrieved normally from the order object if multishipipng is not used.
		$amount = $amount === NULL ? $order->getTotalDue() : $amount;
		
		$currency_code = $order->getOrderCurrencyCode();
		 
		$billingAddress = $order->getBillingAddress();
		$billingCounty = $billingAddress->getCountry() == 'US' ? $billingAddress->getRegionCode() : $billingAddress->getRegion();
		$billingTelNo = $billingAddress->getTelephone();
		
		if ($order->getShippingMethod()) {
			$customerAddress = $order->getShippingAddress();
			$customerCounty = $customerAddress->getCountry() == 'US' ? $customerAddress->getRegionCode() : $customerAddress->getRegion();
			$customerTelNo = $customerAddress->getTelephone();
		}
		
		$requestObject = new stdClass;
		$requestObject->sitereference = $this->getConfigData("site_reference");
		$requestObject->mainamount = MagentoPPages::formatPrice($amount, $currency_code);
		$requestObject->currencyiso3a = $currency_code;
		
		$requestObject->billingfirstname = $billingAddress->getFirstname();
		$requestObject->billinglastname = $billingAddress->getLastname();
		$requestObject->billingpremise = $billingAddress->getStreet(1);
		$requestObject->billingstreet = $billingAddress->getStreet(2);
		$requestObject->billingtown = $billingAddress->getCity();
		$requestObject->billingcounty = $billingCounty;
		$requestObject->billingpostcode = $billingAddress->getPostcode();
		$requestObject->billingcountryiso2a = $billingAddress->getCountry();
		$requestObject->billingemail = $order->getCustomerEmail();
		$requestObject->billingtelephone = $billingTelNo;
		$requestObject->billingtelephonetype = !empty($billingTelNo) ? 'H' : '';
		
		if ($order->getShippingMethod()) {
			$requestObject->customerpremise = $customerAddress->getStreet(1);
			$requestObject->customerstreet = $customerAddress->getStreet(2);
			$requestObject->customertown = $customerAddress->getCity();
			$requestObject->customercounty = $customerCounty;
			$requestObject->customerpostcode = $customerAddress->getPostcode();
			$requestObject->customertelephone = $customerTelNo;
			$requestObject->customertelephonetype = !empty($customerTelNo) ? 'H' : '';
			$requestObject->customeremail = $order->getCustomerEmail();
		}
		
		$requestObject->orderreference = $multiShippingOrderIds ? $multiShippingOrderIds[0] : Mage::getSingleton('checkout/session')->getLastRealOrderId();
		
		$requestObject->settleduedate = $this->getConfigData('settleduedate');
		$requestObject->settlestatus = $this->getConfigData('payment_action') == 'AUTHORIZATION' ? 2 : $this->getConfigData("settlestatus");
		
		$ppages = new MagentoPPages();
		$ppages->createHash($requestObject, $this->getConfigData('site_security'));
		
		$requestObject->multishipping = serialize($multiShippingOrderIds);
		$requestObject->url = Mage::getBaseUrl();
		
		if ($adminOrder) {
			$requestObject->adminorder = TRUE;
			$requestObject->accounttypedescription = 'MOTO';
		}
		else {
			$requestObject->adminorder = FALSE;
		}
		
		$requestObject->emailconfirmation = $emailConfirmation;
		
		$requestObject->parentcss = $this->getConfigData('parent_css');
		$requestObject->childcss = $this->getConfigData('child_css');
		$requestObject->parentjs = $this->getConfigData('parent_js');
		$requestObject->childjs = $this->getConfigData('child_js');
		
        return $requestObject;
	}
	
	/**
	 * Notification script.  Called by StandardController::notificationAction().
	 */
	public function processNotification($postData) {
	
		$ppages = new MagentoPPages();
		
		// Validate the post data:
		
		if (!isset($postData['adminorder'])) {
			$ppages->createException(new Exception('The adminorder field was not returned to the notification script.'), __FILE__, __CLASS__, __LINE__);
		}
				
		if (!isset($postData['emailconfirmation'])) {
			$ppages->createException(new Exception('The emailconfirmation field was not returned to the notification script.'), __FILE__, __CLASS__, __LINE__);
		}
		
		if (!isset($postData['errorcode'])) {
			$ppages->createException(new Exception('The errorcode was not returned to the notification script.'), __FILE__, __CLASS__, __LINE__);
		}
		
		if ($postData['errorcode'] != 0) {
			$ppages->createException(new Exception('A non-zero errorcode was returned to the notification script.'), __FILE__, __CLASS__, __LINE__);
		}
		
		if (!isset($postData['orderreference'])) {
			$ppages->createException(new Exception('The orderreference was not returned to the notification script.'), __FILE__, __CLASS__, __LINE__);
		}
		
		if (!isset($postData['transactionreference'])) {
			$ppages->createException(new Exception('The transactionreference was not returned to the notification script.'), __FILE__, __CLASS__, __LINE__);
		}
		
		if (!isset($postData['responsesitesecurity']) && $this->getConfigData('use_notification_hash')) {
			$ppages->createException(new Exception('The notification hash was enabled but the responsesitesecurity field was not returned to the notification script.'), __FILE__, __CLASS__, __LINE__);
		}
		
		if (isset($postData['responsesitesecurity']) && hash('sha256', $postData['adminorder'] . $postData['emailconfirmation'] . $postData['errorcode'] . $postData['multishipping'] . $postData['orderreference'] . $postData['transactionreference'] . $this->getConfigData('notification_hash')) !== $postData['responsesitesecurity']) {
			$ppages->createException(new Exception('The regenerated notification hash did not match the returned hash.'), __FILE__, __CLASS__, __LINE__);
		}
		
		// Perform the notification:
		if (!isset($postData['multishipping']) || unserialize($postData['multishipping']) === FALSE) { // Perform normal notification.
			$this->processTransaction($postData['orderreference'], $postData['transactionreference'], $postData['adminorder'], $postData['emailconfirmation']);
		}
		else { // Perform multishipping notification.
			foreach(unserialize($postData['multishipping']) as $orderIncrementId) {
				$this->processTransaction($orderIncrementId, $postData['transactionreference']);
			}
		}
		
		exit('Notification complete.');
	}
	
	/**
	 * Encapsulates the logic required by the notification script.  Updates a transaction from 'Pending payment' to 'Processing'/'Pending' depending on payment action (auth/auth and capture).
	 */
	protected function processTransaction($orderIncrementId, $transactionReference, $adminOrder = FALSE, $emailConfirmation = TRUE) {
		
		$order = Mage::getModel('sales/order');
		$order->loadByIncrementId($orderIncrementId);
		
		if(!$order->getId()) {
			exit('The order ID was not retrieved.');
		}
		
		if (!$order->canInvoice()) {
			exit('The order could not be invoiced.  This may happen if the invoice has been created manually before the notification script has run.');
		} 

		$authOnly = $this->getConfigData('payment_action') == self::PAYMENT_TYPE_AUTH_ONLY ? TRUE : FALSE;
		
		$payment = $order->getPayment();
		$payment->setIsTransactionPending($authOnly);
		
		$invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();

		if (!$invoice->getTotalQty()) {
			Mage::throwException(Mage::helper('core')->__('Cannot create an invoice without products.'));
		}
		
		$invoiceState = ($authOnly) ? Mage_Sales_Model_Order_Invoice::STATE_OPEN : Mage_Sales_Model_Order_Invoice::STATE_PAID;
		$invoice->setState($invoiceState);
		$invoice->setTransactionId($transactionReference);
		
		$invoice->register();
		
		Mage::getModel('core/resource_transaction')
			->addObject($invoice)
			->addObject($invoice->getOrder())
			->save();
		
		$order->getPayment()->setTransactionId($transactionReference);

		// Update order status based on chosen payment action:
		if ($authOnly) {
			$order->setState(Mage_Sales_Model_Order::STATE_HOLDED, TRUE, '', TRUE);
		}
		else {
			$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, TRUE, '', TRUE);
		}
		
		// Always send the confirmation email if it is not an admin order.  If it is an admin order, only send if $emailConfirmation is true.
		$sendEmail = $adminOrder ? $emailConfirmation : TRUE;
		
		if ($sendEmail) {
			$order->sendNewOrderEmail();
		}
		
		$order->save();
		
		// Add information to 'Transactions' screen:
		$transactionType = $this->getConfigData('payment_action') == self::PAYMENT_TYPE_AUTH_ONLY ? Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH : Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE;
		
		$transaction = Mage::getModel('sales/order_payment_transaction');
		$transaction->setOrderPaymentObject($order->getPayment());
		$transaction->setTxnType($transactionType);
		$transaction->setTxnId($transactionReference);
		$transaction->save();
	}
	
	/**
	 * Set redirect URL.  If 'multishipping' has been returned in the query string and can be unserialized to a boolean TRUE then we redirect to multishipping success.  Otherwise to onepage success.
	 */
	public function setRedirectUrl($requestParams) {
		
		$ppages = new MagentoPPages();
		
		if (!isset($requestParams['url'])) {
			$ppages->createException(new Exception('The "url" parameter has not been returned to the redirect script.'), __FILE__, __CLASS__, __LINE__);
		}
		
		if (!isset($requestParams['multishipping'])) {
			$ppages->createException(new Exception('The "multishipping" parameter has not been returned to the redirect script.'), __FILE__, __CLASS__, __LINE__);
		}
		
		if (!isset($requestParams['adminorder'])) {
			$ppages->createException(new Exception('The "adminorder" parameter has not been returned to the redirect script.'), __FILE__, __CLASS__, __LINE__);
		}
		
		if ($requestParams['adminorder']) {
			$this->redirectUrl = $requestParams['url'] . 'securetradingppages/admin/redirect/';
		}
		else {
			if (@unserialize($requestParams['multishipping'])) {
				$this->redirectUrl = $requestParams['url'] . 'checkout/multishipping/success';
			}
			else {
				$this->redirectUrl = $requestParams['url'] . 'checkout/onepage/success';
			}
		}
	}
	
	/**
	 * Return redirect URL.
	 */
	public function getRedirectUrl() {
		return $this->redirectUrl;
	}
	
	/**
	 * Removes the 'You have \d items in your cart' message if a user views a product/uses a page with the sidebar after having processed a successful transaction and emptied their cart.
	 */
	 public function redirectCleanup() {
		Mage::getSingleton('checkout/cart')->getQuote()->setItemsQty(0)->save();
	 }
}