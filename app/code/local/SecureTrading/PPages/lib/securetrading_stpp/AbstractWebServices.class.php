<?php

/**
 * SecureTrading STPP Shopping Carts
 * STPP Cart Interface Version 1.3.7
 * Last updated 22/03/2013
 * Written by Peter Barrow for SecureTrading Ltd.
 * http://www.securetrading.com
 */
 
class WebServicesException extends Exception { }

abstract class AbstractWebServices extends AbstractAPI
{
	protected $username;
	
	protected $password;
	
	protected $actionUrl = 'https://webservices.securetrading.net:443/xml/';
	
	public function __construct() {
		return parent::__construct();
	}
	
	public function setAuthentication($username, $password) {
		$this->username = $username;
		$this->password = $password;
	}
	
	protected function sendAndReceiveData($xml_string)
	{
		try {
			$username = $this->username;
			$password = $this->password;
			
			if (empty($username) || empty($password)) {
			    throw new WebServicesException(sprintf($this->languageVars['nousernameorpassword'], $username, $password));
			}
			
			$xml_string = preg_replace('/>[\s]+</', '><', $xml_string); // Remove all whitespace between elements.
			$xml_string = trim($xml_string);
			
			$contentLength = strlen($xml_string);
			$authHeader = base64_encode($username . ':' . $password);
			$actionUrl = $this->actionUrl;
			
			$url = parse_url($this->actionUrl);
			$host = $url['host'];
			$path = $url['path'];
			
			$fp = fsockopen("ssl://".$host, 443, $errno, $errstr, 10);
			
			set_time_limit(60);
			stream_set_timeout($fp, 10);
			
			if (!$fp) {
				throw new WebServicesException(sprintf($this->languageVars['err-ssl-socket'], $errstr, $errno));
			}
			
			fwrite($fp, "POST {$path} HTTP/1.1\r\n");
			fwrite($fp, "Host: {$host}\r\n");
			fwrite($fp, "Content-Type: text/xml;charset=utf-8\r\n");
			fwrite($fp, "Content-Length: {$contentLength}\r\n");
			fwrite($fp, "Authorization: Basic {$authHeader}\r\n");
			fwrite($fp, "Accept: text/cml\r\n");
			fwrite($fp, "Connection: close\r\n\r\n");
			fwrite($fp, $xml_string);
			
			$response = '';
			
			while ($line = fgets($fp)) {
				$response .= $line;
			}
			
			$bool = preg_match("!HTTP/1\.. (\d{3})!", $response, $match);
			
			if (!$bool) {
				throw new WebServicesException($this->languageVars['err-ws-no-http-code']);
			}
			
			$httpResponse = $match[1];
			
			// Check the HTTP response code:
			if ($httpResponse != 200) {
				$message = isset($this->languageVars['err-ws-' . $httpResponse]) ? $this->languageVars['err-ws-' . $httpResponse] : "Error: HTTP Response {$httpResponse} returned.";
				throw new WebServicesException($message);
			}
			
			$response = substr($response, strpos($response, '<')); // Remove headers from response.  Left with just XML.
			
			$xml = simplexml_load_string($response);
			
			if ($xml === FALSE) {
				throw new WebServicesException($this->languageVars['response_not_xml'] . print_r($response, 1));
			}
			
			return $xml;
		}
		catch (Exception $e) {
			$this->createException($e, __FILE__, __CLASS__, __LINE__);
			throw new WebServicesException($e); // Rethrow exception so it is caught by AbstractAPI class.
		}
	}
}