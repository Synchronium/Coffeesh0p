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

require("Mage/Checkout/controllers/MultishippingController.php");

class SecureTrading_PPages_MultishippingController extends Mage_Checkout_MultishippingController
{
	/**
	 * ST Implementation.  If the 'success' action is requested, do not run preDispatch() checks of the parent class.  These would fail and cause us to be redirected to the cart.
	 */
	public function preDispatch() {
       if (Mage::app()->getRequest()->getActionName() === 'success') {
           return;
       }
       return parent::preDispatch();
	}
	
	/**
	 * ST Implementation.  Called before redirecting to the gateway.  Our alterations to this core method have been labelled clearly in the code.
	 */
    public function overviewPostAction()
    {
        if (!$this->_validateMinimumAmount()) {
            return;
        }
		
        try {
            if ($requiredAgreements = Mage::helper('checkout')->getRequiredAgreementIds()) {
                $postedAgreements = array_keys($this->getRequest()->getPost('agreement', array()));
                if ($diff = array_diff($requiredAgreements, $postedAgreements)) {
                    $this->_getCheckoutSession()->addError($this->__('Please agree to all Terms and Conditions before placing the order.'));
                    $this->_redirect('*/*/billing');
                    return;
                }
            }
			
            $payment = $this->getRequest()->getPost('payment');
            $paymentInstance = $this->_getCheckout()->getQuote()->getPayment();
            if (isset($payment['cc_number'])) {
                $paymentInstance->setCcNumber($payment['cc_number']);
            }
            if (isset($payment['cc_cid'])) {
                $paymentInstance->setCcCid($payment['cc_cid']);
            }
			
            $this->_getCheckout()->createOrders();
			
			###########################################
			###### SecureTrading Addition Begins ######
			###########################################
			
			// Empty the cart (to replicate core behaviour when processing a normal order):
			$cartHelper = Mage::helper('checkout/cart');
			$items = $cartHelper->getCart()->getItems();
		
			foreach ($items as $item) {
				$itemId = $item->getItemId();
				$cartHelper->getCart()->removeItem($itemId)->save();
			}
			
			// Redirect to the gateway:
			print $this->getResponse()->setBody($this->getLayout()->createBlock('PPages/standard_multiredirect')->toHtml());
			exit;
			
			// Lots of un-needed core code removed from here.
			
			#########################################
			###### SecureTrading Addition Ends ######
			#########################################
			
        } catch (Mage_Payment_Model_Info_Exception $e) {
            $message = $e->getMessage();
            if( !empty($message) ) {
                $this->_getCheckoutSession()->addError($message);
            }
            $this->_redirect('*/*/billing');
        } catch (Mage_Checkout_Exception $e) {
            Mage::helper('checkout')
                ->sendPaymentFailedEmail($this->_getCheckout()->getQuote(), $e->getMessage(), 'multi-shipping');
            $this->_getCheckout()->getCheckoutSession()->clear();
            $this->_getCheckoutSession()->addError($e->getMessage());
            $this->_redirect('*/cart');
        }
        catch (Mage_Core_Exception $e){
            Mage::helper('checkout')
                ->sendPaymentFailedEmail($this->_getCheckout()->getQuote(), $e->getMessage(), 'multi-shipping');
            $this->_getCheckoutSession()->addError($e->getMessage());
            $this->_redirect('*/*/billing');
        } catch (Exception $e){
            Mage::logException($e);
            Mage::helper('checkout')
                ->sendPaymentFailedEmail($this->_getCheckout()->getQuote(), $e->getMessage(), 'multi-shipping');
            $this->_getCheckoutSession()->addError($this->__('Order place error.'));
            $this->_redirect('*/*/billing');
        }
    }
	
	/**
	 * ST Implementation.  Call setCompleteStep() and setActiveStep() before calling parent::successAction().  
	 * These calls will correct the breadcrumb trail and remove the  'You have \d items in your cart' message from the right sidebar after a user has completed a transaction and views their cart/a product.
	 */
	public function successAction() {
		$this->_getState()->setCompleteStep(Mage_Checkout_Model_Type_Multishipping_State::STEP_OVERVIEW);
		$this->_getState()->setActiveStep(Mage_Checkout_Model_Type_Multishipping_State::STEP_SUCCESS);
		parent::successAction();
	}
}