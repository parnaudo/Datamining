<?php

class Zephyr_Helper_Name_Variation_Exception_LatinSurname extends Zephyr_Helper_Name_Variation_Abstract
{
	public function process()
	{
		if (preg_match('~(.+? .+?) (.+)~i', $this->_name, $matches))
		{
			$firstName 	= $matches[1];
			$lastName	= $matches[2];
			
			$this->_item->setFirstName($firstName);
			$this->_item->setLastName($lastName);
			
			return;
		}
		
		list ($firstName, $lastName) = explode(' ', $this->_name);
		
		$this->_item->setFirstName($firstName);
		$this->_item->setLastName($lastName);
	}
	
	public function isAppropriate()
	{
		$parts		= $this->_breakUp();
		$lastName	= array_pop($parts);
		
		$name		= Zephyr_Helper_Name_Census::getInstance()->find($lastName);
		
		if (!$name)
		{
			return false;
		}
		
		return (bool) ($name->percentHispanic > 90);
	}
}