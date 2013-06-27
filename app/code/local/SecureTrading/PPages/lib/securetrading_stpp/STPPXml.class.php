<?php

/**
 * SecureTrading STPP Shopping Carts
 * STPP Cart Interface Version 1.3.7
 * Last updated 22/03/2013
 * Written by Peter Barrow for SecureTrading Ltd.
 * http://www.securetrading.com
 */
 
class STPP_XML
{
	private $requestObject;
	
	private $xmlVersion;
	
	private $xmlInputEncoding;
	
	private $xmlOutputEncoding;
	
	function __construct(stdClass $requestObject, $xmlVersion = 1.0, $xmlInputEncoding = 'UTF-8', $xmlOutputEncoding = 'UTF-8') {
		$this->requestObject = $requestObject;
		$this->xmlVersion = $xmlVersion;
		$this->xmlInputEncoding = $xmlInputEncoding;
		$this->xmlOutputEncoding = $xmlOutputEncoding;
	}
	
	function formatRequest()
	{
		$args = $this->requestObject;
		
		if (strcasecmp($this->xmlInputEncoding, 'UTF-8') !== 0) { // If the supplied input encoding is not UTF-8:
			if (function_exists('mb_convert_encoding')) { // If the mbstring module is enabled (i.e. if mb_convert_encoding() is a recognised function):
				foreach($args as $k => $v) {
					$args->$k = mb_convert_encoding($v, 'UTF-8', $this->xmlInputEncoding); // Convert all input parameters to UTF-8.
				}
			}
		}
		
		$vars['api_version'] = isset($args->apiversion) ? $args->apiversion : '';
		$vars['site_reference'] = isset($args->sitereference) ? $args->sitereference : '';
		$vars['alias'] = isset($args->alias) ? $args->alias : '';
		$vars['order_reference'] = isset($args->orderreference) ? $args->orderreference : '';
		$vars['ip'] = isset($args->ip) ? $args->ip : '';
		$vars['currency_code'] = isset($args->currencycode) ? $args->currencycode : '';
		$vars['amount'] = isset($args->amount) ? $args->amount : '';
		$vars['settle_due_date'] = isset($args->settleduedate) ? $args->settleduedate : '';
		$vars['settle_status'] = isset($args->settlestatus) ? $args->settlestatus : '';
		$vars['account_type_description'] = isset($args->accounttypedescription) ? $args->accounttypedescription : '';
		$vars['parent_transaction_reference'] = isset($args->parenttransactionreference) ? $args->parenttransactionreference : '';
		$vars['term_url'] = isset($args->termurl) ? htmlspecialchars_decode($args->termurl) : ''; // Old modules that did not use the XMLWriter in this class were sending &amp; instead of & in the term URL (if it had a query string).  XMLWriter handles entities automatically, so convert them to normal text here.
		$vars['md'] = isset($args->md) ? $args->md : '';
		$vars['pares'] = isset($args->pares) ? $args->pares : '';
		$vars['transaction_reference'] = isset($args->transactionreference) ? $args->transactionreference : '';
		$vars['settle_base_amount'] = isset($args->settlebaseamount) ? $args->settlebaseamount : '';
		
		$vars['customer_telephone_type'] = isset($args->customerteltype) ? $args->customerteltype : '';
		$vars['customer_telephone_number'] = isset($args->customertelno) ? $args->customertelno : '';
		$vars['customer_county'] = isset($args->customercounty) ? $args->customercounty : '';
		$vars['customer_street'] = isset($args->customerstreet) ? $args->customerstreet : '';
		$vars['customer_postcode'] = isset($args->customerpostcode) ? $args->customerpostcode : '';
		$vars['customer_premise'] = isset($args->customerpremise) ? $args->customerpremise : '';
		$vars['customer_town'] = isset($args->customertown) ? $args->customertown : '';
		$vars['customer_country'] = isset($args->customercountry) ? $args->customercountry : '';
		$vars['customer_middle_name'] = isset($args->customermiddlename) ? $args->customermiddlename : '';
		$vars['customer_prefix'] = isset($args->customerprefix) ? $args->customerprefix : '';
		$vars['customer_last_name'] = isset($args->customerlastname) ? $args->customerlastname : '';
		$vars['customer_first_name'] = isset($args->customerfirstname) ? $args->customerfirstname : '';
		$vars['customer_suffix'] = isset($args->customersuffix) ? $args->customersuffix : '';
		$vars['customer_email'] = isset($args->customeremail) ? $args->customeremail : '';
		
		$vars['billing_telephone_type'] = isset($args->billingteltype) ? $args->billingteltype : '';
		$vars['billing_telephone_number'] = isset($args->billingtelno) ? $args->billingtelno : '';
		$vars['billing_county'] = isset($args->billingcounty) ? $args->billingcounty : '';
		$vars['billing_street'] = isset($args->billingstreet) ? $args->billingstreet : '';
		$vars['billing_postcode'] = isset($args->billingpostcode) ? $args->billingpostcode : '';
		$vars['billing_premise'] = isset($args->billingpremise) ? $args->billingpremise : '';
		$vars['billing_town'] = isset($args->billingtown) ? $args->billingtown : '';
		$vars['billing_country'] = isset($args->billingcountry) ? $args->billingcountry : '';
		$vars['billing_middle_name'] = isset($args->billingmiddlename) ? $args->billingmiddlename : '';
		$vars['billing_prefix'] = isset($args->billingprefix) ? $args->billingprefix : '';
		$vars['billing_last_name'] = isset($args->billinglastname) ? $args->billinglastname : '';
		$vars['billing_first_name'] = isset($args->billingfirstname) ? $args->billingfirstname : '';
		$vars['billing_suffix'] = isset($args->billingsuffix) ? $args->billingsuffix : '';
		$vars['billing_email'] = isset($args->billingemail) ? $args->billingemail : '';
		
		$vars['payment_type'] = isset($args->paymenttype) ? $args->paymenttype : '';
		$vars['start_date'] = isset($args->startdate) ? $args->startdate : '';
		$vars['expiry_date'] = isset($args->expirydate) ? $args->expirydate : '';
		$vars['pan'] = isset($args->pan) ? $args->pan : '';
		$vars['security_code'] = isset($args->securitycode) ? $args->securitycode : '';
		$vars['issue_number'] = isset($args->issuenumber) ? $args->issuenumber : '';
		
		$xmlWriter = $this->setXmlOptions();
		$xmlWriter->startDocument($this->xmlVersion, $this->xmlOutputEncoding);
		
		switch(strtolower($args->requesttype)) {
			case 'auth':
				$this->formatAuthOr3dQuery($xmlWriter, $vars, FALSE);
				break;
			case 'threedquery':
				$this->formatAuthOr3dQuery($xmlWriter, $vars, TRUE);
				break;
			case 'threedauth_enrolled':
				$this->format3dAuthEnrolled($xmlWriter, $vars);
				break;
			case 'threedauth_notenrolled':
				$this->format3dAuthNotEnrolled($xmlWriter, $vars);
				break;
			case 'refund':
				$this->formatRefund($xmlWriter, $vars);
				break;
			case 'updaterefund':
				$this->formatUpdateRefund($xmlWriter, $vars);
				break;
			case 'updatepartialrefund':
				$this->formatUpdatePartialRefund($xmlWriter, $vars);
				break;
			case 'cardstore':
				$this->formatCardStore($xmlWriter, $vars);
				break;
			case 'transactionupdate':
				$this->formatTransactionUpdate($xmlWriter, $vars);
				break;
			case 'riskdecsingle':
				$this->formatRiskDecision($xmlWriter, $vars);
				break;
			case 'riskdecmultiple3dquery':
				$this->formatRiskDecisionAndAuthOr3dQuery($xmlWriter, $vars, TRUE);
				break;
			case 'riskdecmultipleauth':
				$this->formatRiskDecisionAndAuthOr3dQuery($xmlWriter, $vars, FALSE);
				break;
			default:
				throw new Exception($this->languageVars['invalid_request_type_unspecified']);
				
			$xmlWriter->endDocument();
		}
	
		$xmlRequest = $xmlWriter->outputMemory();
		
		return $xmlRequest;
	}
	
