<?php

class Zephyr_Helper_Name_Variation_Three extends Zephyr_Helper_Name_Variation_Abstract
{
	protected $_expectedParts = 3;
	
	public function __construct($name, Zephyr_Helper_Name_Item_Name $item)
	{
		$this->_name 	= $name;
		$this->_item	= $item;
	}
	
	public function process()
	{
		# Handles things like: "J. W. Bolton"
		if (preg_match('~^([A-Z]{1})[\.|\s]+([A-Z]{1})[\.|\s]+(.+)~', $this->_name, $matches))
		{
			$firstName	= $matches[1];
			$middleName	= $matches[2];
			$lastName	= $matches[3];
			
			$this->_item->setFirstName($firstName, true);
			$this->_item->setMiddleName($middleName);
			$this->_item->setLastName($lastName);
			
			return;
		}
		
		# Handles things like: "T. Prescott Isaac Atkinson"
		if (preg_match('~$([A-Z]{1}(\.)?) (.+?) (.+)~i', $this->_name, $matches))
		{
			$firstName		= $matches[3];
			$middleInitial	= $matches[1];
			$lastName		= $matches[4];
			
			$this->_item->setFirstName($firstName);
			$this->_item->setMiddleInitial($middleInitial);
			$this->_item->setLastName($lastName);
			
			return;
		}
		
		list($firstName, $middleName, $lastName) = $this->_breakUp();
		
		$this->_item->setFirstName($firstName);
		$this->_item->setLastName($lastName);
		
		if (strlen(trim($middleName, '., ')) == 1)
		{
			$this->_item->setMiddleInitial($middleName);
			return;
		}
		
		# Check to see if the first name part are is a valid surname -- if not, then the first part becomes
		# appended to the middle name.
		$validSurname = Zephyr_Helper_Name_Census::getInstance()->find($middleName);
		
		# If the middle name is a valid surname as well as of a Hispanic nature, then typically such names
		# are made up of two parts for its surname (matronymic and patronymic).
		if ($validSurname && $validSurname->percentHispanic > 90)
		{
			$lastName = sprintf('%s %s', $middleName, $lastName);
			$this->_item->setLastName($lastName);
			$this->_item->setMiddleName(null);
			
			return;
		}
		
		$this->_item->setMiddleName($middleName);
	}
}