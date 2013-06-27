<?php

/**
 * SecureTrading STPP Shopping Carts
 * STPP Cart Interface Version 1.3.7
 * Last updated 22/03/2013
 * Written by Peter Barrow for SecureTrading Ltd.
 * http://www.securetrading.com
 */
 
abstract class AbstractSTPP
{
	protected $logDirectory;
	
	protected $moduleApiVersion = "2.0";
	
	public $paymentParams = array();
	
	public $errorParams = array();
	
	public $refundParams = array();
	
	public $cardStoreParams = array();
	
	public $riskDecisionParams = array();
	
	public $languageVars = array(
	
		// Front-end error messages
		'unexpected_error' => 'An unexpected error has occurred. Please try again. If the error persists, please contact an administrator.',
		'card_declined' => 'Your card has been declined, possibly due to a lack of funds.  Please check your details and try again.',
		'missing_field' => 'An incorrect %s was supplied.  Value: "%s"',
		
		// Various class errors
		'js_disabled' => 'You are seeing this message because you have Javascript disabled.  Please click on "Submit" to continue payment.',
		'no_term_url' => 'A term URL has not been supplied.',
		'md_or_pares_strings' => 'The MD or PaRes fields are not strings.',
		'createError_params_not_string_or_int' => 'The paramaters supplied to the AbstractSTAPI::createError() method are not all strings or integers.',
		'pan' => 'card number',
		'no_method_for_request_type' => 'A module-specific method cannot be found for the %s request type',
		'no_case_for_request_type' => 'The request type "%s" is not processed in the AbstractSTAPI::performQuery() switch.',
		'sockerr' => 'Socket Error: ',
		'nohostorport' => 'Host or port is not set.  Host: %s Port: %s',
		'no_alias' => 'An alias has not been provided.',
		'nousernameorpassword' => 'A username or password has not been provided.  Username: %s.  Password: %s.',
		'err-ssl-socket' => 'Error creating SSL socket.  Error: %s. (%s)',
		
		'invalid_settle_due_date' => 'An invalid settle due date (%i) has been provided.',
		'invalid_settle_status' => 'An invalid settle status (%s) has been provided.',
		'invalid_account_type' => 'An invalid account type (%s) has been provided.',
		'invalid_request_type' => 'An invalid request type (%s) has been provided.',
		'invalid_request_type_unspecified' => 'An invalid request type has been provided.', // Used in STPPXml.
		'response_not_xml' => 'The response string could not be loaded as XML.',
		'two_invalid_responses' => 'Two XML response objects were returned: %s and %s.',
		
		// Log file errors
		'cannot_create_logs_dir' => 'Logs dir does not exist.',
		'cannot_create_archive_dir' => 'Archive dir does not exist.',
		'bad_parmas' => 'Bad parameters.',
		'cannot_open_file' => 'Cannot open file.',
		'cannot_write_file' => 'Cannot write to file.',
		'cannot_read_file' => 'Cannot read file.',
		'cannot_close_file' => 'Cannot close file.',
		'cannot_truncate_file' => 'Cannot truncate file.',
		'cannot_unlink_file' => 'Cannot unlink file.',
		'cannot_retrieve_filesize' => 'Cannot retrieve filesize.',
		'invalid_filesize' => 'Invalid filesize.',
		'cannot_match_date' => 'Cannot match date.',
		'test_message' => 'This is a test entry to confirm that logging is working correctly.',
		'test_confirm' => 'The test entry ran without error.',
		
		// Web Services
		'err-ws-no-http-code' => 'An HTTP code could not be found in the response string.',
		'err-ws-401' => 'A HTTP 401 response has been returned (Unauthenticated).  Please check the username and password.',
		'err-ws-403' => 'A HTTP 403 response has been returned (Forbidden).',
		'err-ws-404' => 'A HTTP 404 response has been returned (File not Found).',
		'err-ws-other-http-code' => 'A HTTP %s response has been returned.',
		
		// Die error messages
		'magic_set_called' => 'An attempt has been made to set a property (%s = %s) not defined in the interface.',
		'magic_get_called' => 'An attempt has been made to get a property (%s) not defined in the interface.'
	);
	
