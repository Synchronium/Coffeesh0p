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

class SecureTrading_PPages_Block_Standard_Success extends Mage_Core_Block_Abstract {
    protected function _toHtml() {
	
        $successUrl = Mage::getSingleton('PPages/standard')->getRedirectUrl();

        $html	= '
			<html>
			<meta http-equiv="refresh" content="0; URL=' . $successUrl . '">
			<body>
			<p>Your payment has been successfully processed.</p>
			<p>Please click <a href="%s">here</a> if you are not redirected automatically.</p>
			</body></html>
		';

        return $html;
    }
}