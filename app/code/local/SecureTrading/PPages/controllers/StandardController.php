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

class SecureTrading_PPages_StandardController extends Mage_Core_Controller_Front_Action {

	/**
	 * Displays the form that redirects to the SecureTrading gateway.
	 */
	public function gatewayAction() {
         print $this->getResponse()->setBody($this->getLayout()->createBlock('PPages/standard_redirect')->toHtml());
		 exit;
    }
	
	/**
	 * Redirect script.  Displays the success message.  Accessed via index.php/securetrading/standard/redirect
	 */
	public function redirectAction() {
	
		// Clear cart
		foreach(Mage::getSingleton('checkout/session')->getQuote()->getItemsCollection() as $item) {
			Mage::getSingleton('checkout/cart')->removeItem( $item->getId() )->save();
		}
		
		Mage::getSingleton('PPages/standard')->redirectCleanup();
		Mage::getSingleton('PPages/standard')->setRedirectUrl($this->getRequest()->getParams());
		print $this->getResponse()->setBody($this->getLayout()->createBlock('PPages/standard_success')->toHtml());
		exit;
	}
	
	/**
	 * Notification script.  Updates transactions.  Accessed via index.php/securetrading/standard/notification
	 */
	 public function notificationAction() {
		Mage::getModel('PPages/standard')->processNotification($this->getRequest()->getPost());
	 }
}