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

include("Mage/Adminhtml/controllers/Sales/Order/CreateController.php");

class SecureTrading_PPages_CreateController extends Mage_Adminhtml_Sales_Order_CreateController
{
    public function saveAction()
    {
		########################################
		### Begin SecureTrading Modification ###
		########################################
		
		$payment = $this->getRequest()->getPost('payment');
		
		if ($payment['method'] !== 'PPages_standard') {
			return parent::saveAction();
		}
		
		try {
			$method = method_exists($this, '_processActionData') ? '_processActionData' : '_processData'; // _processActionData() in 1.5.0.1+, _processData() in 1.4.2.0-
			$this->$method('save');
			
		########################################
		#### End SecureTrading Modification ####
		########################################
			
            if ($paymentData = $this->getRequest()->getPost('payment')) {
                $this->_getOrderCreateModel()->setPaymentData($paymentData);
                $this->_getOrderCreateModel()->getQuote()->getPayment()->addData($paymentData);
            }

			// See if the 'Email Order Confirmation' checkbox has been ticked and store its value.
			$orderPost = $this->getRequest()->getPost('order');
			$sendEmail = isset($orderPost['send_confirmation']) && $orderPost['send_confirmation'] ? TRUE : FALSE;
			
            $order = $this->_getOrderCreateModel()
                ->setIsValidate(true)
				->importPostData($orderPost)
				->setSendConfirmation(FALSE)
                ->createOrder();
				
			########################################
			### Begin SecureTrading Modification ###
			########################################
			
			$session = Mage::getSingleton('checkout/session');
			$session->setLastRealOrderId($order->getRealOrderId());
			$session->setEmailConfirmation($sendEmail);
			$order->save();
			
			print $this->getResponse()->setBody($this->getLayout()->createBlock('PPages/standard_adminRedirect')->toHtml());
			exit;
			
			########################################
			#### End SecureTrading Modification ####
			########################################
			
        } catch (Mage_Payment_Model_Info_Exception $e) {
            $this->_getOrderCreateModel()->saveQuote();
            $message = $e->getMessage();
            if( !empty($message) ) {
                $this->_getSession()->addError($message);
            }
            $this->_redirect('*/*/');
        } catch (Mage_Core_Exception $e){
            $message = $e->getMessage();
            if( !empty($message) ) {
                $this->_getSession()->addError($message);
            }
            $this->_redirect('*/*/');
        }
        catch (Exception $e){
            $this->_getSession()->addException($e, $this->__('Order saving error: %s', $e->getMessage()));
            $this->_redirect('*/*/');
        }
    }
}