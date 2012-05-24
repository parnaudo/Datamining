<?php

class Zephyr_Helper_Name_Variation_Six extends Zephyr_Helper_Name_Variation_Abstract
{
	protected $_expectedParts = 6;
	
	public function process()
	{
		preg_match('~(.+? .+?) (.+? .+?) (.+? .+)~i', $this->_name, $matches);
		
		$firstName		= $matches[1];
		$middleName		= $matches[2];
		$lastName		= $matches[3];
		
		$this->_item->setFirstName($firstName);
		$this->_item->setMiddleName($middleName);
		$this->_item->setLastName($lastName);
		
		return;
	}
}