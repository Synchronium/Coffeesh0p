<?php

/**
 * SecureTrading STPP Shopping Carts
 * STPP Cart Interface Version 1.3.7
 * Last updated 22/03/2013
 * Written by Peter Barrow for SecureTrading Ltd.
 * http://www.securetrading.com
 */
 
 if (!class_exists('AbstractSTPP', FALSE)) {
	require_once(dirname(__FILE__) . '/AbstractSTPP.class.php');
	require_once(dirname(__FILE__) . '/AbstractAPI.class.php');
	require_once(dirname(__FILE__) . '/AbstractSTAPI.class.php');
	require_once(dirname(__FILE__) . '/AbstractWebServices.class.php');
	require_once(dirname(__FILE__) . '/AbstractPaymentPages.class.php');
	require_once(dirname(__FILE__) . '/STPPXml.class.php');
	require_once(dirname(__FILE__) . '/AbstractSTPPHelper.class.php');
}