	private function setXmlOptions() {
		$xmlWriter = new XMLWriter();
		$xmlWriter->openMemory();

		$xmlWriter->setIndent(TRUE);
		$xmlWriter->setIndentString('   ');
		return $xmlWriter;
	}
	
	private function formatAuthOr3dQuery(XMLWriter &$xmlWriter, $vars, $use3dSecure)
	{
		$requestType = $use3dSecure ? 'THREEDQUERY' : 'AUTH';
		
		extract($vars);
		
		$xmlWriter->startElement('requestblock');
		$xmlWriter->startAttribute('version');
		$xmlWriter->text($api_version);
		$xmlWriter->endAttribute();
		
			// <alias>
			$xmlWriter->writeElement('alias', $alias);
			
			// <request>
			$xmlWriter->startElement('request');
			$xmlWriter->startAttribute('type');
			$xmlWriter->text($requestType);
			$xmlWriter->endAttribute();
			
				// <merchant>
				$xmlWriter->startElement('merchant');
				
					// <orderreference></orderreference>
					$xmlWriter->writeElement('orderreference', $order_reference);
					
					// If request type is 'threedquery':
					if ($use3dSecure) {
					
						// <termurl></termurl>
						$xmlWriter->writeElement('termurl', $term_url);
					}
					
				// </merchant>
				$xmlWriter->endElement();
				
				// <customer>
				$xmlWriter->startElement('customer');
					
					// <ip></ip>
					$xmlWriter->writeElement('ip', $ip);
					
					// <telephone type=''></telephone>
					$xmlWriter->startElement('telephone');
					$xmlWriter->startAttribute('type');
					$xmlWriter->text($customer_telephone_type);
					$xmlWriter->endAttribute();
					$xmlWriter->text($customer_telephone_number);
					$xmlWriter->endElement();
					
					// <street></street>
					$xmlWriter->writeElement('street', $customer_street);
					
					// <postcode></postcode>
					$xmlWriter->writeElement('postcode', $customer_postcode);
					
					// <premise></premise>
					$xmlWriter->writeElement('premise', $customer_premise);
					
					// <town></town>
					$xmlWriter->writeElement('town', $customer_town);
					
					// <country></country>
					$xmlWriter->writeElement('country', $customer_country);
					
					// <name>
					$xmlWriter->startElement('name');
						
						// <middle></middle>
						$xmlWriter->writeElement('middle', $customer_middle_name);
						
						// <prefix></prefix>
						$xmlWriter->writeElement('prefix', $customer_prefix);
						
						// <last></last>
						$xmlWriter->writeElement('last', $customer_last_name);
						
						// <first></first>
						$xmlWriter->writeElement('first', $customer_first_name);
						
						// <suffix></suffix>
						$xmlWriter->writeElement('suffix', $customer_suffix);
					
					// </name>
					$xmlWriter->endElement();
					
					// <email></email>
					$xmlWriter->writeElement('email', $customer_email);
					
				// </customer>
				$xmlWriter->endElement();
				
				// <billing>
				$xmlWriter->startElement('billing');

					// <telephone type=''></telephone>
					$xmlWriter->startElement('telephone');
					$xmlWriter->startAttribute('type');
					$xmlWriter->text($billing_telephone_type);
					$xmlWriter->endAttribute();
					$xmlWriter->text($billing_telephone_number);
					$xmlWriter->endElement();
					
					// <street></street>
					$xmlWriter->writeElement('street', $billing_street);
					
					// <postcode></postcode>
					$xmlWriter->writeElement('postcode', $billing_postcode);
					
					// <premise></premise>
					$xmlWriter->writeElement('premise', $billing_premise);
					
					// <town></town>
					$xmlWriter->writeElement('town', $billing_town);
					
					// <country></country>
					$xmlWriter->writeElement('country', $billing_country);
					
					// <payment type=''>
					$xmlWriter->startElement('payment');
					$xmlWriter->startAttribute('type');
					$xmlWriter->text($payment_type);
					$xmlWriter->endAttribute();
						
						// <startdate></startdate>
						$xmlWriter->writeElement('startdate', $start_date);
						
						// <expirydate></expirydate>
						$xmlWriter->writeElement('expirydate', $expiry_date);
						
						// <pan></pan>
						$xmlWriter->writeElement('pan', $pan);
						
						// <securitycode></securitycode>
						$xmlWriter->writeElement('securitycode', $security_code);
						
						// <issuenumber></issuenumber>
						$xmlWriter->writeElement('issuenumber', $issue_number);
					
					// </payment>
					$xmlWriter->endElement();
					
					// <name>
					$xmlWriter->startElement('name');
						
						// <middle></middle>
						$xmlWriter->writeElement('middle', $billing_middle_name);
						
						// <prefix></prefix>
						$xmlWriter->writeElement('prefix', $billing_prefix);
						
						// <last></last>
						$xmlWriter->writeElement('last', $billing_last_name);
						
						// <first></first>
						$xmlWriter->writeElement('first', $billing_first_name);
						
						// <suffix></suffix>
						$xmlWriter->writeElement('suffix', $billing_suffix);
					
					// </name>
					$xmlWriter->endElement();
					
					// <amount currencycode=''></amount>
					$xmlWriter->startElement('amount');
					$xmlWriter->startAttribute('currencycode');
					$xmlWriter->text($currency_code);
					$xmlWriter->endAttribute();
					$xmlWriter->text($amount);
					$xmlWriter->endElement();
					
					// <email></email>
					$xmlWriter->writeElement('email', $billing_email);
					
				// </billing>
				$xmlWriter->endElement();
				
				// <operation>
				$xmlWriter->startElement('operation');
					
					// <sitereference></sitereference>
					$xmlWriter->writeElement('sitereference', $site_reference);
					
					// <accounttypedescription></accounttypedescription>
					$xmlWriter->writeElement('accounttypedescription', $account_type_description);
					
					// <parenttransactionreference></parenttransactionreference>
					$xmlWriter->writeElement('parenttransactionreference', $parent_transaction_reference);
					
				// </operation>
				$xmlWriter->endElement();
				
				// <settlement>
				$xmlWriter->startElement('settlement');
				
					// <settleduedate></settleduedate>
					$xmlWriter->writeElement('settleduedate', $settle_due_date);
					
					// <settlestatus></settlestatus>
					$xmlWriter->writeElement('settlestatus', $settle_status);
					
				// </settlement>
				$xmlWriter->endElement();
				
			// </request>
			$xmlWriter->endElement();
			
		// </requestblock>
		$xmlWriter->endElement();
	}
	
