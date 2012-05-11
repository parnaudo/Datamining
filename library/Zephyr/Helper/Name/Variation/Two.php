<?php

class Zephyr_Helper_Name_Variation_Two extends Zephyr_Helper_Name_Variation_Abstract
{
	protected $_expectedParts = 2;
	
	public function process()
	{
		list($firstName, $lastName) = $this->_breakUp();
		
		$this->_item->setFirstName($firstName);
		$this->_item->setLastName($lastName);
	}
}