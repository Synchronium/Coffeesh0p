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

class SecureTrading_PPages_Block_Standard_Form extends Mage_Payment_Block_Form {
    protected function _construct() {
        $this->setTemplate('PPages/standard/form.phtml');
        parent::_construct();
    }
}