<?php

class Zephyr_Helper_Name_Variation_Exception_SpanishLetter extends Zephyr_Helper_Name_Variation_Abstract
{
	protected $_padAttributes 		= false;
	protected $_characterRight		= null;
	protected $_expectedAttributes 	= array('á', 'é', 'í', 'ó', 'ú');
	
	public function process()
	{
		if (preg_match('~(.+?) (.+? .+)~i', $this->_name, $matches))
		{
			$firstName 	= $matches[1];
			$lastName	= $matches[2];
			
			$this->_item->setFirstName($firstName);
			$this->_item->setLastName($lastName);
			
			return;
		}
		
		if (preg_match('~(.+?) (.+)~i', $this->_name, $matches))
		{
			$firstName 	= $matches[1];
			$lastName	= $matches[2];
			
			$this->_item->setFirstName($firstName);
			$this->_item->setLastName($lastName);
			
			return;
		}
	}
}