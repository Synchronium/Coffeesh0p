<?php

/**
 * SecureTrading STPP Shopping Carts
 * STPP Cart Interface Version 1.3.7
 * Last updated 22/03/2013
 * Written by Peter Barrow for SecureTrading Ltd.
 * http://www.securetrading.com
 */
 
abstract class AbstractPaymentPages extends AbstractSTPP
{
	private $version = 1;
	
	protected static $url = 'https://payments.securetrading.net/process/payments/choice';
	
	public function __construct() {
		return parent::__construct();
	}
	
	public static function formatPrice($amount, $currency = '') {
		$amount = (float) $amount;
		return sprintf('%.2f', $amount);
	}
	
	public static function returnUrl() {
		return self::$url;
	}
	
	/**
	 * If a shopping cart does not call self::runPaymentPages() to access SecureTrading, it must call this method manually to validate/format the data correctly.
	 */
	public function preChecks(stdClass &$request_object) {
		// Assign the settleduedate and settlestatus fields default values if they have not been set:
		$request_object->settleduedate = isset($request_object->settleduedate) ? $request_object->settleduedate : 0;
		$request_object->settlestatus = isset($request_object->settlestatus) ? $request_object->settlestatus : 0;
		 
		 // Unset CSS and JS params if they are set and empty:
		$this->validateCssAndJs($request_object);
		 
		// Ensure the settleduedate and the settlestatus provided are both valid:
		$this->checkSettleDueDate($request_object->settleduedate);
		$this->checkSettleStatus($request_object->settlestatus);
		
		// Format the settleduedate:
		$request_object->settleduedate = $this->formatSettleDueDate($request_object->settleduedate);
		
		// Add the version to the request object:
		$request_object->version = $this->version;
		
		// Calculate the sitesecurity hash:
		if (isset($request_object->_useSiteSecurity) && $request_object->_useSiteSecurity) {
			$this->addHash($request_object);
		}
	}
	
	/**
	 * This method can be used to create the hidden input fields of a form in a cart's template file.
	 **/
	public function returnHiddenFields($request_object) {
		$string = '';
		$postData = get_object_vars($request_object);
		
		foreach($postData as $k => $v) {
			$v = htmlentities($v, ENT_QUOTES);
			$string .= "<input type='hidden' name='$k' value='$v' />\n";
		}
		return $string;
	}
	
	/**
	 * This method can be called to create a form that submits to SecureTrading.
	 * It will call self::preChecks() automatically to check/format the passed data, and self::returnHiddenFields() to create the hidden input fields of the form.
	 * @param $requestObject stdClass The information to be passed to SecureTrading.
	 * @param $autoSubmit bool Set to TRUE in order to add a JS call to make the form auto-submit.  Set to FALSE to create an ordinary form.
	 */
	public function runPaymentPages(stdClass $request_object, $autoSubmit = TRUE)
	{
		$this->preChecks($request_object);
		$url = self::$url;
		
		if ($autoSubmit === TRUE) {
			echo "<body onLoad='javascript: document.process.submit();'>\n\n";
		}
		
		echo "<form method='post' action='{$url}' name='process' enctype='multipart/form-data' accept-charset='UTF-8'>\n";
		
		echo $this->returnHiddenFields($request_object);
		
		if ($autoSubmit === TRUE) {
			echo "
				<noscript>{$this->languageVars['js_disabled']}</noscript>
			";
		}
	}
	
	public function createException(Exception $e, $file, $class, $line)
	{
		$this->logError($e->getMessage(), 2, $file, $class, $line);
		$message = $this->languageVars['unexpected_error'];
		$this->handleError($message, $this->errorParams);
	}
	
	public function getUrl() {
		return self::$url;
	}
	
	/**
	 * This function was refactored in STPP Cart Interface Version 1.3.6.  The settleduedate was to be added to the hash in this version but the settleduedate is formatted from an int to a string (in YYYY-MM-DD format) AFTER createHash() was called.
	 * The solution was to add the following properties to the $requestObject and to actually calculate the hash at the end of $this::preChecks().
	 * For this solution to be acceptable the call to unset() in $this::addHash() MUST NOT be removed.
	 */
	static public function createHash(&$requestObject, $storePass) {
		$requestObject->_useSiteSecurity = TRUE;
		$requestObject->_siteSecurityPassword = $storePass;
	}
	
	private function addHash(&$requestObject) {
		$requestObject->sitesecurity = 'g' . hash('sha256', $requestObject->currencyiso3a . $requestObject->mainamount . $requestObject->sitereference . $requestObject->settlestatus . $requestObject->settleduedate . $requestObject->_siteSecurityPassword);
		unset($requestObject->_useSiteSecurity, $requestObject->_siteSecurityPassword); // DO NOT REMOVE.
	}
	
	private function validateCssAndJs(&$request_object) {
		if (isset($request_object->parentjs) && empty($request_object->parentjs)) {
			unset($request_object->parentjs);
		}
		
		if (isset($request_object->childjs) && empty($request_object->childjs)) {
			unset($request_object->childjs);
		}
		
		if (isset($request_object->parentcss) && empty($request_object->parentcss)) {
			unset($request_object->parentcss);
		}
		
		if (isset($request_object->childcss) && empty($request_object->childcss)) {
			unset($request_object->childcss);
		}
	}
}