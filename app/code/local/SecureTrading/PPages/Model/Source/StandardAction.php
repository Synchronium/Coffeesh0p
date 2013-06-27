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

class SecureTrading_PPages_Model_Source_StandardAction
{
    public function toOptionArray()
    {
		return array(
            array('value' => SecureTrading_PPages_Model_Standard::PAYMENT_TYPE_AUTH_ONLY, 'label' => 'Authorize Only'),
            array('value' => SecureTrading_PPages_Model_Standard::PAYMENT_TYPE_AUTH_CAPTURE, 'label' => 'Authorize & Capture'),
        );
    }
}