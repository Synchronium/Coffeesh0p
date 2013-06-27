<?php

/**
 * SecureTrading STPP Shopping Carts
 * STPP Cart Interface Version 1.3.7
 * Last updated 22/03/2013
 * Written by Peter Barrow for SecureTrading Ltd.
 * http://www.securetrading.com
 */
 
abstract class AbstractAPI extends AbstractSTPP
{
	protected $requestObject;
	
	protected $apiVersion = '3.67';
	
	private $xmlVersion = '1.0';
	
	private $xmlInputEncoding = 'UTF-8';
	
	private $xmlOutputEncoding = 'UTF-8';
	
	protected $use3dSecure = FALSE;
	
	protected $accountTypesArray = array(
		'ECOM', 
		'MOTO', 
		'RECUR'
	);
	
	protected $requestTypesArray = array(
		'auth',
		'threedauth_enrolled',
		'threedauth_notenrolled',
		'threedquery',
		'refund',
		'updaterefund',
		'updatepartialrefund',
		'cardstore',
		'riskdecsingle',
		'riskdecmultipleauth',
		'riskdecmultiple3dquery',
	);
	
	protected $amexErrorCasesArray = array(
		60031,
		60032
	);
	
	protected static $cardsArray = array(
		'VISA' => 'Visa',
		'MASTERCARD' => 'Mastercard',
		'JCB' => 'JCB',
		'AMEX' => 'American Express',
		'DISCOVER' => 'Discover',
		'ELECTRON' => 'Electron',
		'DINERS' => 'Diners',
		'SWITCH' => 'Switch',
		'SOLO' => 'Solo',
		'LASER' => 'Laser',
		'MAESTRO' => 'Maestro'
	);
	
	protected static $expiryMonthsArray = array(
		'01' => 'Jan',
		'02' => 'Feb',
		'03' => 'Mar',
		'04' => 'Apr',
		'05' => 'May',
		'06' => 'Jun',
		'07' => 'Jul',
		'08' => 'Aug',
		'09' => 'Sep',
		'10' => 'Oct',
		'11' => 'Nov',
		'12' => 'Dec'
	);
	
	public $callbackParams = array();
	
	private $acsUrl = '';
	
	abstract protected function sendAndReceiveData($xml_string);
	
	public function __construct() {
		return parent::__construct();
	}
	
	public static function getCardsArray() {
		return self::$cardsArray;
	}
	
	public static function getExpiryMonthsArray() {
		return self::$expiryMonthsArray;
	}
	
	public static function getStartYearsArray() {
		
		$startYears = array();
		
		for ($i = 20; $i >= 0; $i--) {
			$year = date('Y', time() - ($i * (60 * 60 * 24 * 356)));
			$startYears[$i] = $year;
		}
		return $startYears;
	}
	
	public static function getExpiryYearsArray() {
			
		$expiryYears = array();
		
		for ($i = 0; $i <= 20; $i++) {
			$year = date('Y', time() + ($i * (60 * 60 * 24 * 356)));
			$expiryYears[$i] = $year;
		}
		return $expiryYears;
	}
	
	public static function createExpiryMonths($useValue = FALSE) {
	
		$months = '';
		
		foreach(self::$expiryMonthsArray as $k => $v) {
			$month = $useValue === TRUE ? $v : $k; // Use a three-digit textual representation of the month if $useValue is set to TRUE.
			$months .= "<option value='$k'>$month</option>";
		}
		return $months;
	}
	
	 public static function createExpiryYears() {
	 
		$yearsArray = self::getExpiryYearsArray();
		$yearsString = '';
		
		foreach($yearsArray as $v) {
			$yearsString .= "<option value='$v'>$v</option>";
		}
		return $yearsString;
	}
	
	public static function createStartYears() {
	
		$yearsArray = self::getStartYearsArray();
		$yearsString = '';
		
		foreach($yearsArray as $v) {
			$yearsString .= "<option value='$v'>$v</option>";
		}
		return $yearsString;
	}
	
