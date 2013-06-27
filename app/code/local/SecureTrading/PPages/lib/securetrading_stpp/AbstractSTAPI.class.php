<?php

/**
 * SecureTrading STPP Shopping Carts
 * STPP Cart Interface Version 1.3.7
 * Last updated 22/03/2013
 * Written by Peter Barrow for SecureTrading Ltd.
 * http://www.securetrading.com
 */
 
class STAPIException extends Exception { }

abstract class AbstractSTAPI extends AbstractAPI
{
	protected $host;
	
	protected $port;
	
	public function __construct() {
		return parent::__construct();
	}
	
	public function setConnection($host, $port) {
		$this->host = $host;
		$this->port = $port;
	}
	
	protected function sendAndReceiveData($xml_string)
	{
		try {
			$host = $this->host;
			$port = $this->port;
			
			if (!isset($host) || !isset($port)) {
			    throw new Exception(sprintf($this->languageVars['nohostorport'], $host, $port));
			}
			
			if (($socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === FALSE) {
				throw new Exception($this->languageVars['sockerr'] . socket_strerror(socket_last_error()));
			}
			
			if ($result = @socket_connect($socket, $this->host, $this->port) === FALSE) {
				throw new Exception($this->languageVars['sockerr'] . socket_strerror(socket_last_error()));
			}
			
			if (@socket_write($socket,$xml_string) === FALSE) {
				throw new Exception($this->languageVars['sockerr'] . socket_strerror(socket_last_error()));
			}

			$responsexml = '';
			
			while ($buffer = @socket_read($socket, 2048)) {
			
				if ($buffer === FALSE) {
					throw new Exception(socket_strerror(socket_last_error()));
				}
				
				$responsexml .= $buffer;
			}
			
			socket_close($socket);
			
			return simplexml_load_string($responsexml);
		}
		catch (Exception $e) {
			$this->createException($e, __FILE__, __CLASS__, __LINE__);
			throw new STAPIException($e); // Rethrow exception so it is caught by AbstractAPI class.
		}
	}
}