	private function format3dAuthEnrolled(XMLWriter &$xmlWriter, $vars)
	{
		extract($vars);
		
		// <requestblock>
		$xmlWriter->startElement('requestblock');
		$xmlWriter->startAttribute('version');
		$xmlWriter->text($api_version);
		$xmlWriter->endAttribute();
		
			// <alias></alias>
			$xmlWriter->writeElement('alias', $alias);
			
			// <requesttype>
			$xmlWriter->startElement('request');
			$xmlWriter->startAttribute('type');
			$xmlWriter->text('AUTH');
			$xmlWriter->endAttribute();
			
				// <operation>
				$xmlWriter->startElement('operation');
				
					// <md></md>
					$xmlWriter->writeElement('md', $md);
					
					// <pares></pares>
					$xmlWriter->writeElement('pares', $pares);
					
				// </operation>
				$xmlWriter->endElement();
				
			// </request>
			$xmlWriter->endElement();
			
		// </requestblock>
		$xmlWriter->endElement();
	}
					
	private function format3dAuthNotEnrolled(XMLWriter &$xmlWriter, $vars)
	{
		extract($vars);
		
		// <requestblock>
		$xmlWriter->startElement('requestblock');
		$xmlWriter->startAttribute('version');
		$xmlWriter->text($api_version);
		$xmlWriter->endAttribute();
		
			// <alias></alias>
			$xmlWriter->writeElement('alias', $alias);
			
			// <requesttype>
			$xmlWriter->startElement('request');
			$xmlWriter->startAttribute('type');
			$xmlWriter->text('AUTH');
			$xmlWriter->endAttribute();
			
				// <operation>
				$xmlWriter->startElement('operation');
				
					// <sitereference></sitereference>
					$xmlWriter->writeElement('sitereference', $site_reference);
					
					// <parenttransactionreference></parenttransactionreference>
					$xmlWriter->writeElement('parenttransactionreference', $parent_transaction_reference);
					
				// </operation>
				$xmlWriter->endElement();
				
			// </request>
			$xmlWriter->endElement();
			
		// </requestblock>
		$xmlWriter->endElement();
	}
	
	private function formatRefund(XMLWriter &$xmlWriter, $vars)
	{
		extract($vars);
		
		// <requestblock>
		$xmlWriter->startElement('requestblock');
		$xmlWriter->startAttribute('version');
		$xmlWriter->text($api_version);
		$xmlWriter->endAttribute();
		
			// <alias></alias>
			$xmlWriter->writeElement('alias', $alias);
			
			// <requesttype>
			$xmlWriter->startElement('request');
			$xmlWriter->startAttribute('type');
			$xmlWriter->text('REFUND');
			$xmlWriter->endAttribute();
			
				// <merchant>
				$xmlWriter->startElement('merchant');
				
					// <orderreference></orderreference>
					$xmlWriter->writeElement('orderreference', $order_reference);
					
				// </merchant>
				$xmlWriter->endElement();
				
				// <operation>
				$xmlWriter->startElement('operation');
				
					// <sitereference></sitereference>
					$xmlWriter->writeElement('sitereference', $site_reference);
					
					// <parenttransactionreference></parenttransactionreference>
					$xmlWriter->writeElement('parenttransactionreference', $parent_transaction_reference);
					
				// </operation>
				$xmlWriter->endElement();
				
				// <billing>
				$xmlWriter->startElement('billing');
				
					// <amount currencycode=''></amount>
					$xmlWriter->startElement('amount');
					$xmlWriter->startAttribute('currencycode');
					$xmlWriter->text($currency_code);
					$xmlWriter->endAttribute();
					$xmlWriter->text($amount);
					$xmlWriter->endElement();
					
				// </billing>
				$xmlWriter->endElement();
				
			// </request>
			$xmlWriter->endElement();
			
		// </requestblock>
		$xmlWriter->endElement();
	}
	