	public static function createCardTypes() {
		
		$cards = '';
		
		foreach(self::$cardsArray as $k => $v) {
			$cards .= "<option value='$k'>$v</option>";
		}
		return $cards;
	}
	
	public function checkAccountType($accountType) {
		if (!in_array($accountType, $this->accountTypesArray)) {
			$this->createException(new Exception(sprintf($this->languageVars['invalid_account_type']), $accountType), __FILE__, __CLASS__, __LINE__);
		}
	}
	
	public function checkRequestType($requestType) {
		if (!in_array($requestType, $this->requestTypesArray)) {
			$this->createException(new Exception(sprintf($this->languageVars['invalid_request_type'], $requestType)), __FILE__, __CLASS__, __LINE__);
		}
	}
	
	public static function formatPrice($amount, $currency = '') {
		$amount = round($amount, 2) * 100;
		return (int) $amount;
	}
	
	public static function createTermUrl($path) {
		return dirname(self::retrieveCurrentUrl()) . $path;
	}
	
	public function setXmlVersion($version) {
		return $this->xmlVersion = $version;
	}
	
	public function setXmlInputEncoding($encoding) {
		return $this->xmlInputEncoding = $encoding;
	}
	
	public function setXmlOutputEncoding($encoding) {
		return $this->xmlOutputEncoding = $encoding;
	}
	
	protected function processPayment($params) {
		return TRUE;
	}
	
	protected function processPaymentFailure($params) {
		return FALSE;
	}
	
	protected function process3dSecure($params) {
		return $this->processPayment($params);
	}
	
	protected function process3dSecureFailure($params) {
		return $this->processPaymentFailure($params);
	}
	
	protected function processRefund($params) {
		return TRUE;
	}
	
	protected function processRefundFailure($params) {
		return FALSE;
	}
	
	protected function processCardStore($params) {
		return TRUE;
	}
	
	protected function processCardStoreFailure($params) {
		return FALSE;
	}
	
	protected function processRiskDecision($params, stdClass $requestObject) {
		return;
	}
	
	protected function processLog3dSecure(stdClass $request_object, stdClass $result) {
		return TRUE;
	}
	
	public function createError($errorCode, $errorData, $errorMessage, $file, $class, $line)
	{
		// Ensure all passed items can be printed to a text file:
		$errorCode = (string) $errorCode;
		$errorData = (string) $errorData;
		$errorMessage = (string) $errorMessage;
		
		// Return without logging the error if it is a 'special case' error:
		if (in_array($errorCode, $this->amexErrorCasesArray)) {
			return;
		}
		
		// Retrieve the value of the incorrect parameter if it can be retrieved.  Set it to NULL otherwise.  Caution: this MAY no be the correct value; it depends on how the XML field names map to the request object 'keys'.
		$enteredValue = isset($this->requestObject->{$errorData}) ? $this->requestObject->{$errorData} : 'NULL';
		
		// Generate the merchant-suitable error message and log it:
		$errorMessage = !empty($errorData) 
		? sprintf('%05s', $errorCode) . ': ' . $errorMessage . ' (' . $errorData . ' value: ' . $enteredValue . ')'
		: sprintf('%05s', $errorCode) . ': ' . $errorMessage;
		
		$this->logError($errorMessage, 1, __FILE__, __CLASS__, __LINE__);
		
		// Generate the customer-suitable error message and display it:
		switch($errorCode) {
			case "30000":
				$errorData = $errorData === 'pan' ? $this->languageVars['pan'] : $errorData; // Replace 'pan' with 'card number'.
				$message = sprintf($this->languageVars['missing_field'], $errorData, $enteredValue);
				break;
				
			case "70000":
				$message = $this->languageVars['card_declined'];
				break;
				
			default:
				$message = $this->languageVars['unexpected_error'];
		}
		$this->handleError($message, $this->errorParams);
	}
	
	public function createException(Exception $e, $file, $class, $line)
	{
		$this->logError($e->getMessage(), 2, $file, $class, $line);
		$message = $this->languageVars['unexpected_error'];
		$this->handleError($message, $this->errorParams);
	}
	