	protected static $acceptedCurrencyArray = array(
		'AED', 'ANG', 'ARS', 'ATS', 'AUD', 'AWG', 'BBD', 'BDT', 'BEF', 'BGL', 'BHD', 'BMD', 'BND', 'BOB', 'BRL', 'BSD', 'BWP', 'BZD', 'CAD', 'CHF', 'CLP', 'CNY', 'COP', 'CRC', 'CYP', 'CZK', 'DEM', 'DKK', 'DOP', 'DZD', 'ECS', 
		'EEK', 'EGP', 'ESP', 'ETB', 'EUR', 'FIM', 'FRF', 'GBP', 'GIP', 'GNF', 'GRD', 'GTQ', 'GYD', 'HKD', 'HNL', 'HUF', 'IDR', 'IEP', 'ILS', 'INR', 'IQD', 'IRR', 'ISK', 'ITL', 'JMD', 'JOD', 'JPY', 'KES', 'KRW', 'KWD', 'KZT', 
		'LBP', 'LKR', 'LUF', 'LYD', 'MAD', 'MGF', 'MTL', 'MUR', 'MWK', 'MXN', 'MYR', 'NGN', 'NIO', 'NLG', 'NOK', 'NPR', 'NZD', 'OMR', 'PAB', 'PEN', 'PHP', 'PKR', 'PLN', 'PTE', 'PYG', 'QAR', 'ROL', 'RON', 'RUB', 'SAR', 'SCR', 
		'SDG', 'SEK', 'SGD', 'SIT', 'SKK', 'SLL', 'SRG', 'SVC', 'SYP', 'THB', 'TND', 'TRL', 'TRY', 'TTD', 'TWD', 'TZS', 'USD', 'UYU', 'VEB', 'VND', 'XAF', 'XCD', 'XOF', 'XPF', 'YER', 'ZAR', 'ZMK', 'ZWD'
	);
	
	protected static $settleStatusArray = array(
		0 => 'Pending Settlement',
		1 => 'Pending Settlement (Manually Overridden)',
		2 => 'Suspended',
		100 => 'Settled (Only available for certain aquirers)'
	);
	
	protected static $settleDueDateArray = array(
		0 => 'Process immediately',
		1 => 'Wait 1 day',
		2 => 'Wait 2 days',
		3 => 'Wait 3 days',
		4 => 'Wait 4 days',
		5 => 'Wait 5 days',
		6 => 'Wait 6 days',
		7 => 'Wait 7 days',
	);
	
	protected $stateCodesArray = array(
		'AA' =>	'Armed Forces Americas (except Canada)',
		'AE' => 'Armed Forces Middle East',
		'AK' => 'Alaska',
		'AL' => 'Alabama',
		'AP' => 'Armed Forces Pacific',
		'AR' => 'Arkansas',
		'AS' => 'American Samoa',
		'AZ' => 'Arizona',
		'CA' => 'California',
		'CO' => 'Colorado',
		'CT' => 'Connecticut',
		'DC' => 'District of Columbia',
		'DE' => 'Delaware',
		'FL' => 'Florida',
		'FM' => 'Federated States of Micronesia',
		'GA' => 'Georgia',
		'GU' => 'Guam',
		'HI' => 'Hawaii',
		'IA' => 'Iowa',
		'ID' => 'Idaho',
		'IL' => 'Illinois',
		'IN' => 'Indiana',
		'KS' => 'Kansas',
		'KY' => 'Kentucky',
		'LA' => 'Louisiana',
		'MA' => 'Massachusetts',
		'MD' => 'Maryland',
		'ME' => 'Maine',
		'MH' => 'Marshall Islands',
		'MI' => 'Michigan',
		'MN' => 'Minnesota',
		'MO' => 'Missouri',
		'MP' => 'Northern Mariana Islands',
		'MS' => 'Mississippi',
		'MT' => 'Montana',
		'NC' => 'North Carolina',
		'ND' => 'North Dakota',
		'NE' => 'Nebraska',
		'NH' => 'New Hampshire',
		'NJ' => 'New Jersey',
		'NM' => 'New Mexico',
		'NV' => 'Nevada',
		'NY' => 'New York',
		'OH' => 'Ohio',
		'OK' => 'Oklahoma',
		'OR' => 'Oregon',
		'PA' => 'Pennsylvania',
		'PR' => 'Puerto Rico',
		'PW' => 'Palau',
		'RI' => 'Rhode Island',
		'SC' => 'South Carolina',
		'SD' => 'South Dakota',
		'TN' => 'Tennessee',
		'TX' => 'Texas',
		'UT' => 'Utah',
		'VA' => 'Virginia',
		'VI' => 'Virgin Islands',
		'VT' => 'Vermont',
		'WA' => 'Washington',
		'WI' => 'Wisconsin',
		'WV' => 'West Viginia',
		'WY' => 'Wyoming'
	);
	
