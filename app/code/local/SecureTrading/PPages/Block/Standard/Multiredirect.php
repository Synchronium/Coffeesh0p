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

class SecureTrading_PPages_Block_Standard_Multiredirect extends Mage_Core_Block_Abstract {
    protected function _toHtml() {
		$standardModel = Mage::getModel('PPages/standard');
		
		$html = $standardModel->prepareMultishippingCheckoutForm();
		
        return $html;
    }
}