	/**
	 * Special case that processes a full or partial refund or cancels the transaction if it has not been settled.
	 * Settle status should always be '3' in the update since we want to cancel the transaction.
	 */
	private function formatUpdateRefund(XMLWriter &$xmlWriter, $vars)
	{
		extract($vars);
		
		// <requestblock>
		$xmlWriter->startElement('requestblock');
		$xmlWriter->startAttribute('version');
		$xmlWriter->text($api_version);
		$xmlWriter->endAttribute();
		
			// <alias></alias>
			$xmlWriter->writeElement('alias', $alias);
			
			// <request type='TRANSACTIONUPDATE'>
			$xmlWriter->startElement('request');
			$xmlWriter->startAttribute('type');
			$xmlWriter->text('TRANSACTIONUPDATE');
			$xmlWriter->endAttribute();
			
				// <filter>
				$xmlWriter->startElement('filter');
				
					// <sitereference></sitereference>
					$xmlWriter->writeElement('sitereference', $site_reference);
					
					// <transactionreference></transactionreference>
					$xmlWriter->writeElement('transactionreference', $transaction_reference);
					
				// </filter>
				$xmlWriter->endElement();
				
				// <updates>
				$xmlWriter->startElement('updates');
				
					// <settlement>
					$xmlWriter->startElement('settlement');
					
						// <settlestatus>3</settlestatus>
						$xmlWriter->writeElement('settlestatus', 3);
						
					// </settlement>
					$xmlWriter->endElement();
					
				// </updates>
				$xmlWriter->endElement();
				
			// </request>
			$xmlWriter->endElement();
			
			// <request type='REFUND'>
			$xmlWriter->startElement('request');
			$xmlWriter->startAttribute('type');
			$xmlWriter->text('REFUND');
			$xmlWriter->endAttribute();
			
				// <operation>
				$xmlWriter->startElement('operation');
			
					// <sitereference></sitereference>
					$xmlWriter->writeElement('sitereference', $site_reference);
					
				// </operation>
				$xmlWriter->endElement();
				
			// </request>
			$xmlWriter->endElement();
				
		// </requestblock>
		$xmlWriter->endElement();
	}
	
	/**
	 * Refunds part of the authorized amount; we do not want to change the settle status here.
	 */
	private function formatUpdatePartialRefund(XMLWriter &$xmlWriter, $vars)
	{
		extract($vars);
		
		// <requestblock>
		$xmlWriter->startElement('requestblock');
		$xmlWriter->startAttribute('version');
		$xmlWriter->text($api_version);
		$xmlWriter->endAttribute();
		
			// <alias></alias>
			$xmlWriter->writeElement('alias', $alias);
			
			// <request type='TRANSACTIONUPDATE'>
			$xmlWriter->startElement('request');
			$xmlWriter->startAttribute('type');
			$xmlWriter->text('TRANSACTIONUPDATE');
			$xmlWriter->endAttribute();
			
				// <filter>
				$xmlWriter->startElement('filter');
				
					// <sitereference></sitereference>
					$xmlWriter->writeElement('sitereference', $site_reference);
					
					// <transactionreference></transactionreference>
					$xmlWriter->writeElement('transactionreference', $transaction_reference);
				
				// </filter>
				$xmlWriter->endElement();
				
				// <updates>
				$xmlWriter->startElement('updates');
				
					// <settlement>
					$xmlWriter->startElement('settlement');
					
						// <settlebaseamount></settlebaseamount>
						$xmlWriter->writeElement('settlebaseamount', $settle_base_amount);
						
					// </settlement>
					$xmlWriter->endElement();
					
				// </updates>
				$xmlWriter->endElement();
				
			// </request>
			$xmlWriter->endElement();
			
			// <request type='REFUND'>
			$xmlWriter->startElement('request');
			$xmlWriter->startAttribute('type');
			$xmlWriter->text('REFUND');
			$xmlWriter->endAttribute();
			
				// <operation>
				$xmlWriter->startElement('operation');
				
					// <sitereference></sitereference>
					$xmlWriter->writeElement('sitereference', $site_reference);
					
				// </operation>
				$xmlWriter->endElement();
				
				// <billing>
				$xmlWriter->startElement('billing');
				
					// <amount></amount>
					$xmlWriter->writeElement('amount', $amount);
					
				// </billing>
				$xmlWriter->endElement();
				
			// </request>
			$xmlWriter->endElement();
			
		// </requestblock>
		$xmlWriter->endElement();
	}
	
	/**
	 * Format a CARDSTORE request.
	 */
	private function formatCardStore(XMLWriter &$xmlWriter, $vars)
	{
		extract($vars);
		
		// <requestblock>
		$xmlWriter->startElement('requestblock');
		$xmlWriter->startAttribute('version');
		$xmlWriter->text($api_version);
		$xmlWriter->endAttribute();
		
			// <alias></alias>
			$xmlWriter->writeElement('alias', $alias);
			
			// <request type='STORE'>
			$xmlWriter->startElement('request');
			$xmlWriter->startAttribute('type');
			$xmlWriter->text('STORE');
			$xmlWriter->endAttribute();
			
				// <merchant>
				$xmlWriter->startElement('merchant');
				
					// <orderreference></orderreference>
					$xmlWriter->writeElement('orderreference', $order_reference);
					
				// </merchant>
				$xmlWriter->endElement();
				
				// <operation>
				$xmlWriter->startElement('operation');
				
					// <sitereference></sitereference>
					$xmlWriter->writeElement('sitereference', $site_reference);
					
					// <accounttypedescription></accounttypedescription>
					$xmlWriter->writeElement('accounttypedescription', $account_type_description);
					
				// </operation>
				$xmlWriter->endElement();
				
				// <billing>
				$xmlWriter->startElement('billing');
			
					// <telephone type=''></telephone>
					$xmlWriter->startElement('telephone');
					$xmlWriter->startAttribute('type');
					$xmlWriter->text($billing_telephone_type);
					$xmlWriter->endAttribute();
					$xmlWriter->text($billing_telephone_number);
					$xmlWriter->endElement();
					
					// <street></street>
					$xmlWriter->writeElement('street', $billing_street);
					
					// <postcode></postcode>
					$xmlWriter->writeElement('postcode', $billing_postcode);
					
					// <premise></premise>
					$xmlWriter->writeElement('premise', $billing_premise);
					
					// <town></town>
					$xmlWriter->writeElement('town', $billing_town);
					
					// <country></country>
					$xmlWriter->writeElement('country', $billing_country);
					
					// <payment type=''>
					$xmlWriter->startElement('payment');
					$xmlWriter->startAttribute('type');
					$xmlWriter->text($payment_type);
					$xmlWriter->endAttribute();
						
						// <startdate></startdate>
						$xmlWriter->writeElement('startdate', $start_date);
						
						// <expirydate></expirydate>
						$xmlWriter->writeElement('expirydate', $expiry_date);
						
						// <pan></pan>
						$xmlWriter->writeElement('pan', $pan);
						
						// <securitycode></securitycode>
						$xmlWriter->writeElement('securitycode', $security_code);
						
						// <issuenumber></issuenumber>
						$xmlWriter->writeElement('issuenumber', $issue_number);
					
					// </payment>
					$xmlWriter->endElement();
					
					// <name>
					$xmlWriter->startElement('name');
						
						// <middle></middle>
						$xmlWriter->writeElement('middle', $billing_middle_name);
						
						// <prefix></prefix>
						$xmlWriter->writeElement('prefix', $billing_prefix);
						
						// <last></last>
						$xmlWriter->writeElement('last', $billing_last_name);
						
						// <first></first>
						$xmlWriter->writeElement('first', $billing_first_name);
						
						// <suffix></suffix>
						$xmlWriter->writeElement('suffix', $billing_suffix);
					
					// </name>
					$xmlWriter->endElement();
					
					// <email></email>
					$xmlWriter->writeElement('email', $billing_email);
					
				// </billing>
				$xmlWriter->endElement();
			
			// </request>
			$xmlWriter->endElement();
				
		// </requestblock>
		$xmlWriter->endElement();
	}
	