	/**
	 * Object instance.  Child class of AbstractHelper.
	 */
	protected $helper;
	
	abstract public function createException(Exception $e, $file, $class, $line);
	
	abstract protected function handleError($message, $params);
	
	public function __construct() {
		$this->logDirectory = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR;
		return $this;
	}
	
	/**
	 * Sets helper object instace.
	 */
	public function setHelper(AbstractSTPPHelper $helperObject) {
		$this->helper = $helperObject;
	}
	
	/**
	 * Retrieves instance of the current helper object.
	 */
	public function getHelper() {
		return $this->helper;
	}
	
	public static function formatPrice($price, $currency = '') { 
		return TRUE; // This method would be abstract if not for strict standards disallowing abstract static methods.
	}
	
	final public static function getAcceptedCurrencyArray() {
		return self::$acceptedCurrencyArray;
	}
	
	final public static function getSettleStatusArray() {
		return self::$settleStatusArray;
	}
	
	final public static function getSettleDueDateArray() {
		return self::$settleDueDateArray;
	}
	
	final public function getStateCodesArray() {
		return $this->stateCodesArray;
	}
	
	final protected function formatSettleDueDate($days_to_add_to_date = 0) {
		$days_to_add = '+ ' . (int) $days_to_add_to_date . ' days';
		return date('Y-m-d', strtotime($days_to_add));
	}
	
	final protected function checkSettleDueDate($settleDueDate) {
		if (!array_key_exists($settleDueDate, self::$settleDueDateArray)) {
			$this->createException(new Exception(sprintf($this->languageVars['invalid_settle_due_date'], $settleDueDate)), __FILE__, __CLASS__, __LINE__);
		}
	}
	
	final protected function checkSettleStatus($settleStatus) {
		if (!array_key_exists($settleStatus, self::$settleStatusArray)) {
			$this->createException(new Exception(sprintf($this->languageVars['invalid_settle_status'], $settleStatus)), __FILE__, __CLASS__, __LINE__);
		}
	}
	
	protected static function formatDate($time = '') {
		$time = !empty($time) ? $time : time();
		return date('dS M Y - H:i:s T', $time);
	}
	
	final public static function retrieveCurrentUrl() {
		$url = (!empty($_SERVER['HTTPS'])) ? "https://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] : "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
		return $url;
	}
	
	final protected function logError($error_message, $error_type, $calling_file, $calling_class, $calling_line, $log_test = FALSE)
	{
		$filePerms = 0750;
		$folderPerms = 0755;
		
		// Create logs directory if it does not exist.
		if (!is_dir($this->logDirectory)) {
			if (mkdir($this->logDirectory) === FALSE) {
				return $this->languageVars['cannot_create_logs_dir'];
			}
			chmod($this->logDirectory, $folderPerms);
		}
		
		// Create archive directory if it does not exist.
		if (!is_dir($this->logDirectory . 'archive')) {
			if (mkdir($this->logDirectory . 'archive') === FALSE) {
				return $this->languageVars['cannot_create_archive_dir'];
			}
			chmod($this->logDirectory . 'archive', $folderPerms);
		}
			
		$log_filepath = $this->logDirectory . 'log.txt';
		
		switch($calling_class) {
			
			case 'AbstractSTPP': // Log files
				$class = 'ASTPP';
				break;
			case 'AbstractAPI':
				$class = 'ABAPI';
				break;
			case 'AbstractSTAPI':
				$class = 'STAPI';
				break;
			case 'AbstractWebServices':
				$class = 'WSRV';
				break;
			case 'AbstractPaymentPages':
				$class = 'PPAGE';
				break;
			default:
				$class = 'SCART';
				break;
		}
		
		if (!is_string($error_message) || !is_string($calling_class) || !is_string($calling_file) || !is_int($calling_line) || !is_int($error_type)) {
			return $this->languageVars['bad_params'];
		}
		
		if (($message = $this->modifyArchiveIfRequired($log_filepath, $log_test, $filePerms)) !== TRUE) {
			return $message;
		}
		
		if (file_exists($log_filepath)) {
			chmod($log_filepath, $filePerms);
		}
		
		if (($file = fopen($log_filepath, 'a+')) === FALSE) {
			return $this->languageVars['cannot_open_file'];
		}
		
		if (!preg_match('/\d{5}: /', $error_message)) {
			$error_message = '00000: ' . $error_message;
		}
		
		$now = self::formatDate();
		
		// Remove a trailing newline from $error_message if one exists:
		$error_message = preg_replace(array('/\n$/', '/\r$/'), '', $error_message);
		
		$message = $now . ' - ' . $error_type . ' - ' . $class . ' - Line ' . sprintf('%04d', $calling_line) . ' - ' . $error_message . PHP_EOL;
		
		if (fwrite($file, $message) === FALSE) {
			return $this->languageVars['cannot_write_file'];
		}
		
		if (fclose($file) === FALSE) {
			return $this->languageVars['cannot_close_file'];
		}
		return TRUE;
	}
	