	public function runSTAPI(stdClass $request_object, $use_3d_secure = FALSE)
	{
		$this->use3dSecure = $use_3d_secure;
		
		// Assign the settleduedate and settlestatus fields default values if they have not been set:
		$request_object->settleduedate = isset($request_object->settleduedate) ? $request_object->settleduedate : 0;
		$request_object->settlestatus = isset($request_object->settlestatus) ? $request_object->settlestatus : 0;
		 
		// Ensure the settleduedate and the settlestatus provided are both valid:
		$this->checkSettleDueDate($request_object->settleduedate);
		$this->checkSettleStatus($request_object->settlestatus);
		
		// Ensure alias has been set:
		if (empty($request_object->alias)) {
			$this->createException(new Exception($this->languageVars['no_alias']), __FILE__, __CLASS__, __LINE__);
		}
		
		// Format the settleduedate:
		$request_object->settleduedate = $this->formatSettleDueDate($request_object->settleduedate);
		
		// Add the API Version to the request object:
		$request_object->apiversion = $this->apiVersion;
		
		// This intercepts $request_objects where a 'requesttype' property has been set and runs the query.  It is how refunds etc. are intended to be run since they are not so common as to be given a separate method.
		if (isset($request_object->requesttype)) {
			if ($request_object->requesttype == 'riskdecmultiple') { // If requesttype ir riskdecmultiple, set it to run a RISKDEC/3DQUERy or RISKDEC/AUTH depending on whether 3D Secure is enabled or not.
				$request_object->requesttype = $use_3d_secure ? 'riskdecmultiple3dquery' : 'riskdecmultipleauth';
			}
			if ($this->performQuery($request_object)) {
				return TRUE;
			}
			return FALSE;
		}
		
		// Performs the 3D Query Logic:
		try {
			if ((bool) $use_3d_secure === TRUE) { // Perform 3D Query (3D Secure Enabled)
				
				if (!isset($request_object->termurl)) {
					throw new Exception($this->languageVars['no_term_url']);
				}
				$request_object->requesttype = 'threedquery';
			}
			else { // Perform standard auth (3D Secure Disabled)
				$request_object->requesttype = 'auth';
			}
			
			if ($this->performQuery($request_object)) {
				return TRUE;
			}
			return FALSE;
		}
		catch(Exception $e) {
			$this->createException($e, __FILE__, __CLASS__, __LINE__);
			return FALSE;
		}
	}
	
	private function performQuery(stdClass $request_object)
	{	
		try {
			// Add the requestObject to the class so it can be accessed by error logs.
			$this->requestObject = $request_object;
			
			// Check the request type:
			$this->checkRequestType($request_object->requesttype);
			
			// Send and receive the XML request/response:
			$xmlClass = new STPP_XML($request_object, $this->xmlVersion, $this->xmlInputEncoding, $this->xmlOutputEncoding);
			$xml = $xmlClass->formatRequest();
			
			$xmlResult = $this->sendAndReceiveData($xml);
			$result = $xmlClass->buildResponseObject($xmlResult);
			
			$this->paymentParams['request'] = $request_object;
			$this->callbackParams['request'] = $request_object;
			$this->errorParams['request'] = $request_object;
			$this->refundParams['request'] = $request_object;
			$this->cardStoreParams['request'] = $request_object;
			$this->riskDecisionParams['request'] = $request_object;
			
			// Add the result object to all properties that may be required by the module-specific classes:
			$this->paymentParams['result'] = $result;
			$this->callbackParams['result'] = $result;
			$this->errorParams['result'] = $result;
			$this->refundParams ['result'] = $result;
			$this->cardStoreParams['result'] = $result;
			$this->riskDecisionParams['result'] = $result;		
		}
		catch (STAPIException $e) {
			return FALSE;
		}
		
		if ($result->errorcode == "0") {
			return $this->runAppropriateMethod($request_object, $result);
		}
		else {
			$errorData = isset($result->errordata) ? $result->errordata : ''; // errordata is not set for all error codes.  The most common error code without errordata is likely to be 60031/60032.
			$bool = $this->runAppropriateMethod($request_object, $result, TRUE); // This will usually return FALSE.  The only instance it should return TRUE is when coming from self::run3dLogicFailure();
			$this->createError($result->errorcode, $errorData, $result->errormessage, __FILE__, __CLASS__, __LINE__);
			return (bool) $bool;
		}
	}
	
