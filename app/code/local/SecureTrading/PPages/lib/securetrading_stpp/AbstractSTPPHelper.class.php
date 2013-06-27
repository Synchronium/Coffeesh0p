<?php

/**
 * SecureTrading STPP Shopping Carts
 * STPP Cart Interface Version 1.3.7
 * Last updated 22/03/2013
 * Written by Peter Barrow for SecureTrading Ltd.
 * http://www.securetrading.com
 */
 
abstract class AbstractSTPPHelper
{
	protected $cartObject;
	
	public function __construct(AbstractSTPP $object) {
		$this->cartObject = $object;
	}
	
	abstract public function retrieveCartVersion();
}