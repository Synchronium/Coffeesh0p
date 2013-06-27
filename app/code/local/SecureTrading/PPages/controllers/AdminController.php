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

class SecureTrading_PPages_AdminController extends Mage_Adminhtml_Controller_Action
{
	protected $_publicActions = array('redirect');
	
	public function redirectAction() {
		Mage::getSingleton ( 'core/session', array ('name' => 'adminhtml' ))->addSuccess('The order has been created.');
		header("Location: " . Mage::helper("adminhtml")->getUrl("adminhtml/sales_order/index"));
		exit;
	}
}