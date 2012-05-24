<?php

class Zephyr_Helper_Name_Variation_Exception_SpanishSuffix extends Zephyr_Helper_Name_Variation_Abstract
{
	protected $_padAttributes 		= false;
	protected $_characterRight		= '$';
	protected $_expectedAttributes 	= array
	(
		'cia', 'nez', 'ez', 'era', 'res', 'az', 'les', 'yes', 'mos', 'iz', 'vez', 'llo', 'lla', 'gas', 'ero',
		'za', 'dez', 'eno', 'ena', 'era', 'ago', 'zar', 'lar', 'ega', 'los'
	);
	
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
	}
}