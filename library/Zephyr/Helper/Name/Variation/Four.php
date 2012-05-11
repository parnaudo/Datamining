<?php

class Zephyr_Helper_Name_Variation_Four extends Zephyr_Helper_Name_Variation_Abstract
{
	protected $_expectedParts = 4;
	
	public function process()
	{
		# To handle things like: "Jay Michael S. Balagtas" and "Michael S. B. Edwards" where there are two middle names.
		if (preg_match('~(.+?) (.+?) ([A-Z]{1}[\s|,\.]+) (.+)~i', $this->_name, $matches))
		{
			$firstName		= $matches[1];
			$middleName		= $matches[2];
			$middleInitial	= $matches[3];
			$lastName		= $matches[4];
			
			# If the middle name is a single letter too, then prepend it to the middle initial.
			if (strlen(preg_replace('~[^A-Z]~', '', $middleName)) == 1)
			{
				$middleInitial 	= sprintf('%s %s', $middleName, $middleInitial);
				$middleName		= null;
			}
			
			$this->_item->setFirstName($firstName);
			$this->_item->setMiddleName($middleName, true);
			$this->_item->setMiddleInitial($middleInitial);
			$this->_item->setLastName($lastName);
			
			return;
		}
		
		# Handles things like: "Lucy Christina N Smith"
		if (preg_match('~(.+?) (.+?) ([A-Z]{1}\s)* (.+)~i', $this->_name, $matches))
		{
			$firstName		= $matches[1];
			$middleName		= $matches[2];
			$middleInitial	= $matches[3];
			$lastName		= $matches[4];
			
			$this->_item->setFirstName($firstName);
			$this->_item->setMiddleName($middleName);
			$this->_item->setMiddleInitial($middleInitial);
			$this->_item->setLastName($lastName);
			
			return;
		}
		
		# Handles things like: "T. Prescott Isaac Atkinson"
		if (preg_match('~([A-Z]{1}(\s|\.)) (.+?) (.+?) (.+)~i', $this->_name, $matches))
		{
			$firstName		= $matches[3];
			$middleInitial	= $matches[1];
			$middleName		= $matches[4];
			$lastName		= $matches[5];
			
			$this->_item->setFirstName($firstName);
			$this->_item->setMiddleName($middleName);
			$this->_item->setMiddleInitial($middleInitial);
			$this->_item->setLastName($lastName);
			
			return;
		}
		
		# Try to get the parts before the first comma -- if it exists.
		$firstName = $this->_beforeFirstComma();
		
		# If we have a comma to help us identify what's what, then let's use it.
		if ($firstName)
		{
			# Get the remainder of the name after the comma.
			$remainingName = $this->_afterFirstComma();
			
			# Split the rest appropriately, so that we always have a last name of some kind.
			list($middleName, $lastName) = $this->_split($remainingName, '(.+? .+?) (.+)');
			
			# Set the items.
			$this->_item->setFirstName($firstName);
			$this->_item->setMiddleName($middleName);
			$this->_item->setLastName($lastName);
			
			return;
		}
		
		# Otherwise there are no commas to help -- so we'll make an assumption.
		list($firstName, $middleName, $lastName) = $this->_split($this->_name, '(.+?) (.+?) (.+? .+)');
		
		# Check to see if the first name part are is a valid surname -- if not, then the first part becomes
		# appended to the middle name.
		list($firstPart, $secondPart) = explode(' ', $lastName);
		$validSurname = Zephyr_Helper_Name_Census::getInstance()->find($firstPart);
		
		# If the first part of the surname isn't a valid surname, then append it to the middle name, rather
		# than hooking it onto the surname.
		if (!$validSurname || $validSurname->percentHispanic > 90)
		{
			$lastName 	= $secondPart;
			$middleName	.= ' ' . $firstPart;
		}
		else
		{
			# Otherwise there are no commas to help -- so we'll make an assumption.
			list($firstName, $middleName, $lastName) = $this->_split($this->_name, '(.+?) (.+? .+?) (.+)');
		}
		
		# Set the items.
		$this->_item->setFirstName($firstName);
		$this->_item->setMiddleName($middleName);
		$this->_item->setLastName($lastName);
	}
}