	/**
	 * Format a TRANSACTIONUPDATE request.
	 */
	private function formatTransactionUpdate(XMLWriter &$xmlWriter, $vars)
	{
		extract($vars);
		
		// <requestblock>
		$xmlWriter->startElement('requestblock');
		$xmlWriter->startAttribute('version');
		$xmlWriter->text($api_version);
		$xmlWriter->endAttribute();
		
			// <alias></alias>
			$xmlWriter->writeElement('alias', $alias);
			
			// <request type='TRANSACTIONUPDATE'>
			$xmlWriter->startElement('request');
			$xmlWriter->startAttribute('type');
			$xmlWriter->text('TRANSACTIONUPDATE');
			$xmlWriter->endAttribute();
			
				// <filter>
				$xmlWriter->startElement('filter');
				
					// <sitereference></sitereference>
					$xmlWriter->writeElement('sitereference', $site_reference);
					
					// <transactionreference></transactionreference>
					$xmlWriter->writeElement('transactionreference', $transaction_reference);
				
				// </filter>
				$xmlWriter->endElement();
				
				// <updates>
				$xmlWriter->startElement('updates');
			
				// TODO - all things here...
			
				// </updates>
				$xmlWriter->endElement();
				
			$xmlWriter->endElement();
			// </request>
			
		// </requestblock>
		$xmlWriter->endElement();
	}
	
	private function formatRiskDecision(XMLWriter &$xmlWriter, $vars)
	{
		extract($vars);
		
		$xmlWriter->startElement('requestblock');
		$xmlWriter->startAttribute('version');
		$xmlWriter->text($api_version);
		$xmlWriter->endAttribute();
		
			// <alias>
			$xmlWriter->writeElement('alias', $alias);
			
			// <request>
			$xmlWriter->startElement('request');
			$xmlWriter->startAttribute('type');
			$xmlWriter->text('RISKDEC');
			$xmlWriter->endAttribute();
			
				// <merchant>
				$xmlWriter->startElement('merchant');
				
					// <orderreference></orderreference>
					$xmlWriter->writeElement('orderreference', $order_reference);
					
				// </merchant>
				$xmlWriter->endElement();
				
				// <customer>
				$xmlWriter->startElement('customer');
					
					// <ip></ip>
					$xmlWriter->writeElement('ip', $ip);
					
					// <telephone type=''></telephone>
					$xmlWriter->startElement('telephone');
					$xmlWriter->startAttribute('type');
					$xmlWriter->text($customer_telephone_type);
					$xmlWriter->endAttribute();
					$xmlWriter->text($customer_telephone_number);
					$xmlWriter->endElement();
					
					// <street></street>
					$xmlWriter->writeElement('street', $customer_street);
					
					// <postcode></postcode>
					$xmlWriter->writeElement('postcode', $customer_postcode);
					
					// <premise></premise>
					$xmlWriter->writeElement('premise', $customer_premise);
					
					// <town></town>
					$xmlWriter->writeElement('town', $customer_town);
					
					// <country></country>
					$xmlWriter->writeElement('country', $customer_country);
					
					// <name>
					$xmlWriter->startElement('name');
						
						// <middle></middle>
						$xmlWriter->writeElement('middle', $customer_middle_name);
						
						// <prefix></prefix>
						$xmlWriter->writeElement('prefix', $customer_prefix);
						
						// <last></last>
						$xmlWriter->writeElement('last', $customer_last_name);
						
						// <first></first>
						$xmlWriter->writeElement('first', $customer_first_name);
						
						// <suffix></suffix>
						$xmlWriter->writeElement('suffix', $customer_suffix);
					
					// </name>
					$xmlWriter->endElement();
					
					// <email></email>
					$xmlWriter->writeElement('email', $customer_email);
					
				// </customer>
				$xmlWriter->endElement();
				
				// <billing>
				$xmlWriter->startElement('billing');

					// <telephone type=''></telephone>
					$xmlWriter->startElement('telephone');
					$xmlWriter->startAttribute('type');
					$xmlWriter->text($billing_telephone_type);
					$xmlWriter->endAttribute();
					$xmlWriter->text($billing_telephone_number);
					$xmlWriter->endElement();
					
					// <street></street>
					$xmlWriter->writeElement('street', $billing_street);
					
					// <postcode></postcode>
					$xmlWriter->writeElement('postcode', $billing_postcode);
					
					// <premise></premise>
					$xmlWriter->writeElement('premise', $billing_premise);
					
					// <town></town>
					$xmlWriter->writeElement('town', $billing_town);
					
					// <country></country>
					$xmlWriter->writeElement('country', $billing_country);
					
					// <payment type=''>
					$xmlWriter->startElement('payment');
					$xmlWriter->startAttribute('type');
					$xmlWriter->text($payment_type);
					$xmlWriter->endAttribute();
						
						// <startdate></startdate>
						$xmlWriter->writeElement('startdate', $start_date);
						
						// <expirydate></expirydate>
						$xmlWriter->writeElement('expirydate', $expiry_date);
						
						// <pan></pan>
						$xmlWriter->writeElement('pan', $pan);
						
						// <securitycode></securitycode>
						$xmlWriter->writeElement('securitycode', $security_code);
						
						// <issuenumber></issuenumber>
						$xmlWriter->writeElement('issuenumber', $issue_number);
					
					// </payment>
					$xmlWriter->endElement();
					
					// <name>
					$xmlWriter->startElement('name');
						
						// <middle></middle>
						$xmlWriter->writeElement('middle', $billing_middle_name);
						
						// <prefix></prefix>
						$xmlWriter->writeElement('prefix', $billing_prefix);
						
						// <last></last>
						$xmlWriter->writeElement('last', $billing_last_name);
						
						// <first></first>
						$xmlWriter->writeElement('first', $billing_first_name);
						
						// <suffix></suffix>
						$xmlWriter->writeElement('suffix', $billing_suffix);
					
					// </name>
					$xmlWriter->endElement();
					
					// <amount currencycode=''></amount>
					$xmlWriter->startElement('amount');
					$xmlWriter->startAttribute('currencycode');
					$xmlWriter->text($currency_code);
					$xmlWriter->endAttribute();
					$xmlWriter->text($amount);
					$xmlWriter->endElement();
					
					// <email></email>
					$xmlWriter->writeElement('email', $billing_email);
					
				// </billing>
				$xmlWriter->endElement();
				
				// <operation>
				$xmlWriter->startElement('operation');
					
					// <sitereference></sitereference>
					$xmlWriter->writeElement('sitereference', $site_reference);
					
					// <accounttypedescription></accounttypedescription>
					$xmlWriter->writeElement('accounttypedescription', 'FRAUDCONTROL');
					
				// </operation>
				$xmlWriter->endElement();

			// </request>
			$xmlWriter->endElement();
			
		// </requestblock>
		$xmlWriter->endElement();
	}
	