	/**
	 * Uses preg_match() to recieve the month and year of first entry in log file.  If this month or year is different to the currenty month/year, the following happens:
	 * The contents of the log file are put into a variable.  A new log file is created in the archive, named 'month_year_log.txt'.  The contents of the original log file are inserted into it and the new log file handle is closed.
	 * An empty string is saved into the original log file and this file handle, too, is closed.
	 */
	final private function modifyArchiveIfRequired($log_filepath, $log_test, $filePerms)
	{
		$current_month = date('M');
		
		// If the file doesn't exist yet, return TRUE since there can be no archiving to do.
		if (!file_exists($log_filepath)) {
			return TRUE;
		}
		
		if (($filesize = filesize($log_filepath)) === FALSE) {
			return $this->languageVars['cannot_retrieve_filesize'];
		}
		
		if ($filesize < 0) {
			return $this->languageVars['invalid_filesize'];
		}
		
		// Return TRUE if the filesize is 0 since there will be nothing to read.
		if ($filesize == 0) {
			return TRUE;
		}

		// Open file for reading.  May need to write later; previously used 'c+' but was incompatible with PHP 5.1.*.  Now open twice for better compatibility.
		if (!$log_file = fopen($log_filepath, 'r')) {
			die('notopen');return $this->languageVars['cannot_open_file'];
		}
		
		$contents = file_get_contents($log_filepath);
		
		if (fclose($log_file) === FALSE) {
		   return $this->languageVars['cannot_close_file'];
		}
		
		if (!preg_match('/^(?:[\d]{2})(?:[a-z]{2}) ([A-Za-z]{3}) ([\d]{4})/', $contents, $matches)) {
			return $this->languageVars['cannot_match_date'];
		}
		
		$now = date('M Y');
		$log_date = $matches[1] . ' ' . $matches[2];
		
		// Return TRUE if the current month and year are equal to the month and year of the first entry in the log file, since no further action is here needed.  If $log_test is TRUE, continue since we want to test the entire process.
		if ($now === $log_date && $log_test !== TRUE) {
			return TRUE;
		}
		
		$new_file_path = $this->logDirectory . 'archive/' . str_replace(' ', '_', strtolower($log_date)) . '_log.txt';
		
		if (($new_file = fopen($new_file_path, 'w+')) === FALSE) {
			return $this->languageVars['cannot_open_file'];
		}
		
		if (fwrite($new_file, $contents) === FALSE) {
			return $this->languageVars['cannot_write_file'];
		}
		
		if (fclose($new_file) === FALSE) {
			return $this->languageVars['cannot_close_file'];
		}
		
		chmod($new_file_path, $filePerms);
		
		// The new file (the archive file) has now been created and its' handle closed.  The original (main) log file still has the archive contents in and must be wiped. 
		if (!$log_file = fopen($log_filepath, 'w')) {
		   return $this->languageVars['cannot_open_file'];
		}
		
		if (fclose($log_file) === FALSE) {
			return $this->languageVars['cannot_close_file'];
		}
		
		// If we are here, the modification has been successful.  Return TRUE.
		return TRUE;
	}
	
	/**
	 * Stop inaccessible/undefined properties from being set to an instance.
	 */
	final public function __set($key, $value) {
		die(sprintf($this->languageVars['magic_set_called'], $key, $value));
	}
	
	/**
	 * Stop attempts to retrieve inaccessible/non-existent properties from an instance.
	 */
	final public function __get($key) {
		die(sprintf($this->languageVars['magic_get_called'], $key));
	}
}