<?php

class Zephyr_Helper_Name_Variation_Exception_Connective extends Zephyr_Helper_Name_Variation_Abstract
{
	protected $_padAttributes 		= true;
	protected $_characterRight		= null;
	protected $_expectedAttributes 	= array('de las', 'van der', 'von der', 'de los', 'de la', 'de', 'da', 'del', 'di', 'du', 'van', 'von', 'dela', 'li');
	
	public function process()
	{
		$regExp = sprintf('~^(.+?) ((%s) (.+))~i', implode('|', $this->_expectedAttributes));
		
		if (preg_match($regExp, $this->_name, $matches))
		{
			$message = sprintf('"%1$s" has the connective "%2$s" and is therefore a Spanish/Portuguese/Italian/Dutch name.', $this->_name, $matches[3]);
			Zephyr_Helper_Name_Abstract::addAssumption($message);
			
			$this->_item->setFirstName($matches[1]);
			$this->_item->setLastName($matches[2]);
			
			return;
		}
		
		$regExp = sprintf('~^(.+? .+?) ((%s) (.+))~i', implode('|', $this->_expectedAttributes));
		
		if (preg_match($regExp, $this->_name, $matches))
		{
			$message = sprintf('"%1$s" has the connective "%2$s" and is therefore a Spanish/Portuguese/Italian/Dutch name.', $this->_name, $matches[3]);
			Zephyr_Helper_Name_Abstract::addAssumption($message);
			
			$this->_item->setFirstName($matches[1]);
			$this->_item->setLastName($matches[2]);
			
			return;
		}
	}
}