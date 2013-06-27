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
 
class SecureTrading_PPages_Model_Type_Multishipping extends Mage_Checkout_Model_Type_Multishipping
{
   public function createOrders()
   {
		###########################################
		###### SecureTrading Addition Begins ######
		###########################################
		
		if (get_class($this->getQuote()->getPayment()->getMethodInstance()) !== 'SecureTrading_PPages_Model_Standard') {
			return parent::createOrders();
		}
		
		###########################################
		####### SecureTrading Addition Ends #######
		###########################################
		
        $orderIds = array();
        $this->_validate();
        $shippingAddresses = $this->getQuote()->getAllShippingAddresses();
        $orders = array();

        if ($this->getQuote()->hasVirtualItems()) {
            $shippingAddresses[] = $this->getQuote()->getBillingAddress();
        }
		
        try {
            foreach ($shippingAddresses as $address) {
                $order = $this->_prepareOrder($address);
				
                $orders[] = $order;
                Mage::dispatchEvent(
                    'checkout_type_multishipping_create_orders_single',
                    array('order'=>$order, 'address'=>$address)
                );
            }
			
            foreach ($orders as $order) {
			
				###########################################
				###### SecureTrading Addition Begins ######
				###########################################
				
				$quote = $this->getQuote();
				$transaction = Mage::getModel('core/resource_transaction');
				$transaction->addObject($quote->getCustomer());
				$transaction->addObject($quote);
				$transaction->addObject($order);
				$transaction->save();
				
				$order->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, 'pending_payment', '', false);
				
				$orderIds[] = $order['increment_id'];
				$order->save();
				usleep(700000); // Sleep for 0.7 second for each order.  This stops a bug that was adding orders in the admin panel in the wrong order (e.g. 30, 32, 31 instead of 30, 31, 32).
				
				// Lots of other actions happen here in the core code.  We do not need to do any of that here.
			}
			
			// These two lines are SecureTrading additions.  They are not in core code.
			$_SESSION['st_ms_order_ids'] = $orderIds;
			return;
			
			###########################################
			####### SecureTrading Addition Ends #######
			###########################################
		
        } catch (Exception $e) {
            Mage::dispatchEvent('checkout_multishipping_refund_all', array('orders' => $orders));
            throw $e;
        }
    }
}