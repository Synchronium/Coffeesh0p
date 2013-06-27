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

if (realpath(dirname(__FILE__) . '/../../lib/securetrading_stpp/STPPLoader.php')) {
	require_once(realpath(dirname(__FILE__) . '/../../lib/securetrading_stpp/STPPLoader.php'));
	require_once(realpath(dirname(__FILE__) . '/../../lib/MagentoPPages.class.php'));
}
else { // Compiler enabled.
	require_once(realpath(dirname(__FILE__) . '/../../app/code/local/SecureTrading/PPages/lib/securetrading_stpp/STPPLoader.php'));
	require_once(realpath(dirname(__FILE__) . '/../../app/code/local/SecureTrading/PPages/lib/MagentoPPages.class.php'));
}

class SecureTrading_PPages_Model_Source_SettleDueDate
{
    public function toOptionArray()
    {
		$array = MagentoPPages::getSettleDueDateArray();
		$newArray = array();
		
		foreach($array as $k => $v) {
			$newArray[] = array(
				'value' => $k,
				'label' => $v,
			);
		}
        
		return $newArray;
    }
}