	private function runAppropriateMethod(stdClass $request_object, stdClass $result, $failure = FALSE)
	{
		$suffix = $failure === TRUE ? 'Failure' : '';
		
		switch($request_object->requesttype) {
			case 'auth':
			case 'riskdecmultipleauth':
				if ($result->errorcode == "20004") {
					$method = 'postRiskDecisionMissingParent';
					break;
				}
				$method = 'processPayment' . $suffix;
				$property_name = 'paymentParams';
				break;
			case 'threedquery':
			case 'riskdecmultiple3dquery':
				if ($result->errorcode == "20004") {
					$method = 'postRiskDecisionMissingParent';
					break;
				}
				$method = 'handle3dResponseLogic' . $suffix;
				$property_name = 'callbackParams';
				break;
			case "threedauth_enrolled":
			case 'threedauth_notenrolled':
				$method = 'process3dSecure' . $suffix; 
				$property_name = 'callbackParams';
				break;
			case 'refund':
			case 'updaterefund':
			case 'updatepartialrefund':
				$method = 'processRefund' . $suffix;
				$property_name = 'refundParams';
				break;
			case 'cardstore':
				$method = 'processCardStore' . $suffix;
				$property_name = 'cardStoreParams';
				break;
			case 'riskdecsingle':
				$method = 'handleRiskDecisionResponseLogic';
				$property_name = 'riskDecisionParams';
				break;
			default:
				$this->createException(new Exception(sprintf($this->languageVars['no_case_for_request_type'], $request_object->requesttype)), __FILE__, __CLASS__, __LINE__);
				return;
		}
		
		if (!method_exists($this, $method)) {
			$this->createException(new Exception(sprintf($this->languageVars['no_method_for_request_type'], $request_object->requesttype)), __FILE__, __CLASS__, __LINE__);
		}
		
		// This is a 'special case' method that can not be overridden in module-specific classes.  It must be called with the $request_object and the $result properties.
		if ($method === 'handle3dResponseLogic' || $method === 'handle3dResponseLogicFailure') {
			if ($this->$method($request_object, $result) === TRUE) {
				return TRUE;
			}
		}
		elseif($method === 'handleRiskDecisionResponseLogic' || $method == 'postRiskDecisionMissingParent') {
			return $this->$method($request_object, $result);
		}
		else {
			$property = !empty($this->$property_name) ? $this->$property_name : array();
			
			if ($this->$method($property)) {
				return TRUE;
			}
		}
		return FALSE;
	}
	
	/**
	 * This method is called if an errorcode 0 is returned from a threedquery.
	 */
	protected function handle3dResponseLogic(stdClass $request_object, stdClass $result)
	{
		$this->processLog3dSecure($request_object, $result);
		
		if ($result->enrolled == "Y") { // Perform 3D Auth (Card enrolled)
		
			if (method_exists($this, 'before3dRedirectAction')) {
			
				$this->callbackParams['termurl'] = $request_object->termurl; // Add termurl to callback params so it can be retrieved in before3dRedirectAction
				
				if ($this->before3dRedirectAction($this->callbackParams) === FALSE) { // This conditional added 13/1/2012
					return FALSE;
				}
			}
			
			$this->displayAcsRedirectForm($result->acsurl, $result->pareq, $request_object->termurl, $result->md);
		}
		else { // Perform standard auth (Card not enrolled)
			$request_object->parenttransactionreference = $result->transactionreference;
			$request_object->requesttype = 'threedauth_notenrolled';
			
			if ($this->performQuery($request_object)) {
				return TRUE;
			}
			return FALSE;
		}
	}
	