	private function formatRiskDecisionAndAuthOr3dQuery(XMLWriter &$xmlWriter, $vars, $use3dSecure)
	{
		$requestType = $use3dSecure ? 'THREEDQUERY' : 'AUTH';
		
		extract($vars);
		
		$xmlWriter->startElement('requestblock');
		$xmlWriter->startAttribute('version');
		$xmlWriter->text($api_version);
		$xmlWriter->endAttribute();
		
			// <alias>
			$xmlWriter->writeElement('alias', $alias);
			
			// <request>
			$xmlWriter->startElement('request');
			$xmlWriter->startAttribute('type');
			$xmlWriter->text('RISKDEC');
			$xmlWriter->endAttribute();
			
				// <merchant>
				$xmlWriter->startElement('merchant');
				
					// <orderreference></orderreference>
					$xmlWriter->writeElement('orderreference', $order_reference);
					
				// </merchant>
				$xmlWriter->endElement();
				
				// <customer>
				$xmlWriter->startElement('customer');
					
					// <ip></ip>
					$xmlWriter->writeElement('ip', $ip);
					
					// <telephone type=''></telephone>
					$xmlWriter->startElement('telephone');
					$xmlWriter->startAttribute('type');
					$xmlWriter->text($customer_telephone_type);
					$xmlWriter->endAttribute();
					$xmlWriter->text($customer_telephone_number);
					$xmlWriter->endElement();
					
					// <street></street>
					$xmlWriter->writeElement('street', $customer_street);
					
					// <postcode></postcode>
					$xmlWriter->writeElement('postcode', $customer_postcode);
					
					// <premise></premise>
					$xmlWriter->writeElement('premise', $customer_premise);
					
					// <town></town>
					$xmlWriter->writeElement('town', $customer_town);
					
					// <country></country>
					$xmlWriter->writeElement('country', $customer_country);
					
					// <name>
					$xmlWriter->startElement('name');
						
						// <middle></middle>
						$xmlWriter->writeElement('middle', $customer_middle_name);
						
						// <prefix></prefix>
						$xmlWriter->writeElement('prefix', $customer_prefix);
						
						// <last></last>
						$xmlWriter->writeElement('last', $customer_last_name);
						
						// <first></first>
						$xmlWriter->writeElement('first', $customer_first_name);
						
						// <suffix></suffix>
						$xmlWriter->writeElement('suffix', $customer_suffix);
					
					// </name>
					$xmlWriter->endElement();
					
					// <email></email>
					$xmlWriter->writeElement('email', $customer_email);
					
				// </customer>
				$xmlWriter->endElement();
				
				// <billing>
				$xmlWriter->startElement('billing');

					// <telephone type=''></telephone>
					$xmlWriter->startElement('telephone');
					$xmlWriter->startAttribute('type');
					$xmlWriter->text($billing_telephone_type);
					$xmlWriter->endAttribute();
					$xmlWriter->text($billing_telephone_number);
					$xmlWriter->endElement();
					
					// <street></street>
					$xmlWriter->writeElement('street', $billing_street);
					
					// <postcode></postcode>
					$xmlWriter->writeElement('postcode', $billing_postcode);
					
					// <premise></premise>
					$xmlWriter->writeElement('premise', $billing_premise);
					
					// <town></town>
					$xmlWriter->writeElement('town', $billing_town);
					
					// <country></country>
					$xmlWriter->writeElement('country', $billing_country);
					
					// <payment type=''>
					$xmlWriter->startElement('payment');
					$xmlWriter->startAttribute('type');
					$xmlWriter->text($payment_type);
					$xmlWriter->endAttribute();
						
						// <startdate></startdate>
						$xmlWriter->writeElement('startdate', $start_date);
						
						// <expirydate></expirydate>
						$xmlWriter->writeElement('expirydate', $expiry_date);
						
						// <pan></pan>
						$xmlWriter->writeElement('pan', $pan);
						
						// <securitycode></securitycode>
						$xmlWriter->writeElement('securitycode', $security_code);
						
						// <issuenumber></issuenumber>
						$xmlWriter->writeElement('issuenumber', $issue_number);
					
					// </payment>
					$xmlWriter->endElement();
					
					// <name>
					$xmlWriter->startElement('name');
						
						// <middle></middle>
						$xmlWriter->writeElement('middle', $billing_middle_name);
						
						// <prefix></prefix>
						$xmlWriter->writeElement('prefix', $billing_prefix);
						
						// <last></last>
						$xmlWriter->writeElement('last', $billing_last_name);
						
						// <first></first>
						$xmlWriter->writeElement('first', $billing_first_name);
						
						// <suffix></suffix>
						$xmlWriter->writeElement('suffix', $billing_suffix);
					
					// </name>
					$xmlWriter->endElement();
					
					// <amount currencycode=''></amount>
					$xmlWriter->startElement('amount');
					$xmlWriter->startAttribute('currencycode');
					$xmlWriter->text($currency_code);
					$xmlWriter->endAttribute();
					$xmlWriter->text($amount);
					$xmlWriter->endElement();
					
					// <email></email>
					$xmlWriter->writeElement('email', $billing_email);
					
				// </billing>
				$xmlWriter->endElement();
				
				// <operation>
				$xmlWriter->startElement('operation');
					
					// <sitereference></sitereference>
					$xmlWriter->writeElement('sitereference', $site_reference);
					
					// <accounttypedescription></accounttypedescription>
					$xmlWriter->writeElement('accounttypedescription', 'FRAUDCONTROL');
					
				// </operation>
				$xmlWriter->endElement();

			// </request>
			$xmlWriter->endElement();
			
			// <request>
			$xmlWriter->startElement('request');
			$xmlWriter->startAttribute('type');
			$xmlWriter->text($requestType);
			$xmlWriter->endAttribute();
			
				if ($use3dSecure) {
					// <merchant>
					$xmlWriter->startElement('merchant');
				
						// <termurl></termurl>
						$xmlWriter->writeElement('termurl', $term_url);
						
					// </merchant>
					$xmlWriter->endElement();
				}

				// <billing>
				$xmlWriter->startElement('billing');

					// <payment type=''>
					$xmlWriter->startElement('payment');
					$xmlWriter->startAttribute('type');
					$xmlWriter->text($payment_type);
					$xmlWriter->endAttribute();
						
						// <securitycode></securitycode>
						$xmlWriter->writeElement('securitycode', $security_code);
						
					// </payment>
					$xmlWriter->endElement();
					
				// </billing>
				$xmlWriter->endElement();
				
				// <operation>
				$xmlWriter->startElement('operation');
					
					// <accounttypedescription></accounttypedescription>
					$xmlWriter->writeElement('accounttypedescription', "ECOM");
					
				// </operation>
				$xmlWriter->endElement();
					
			// </request>
			$xmlWriter->endElement();
			
		// </requestblock>
		$xmlWriter->endElement();
	}
	