	/**
	 * Handles a risk decision response.
	 */
	protected function handleRiskDecisionResponseLogic(stdClass &$request_object, stdClass $result) {
		$this->processRiskDecision($this->riskDecisionParams, $request_object);
		$request_object->requesttype = $this->use3dSecure ? 'threedquery' : 'auth';
		$request_object->parenttransactionreference = $result->transactionreference;
		sleep(3);
		return $this->performQuery($request_object); // Sleep and then send AUTH/THREEDQUERY with PTR.
	}
	
	/**
	 * Calld if an AUTH is sent and returns missing parent (so when we sent a 'riskdecsingle'). Unset PTR and send AUTH again.  Also works the same for THREEDQUERY requests.
	 */
	protected function postRiskDecisionMissingParent(stdClass &$request_object, stdClass $result) { 
		$this->logError("Ran {$request_object->requesttype} after a riskdecision for order with ID {$request_object->orderreference}.  Query returned errorcode 20004: missing parent when referencing transaction reference {$request_object->parenttransactionreference}.  Sending another auth request with no parent transaction reference.  This auth will not be linked to the riskdecision request in MyST.", 0, __FILE__, __CLASS__, __LINE__);
		$request_object->parenttransactionreference = NULL;
		return $this->performQuery($request_object);
	}
	
	/**
	 * Display form that automatically submits to ACS.
	 */
	public function displayAcsRedirectForm($acsUrl, $pareq, $termurl, $md) {
		?>
			<body onLoad="javascript:document.process.submit();">
			<form action="<?php echo $acsUrl; ?>" name="process" id="process" method="post">
				<input type="hidden" name="PaReq" value="<?php echo $pareq; ?>" />
				<input type="hidden" name="TermUrl" value="<?php echo $termurl; ?>" />
				<input type="hidden" name="MD" value="<?php echo $md; ?>" />
				<input type="submit" name="submit_form" value="Submit" />
			</form>
			<noscript>
				<?php echo $this->languageVars['js_disabled']; ?>
			</noscript>
		<?php exit;
	}
	
	/**
	 * This method is called if a non-zero errorcode is returned from a threedquery.
	 */
	protected function handle3dResponseLogicFailure(stdClass $request_object, stdClass $result)
	{
		if (in_array($result->errorcode, $this->amexErrorCasesArray)) { // Perform standard auth (AMEX card)
			$request_object->parenttransactionreference = $result->transactionreference;
			$request_object->requesttype = 'threedauth_notenrolled';
			
			if ($this->performQuery($request_object)) {
				return TRUE;
			}
			return FALSE;
			
		}
		else { // Error (Error code sent back) 
			$this->process3dSecureFailure($this->callbackParams);
			$errorData = isset($result->errordata) ? $result->errordata : '';
			$this->createError($result->errorcode, $errorData, $result->errormessage, __FILE__, __CLASS__, __LINE__);
			return FALSE;
		}
	}
	
	public function run3dCallback($alias, $exit = TRUE)
	{
		if (!isset($_POST['MD']) || !isset($_POST['PaRes'])) {
			return;
		}
		
		try {
			if (!is_string($_POST['MD'])|| !is_string($_POST['PaRes'])) {
				throw new Exception($this->languageVars['md_or_pares_strings']);
			}
			
			$request_object = new stdClass;
			$request_object->alias = $alias;
			$request_object->apiversion = $this->apiVersion;
			$request_object->md = $_POST['MD'];
			$request_object->pares = $_POST['PaRes'];
			$request_object->requesttype = 'threedauth_enrolled';
			
			if ($this->performQuery($request_object) === TRUE) {
				if ($exit === TRUE) exit;
				return TRUE;
			}
			return FALSE;
		}
		catch (Exception $e) {
			$this->createException($e, __FILE__, __CLASS__, __LINE__);
			if ($exit === TRUE) exit;
			return FALSE;
		}
	}
}