	function buildResponseObject($xmlResponses)
	{
		$children = $xmlResponses->xpath('response');
		
		if (count($children) == 2) { // Special cases: If we receive two response objects.
			$child1 = (string) $children[0]->attributes()->type;
			$child2 = (string) $children[1]->attributes()->type;
			
			// Allowed ombinations:
			// TRANSACTIONUPDATE, ERRROR			TRANSACTIONUPDATE, REFUND
			// RISKDEC, THREEDQUERY					RISKDEC, AUTH
			// RISKDEC, ERROR						ERROR, AUTH
			// ERROR, THREEDQUERY
			
			$error = TRUE;
			
			if ($child1 === 'TRANSACTIONUPDATE' || in_array($child2, array('ERROR', 'REFUND'))) {
				$error = FALSE;
			}
			elseif ($child1 === 'RISKDEC' && in_array($child2, array('THREEDQUERY', 'AUTH', 'ERROR'))) {
				$error = FALSE;
			}
			elseif ($child1 === 'ERROR' && in_array($child2, array('AUTH', 'THREEDQUERY'))) {
				$error = FALSE;
			}
			
			if ($error) {
				throw new Exception(sprintf($this->languageVars['two_invalid_responses'], $child1, $child2));
			}
			
			$xmlResponse = new stdClass;
			$xmlResponse->response = $children[1]; // Ignore first response object and work only with the second.
		}
		else {
			$xmlResponse = new stdClass;
			$xmlResponse->response = $children[0];
		}
		
		$ro = new stdClass;
		$ro->requestreference = ((string) $xmlResponses->requestreference) ? (string) $xmlResponses->requestreference : '';
		
		switch($xmlResponse->response->attributes()->type) {
			case "THREEDQUERY":	
										$ro->type = "THREEDQUERY";
										$ro->merchantname = (string) $xmlResponse->response->merchant->merchantname;
										$ro->orderreference = (string) $xmlResponse->response->merchant->orderreference;
										$ro->tid = (string) $xmlResponse->response->merchant->tid;
										$ro->merchantnumber = (string) $xmlResponse->response->merchant->merchantnumber;
										$ro->merchantcountryiso2a = (string) $xmlResponse->response->merchant->merchantcountryiso2a;
										$ro->transactionreference = (string) $xmlResponse->response->transactionreference;
										$ro->cardtype = (string) $xmlResponse->response->billing->payment->attributes()->type;
										$ro->maskedpan = (string) $xmlResponse->response->billing->payment->pan;
										$ro->timestamp = (string) $xmlResponse->response->timestamp;
										$ro->acsurl = (string) $xmlResponse->response->threedsecure->acsurl;
										$ro->md = (string) $xmlResponse->response->threedsecure->md;
										$ro->xid = (string) $xmlResponse->response->threedsecure->xid;
										$ro->pareq = (string) $xmlResponse->response->threedsecure->pareq;
										$ro->enrolled = (string) $xmlResponse->response->threedsecure->enrolled;
										$ro->live = (string) $xmlResponse->response->live;
										$ro->errorcode = (string) $xmlResponse->response->error->code;
										$ro->errormessage = (string) $xmlResponse->response->error->message;
										$ro->accounttypedescription = (string) $xmlResponse->response->operation->accounttypedescription;
										$ro->settleduedate = (string) $xmlResponse->response->settlement->settleduedate;
										$ro->settlestatus = (string) $xmlResponse->response->settlement->settlestatus;
										break;
			case "AUTH":		
										$ro->type = "AUTH";
										$ro->merchantname = (string) $xmlResponse->response->merchant->merchantname;
										$ro->orderreference = (string) $xmlResponse->response->merchant->orderreference;
										$ro->tid = (string) $xmlResponse->response->merchant->tid;
										$ro->merchantnumber = (string) $xmlResponse->response->merchant->merchantnumber;
										$ro->merchantcountryiso2a = (string) $xmlResponse->response->merchant->merchantcountryiso2a;
										$ro->transactionreference = (string) $xmlResponse->response->transactionreference;
										$ro->securitycode = (string) $xmlResponse->response->security->securitycode;
										$ro->securitypostcode = (string) $xmlResponse->response->security->postcode;
										$ro->securityaddress = (string) $xmlResponse->response->security->address;
										$ro->amount = (string) $xmlResponse->response->billing->amount;
										$ro->currencycode = (string) $xmlResponse->response->billing->amount->attributes()->currencycode; ##
										$ro->cardtype = (string) $xmlResponse->response->billing->payment->attributes()->type;
										$ro->maskedpan = (string) $xmlResponse->response->billing->payment->pan;
										$ro->authcode = (string) $xmlResponse->response->authcode;
										$ro->timestamp = (string) $xmlResponse->response->timestamp;
										$ro->threedcavv = (string) $xmlResponse->response->threedsecure->cavv;
										$ro->threedstatus = (string) $xmlResponse->response->threedsecure->status;
										$ro->threedxid = (string) $xmlResponse->response->threedsecure->xid;
										$ro->threedeci = (string) $xmlResponse->response->threedsecure->eci;
										$ro->enrolled = (string) $xmlResponse->response->threedsecure->enrolled;
										$ro->live = (string) $xmlResponse->response->live;
										$ro->errorcode = (string) $xmlResponse->response->error->code;
										$ro->errormessage = (string) $xmlResponse->response->error->message;
										$ro->parenttransactionreference = (string) $xmlResponse->response->operation->parenttransactionreference;
										$ro->accounttypedescription = (string) $xmlResponse->response->operation->accounttypedescription;
										$ro->settleduedate = (string) $xmlResponse->response->settlement->settleduedate;
										$ro->settlestatus = (string) $xmlResponse->response->settlement->settlestatus;
										break;		
			case "ERROR":		
										$ro->type = "ERROR";
										$ro->timestamp = (string) $xmlResponse->response->timestamp;
										$ro->transactionreference = (string) $xmlResponse->response->transactionreference;
										$ro->errorcode = (string) $xmlResponse->response->error->code;
										$ro->errormessage = (string) $xmlResponse->response->error->message;
										$ro->errordata = (string) $xmlResponse->response->error->data;
										break;		
			case "REFUND":		
										$ro->type="REFUND";
										$ro->merchantname = (string) $xmlResponse->response->merchant->merchantname;
										$ro->orderreference = (string) $xmlResponse->response->merchant->orderreference;
										$ro->tid = (string) $xmlResponse->response->merchant->tid;
										$ro->merchantnumber = (string) $xmlResponse->response->merchant->merchantnumber;
										$ro->merchantcountryiso2a = (string) $xmlResponse->response->merchant->merchantcountryiso2a;
										$ro->transactionreference = (string) $xmlResponse->response->transactionreference;
										$ro->amount = (string) $xmlResponse->response->billing->amount;
										$ro->currencycode = (string) $xmlResponse->response->billing->amount->attributes()->currencycode;
										$ro->cardtype = (string) $xmlResponse->response->billing->payment->attributes()->type;
										$ro->maskedpan = (string) $xmlResponse->response->billing->payment->pan;
										$ro->authcode = (string) $xmlResponse->response->authcode;
										$ro->timestamp = (string) $xmlResponse->response->timestamp;	
										$ro->securitycode = (string) $xmlResponse->response->security->securitycode;
										$ro->securitypostcode = (string) $xmlResponse->response->security->postcode;
										$ro->securityaddress = (string) $xmlResponse->response->security->address;
										$ro->parenttransactionreference = (string) $xmlResponse->response->operation->parenttransactionreference;
										$ro->accounttypedescription = (string) $xmlResponse->response->operation->accounttypedescription;
										$ro->settleduedate = (string) $xmlResponse->response->settlement->settleduedate;
										$ro->settlestatus = (string) $xmlResponse->response->settlement->settlestatus;
										break;
		case "STORE":
										$ro->type = "STORE";
										$ro->merchantname = (string) $xmlResponse->response->merchant->merchantname;
										$ro->orderreference = (string) $xmlResponse->response->merchant->orderreference;
										$ro->transactionreference = (string) $xmlResponse->response->transactionreference;
										$ro->paymenttype = (string) $xmlResponse->response->billing->payment->attributes()->type;
										$ro->active = (string) $xmlResponse->response->billing->payment->active;
										$ro->pan = (string) $xmlResponse->response->billing->payment->pan;
										$ro->timestamp = (string) $xmlResponse->response->timestamp;
										$ro->live = (string) $xmlResponse->response->live;
										$ro->errorcode = (string) $xmlResponse->response->error->code;
										$ro->errormessage = (string) $xmlResponse->response->error->message;
										$ro->errordata = (string) $xmlResponse->response->error->data;
										$ro->accounttypedescription = (string) $xmlResponse->response->operation->accounttypedescription;
										break;
			case "TRANSACTIONUPDATE":
										$ro->timestamp = (string) $xmlResponse->response->timestamp;
										$ro->errorcode = (string) $xmlResponse->response->error->code;
										$ro->errormessage = (string) $xmlResponse->response->error->message;
										$ro->errordata = (string) $xmlResponse->response->error->data;
										break;
			case "RISKDEC":
										$ro->type = "RISKDEC";
										$ro->orderreference = (string) $xmlResponse->response->merchant->orderreference;
										$ro->transactionreference = (string) $xmlResponse->response->transactionreference;
										$ro->maskedpan = (string) $xmlResponse->response->billing->payment->pan;
										$ro->timestamp = (string) $xmlResponse->response->timestamp;
										$ro->shieldstatuscode = (string) $xmlResponse->response->fraudcontrol->shieldstatuscode;
										$ro->reference = (string) $xmlResponse->response->fraudcontrol->reference;
										$ro->recommendedaction = (string) $xmlResponse->response->fraudcontrol->recommendedaction;
										$ro->categoryflag = (string) $xmlResponse->response->fraudcontrol->categoryflag;
										$ro->categorymessage = (string) $xmlResponse->response->fraudcontrol->categorymessage;
										$ro->responsecode = (string) $xmlResponse->response->fraudcontrol->responsecode;
										$ro->live = (string) $xmlResponse->response->live;
										$ro->errorcode = (string) $xmlResponse->response->error->code;
										$ro->errormessage = (string) $xmlResponse->response->error->message;
										$ro->errordata = (string) $xmlResponse->response->error->data;
										$ro->parenttransactionreference = (string) $xmlResponse->response->operation->parenttransactionreference;
										$ro->accounttypedescription = (string) $xmlResponse->response->operation->accounttypedescription;
										break;
		}
		return $ro;
	}
}