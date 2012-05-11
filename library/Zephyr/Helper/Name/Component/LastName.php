<?php

class Zephyr_Helper_Name_Component_LastName extends Zephyr_Helper_Name_Component
{
	/**
	 * Process the last name at the beginning of the string, if it exists.
	 *
	 * @param string $name
	 */
	protected function _process($name)
	{
		# Places the surname at the end of the string, where one word appears before a comma, at the
		# beginning of the string.
		if (preg_match('~^([^\s]+), (.+)~i', $name, $matches))
		{
			$name = sprintf('%s %s', $matches[2], $matches[1]);
			
			$message = sprintf('Comma after "%1$s" indicates that "%1$s" is a surname, therefore "%2$s" becomes the first name.', $matches[1], $matches[2]);
			Zephyr_Helper_Name_Abstract::addAssumption($message);
			
			# Return the items.
			$this->_name = $this->_standardiseName($name);
			
			return;
		}
		
		$parts					= explode(' ', $name);
		$firstName 				= array_shift($parts);
		$lastName				= array_pop($parts);
		
		# Determine whether the current last name is a valid last name, and return if so.
		$isCurrentLastName		= Zephyr_Helper_Name_Census::getInstance()->find($lastName);
		
		if ($isCurrentLastName)
		{
			$message = sprintf('"%1$s" is a valid surname, therefore as it also appears at the end, it is the surname."', $lastName);
			Zephyr_Helper_Name_Abstract::addAssumption($message);
			return;
		}
		
		# Determine whether this name is a first name and/or a last name.
		$isLastName				= Zephyr_Helper_Name_Census::getInstance()->find($firstName);
		$isFirstName			= Zephyr_Helper_Name_FirstNames::getInstance()->exists($firstName);
		
		# If the first name and the last name are both valid surnames, then we have a problem that needs
		# to be resolved. Also, the current first name must be a valid first name.
//		if ($isFirstName && $isLastName && $isCurrentLastName)
//		{
//			# If the first name's rank is higher than the current last name's rank, then no need to change.
//			if ($isLastName->rank > $isCurrentLastName->rank)
//			{
//				$message = sprintf('"%1$s" has a lower rank than "%2$s" therefore "%2$s" will remain as the surname."', $firstName, $lastName);
//				Zephyr_Helper_Name_Abstract::addAssumption($message);
//				return;
//			}
//		}
		
		# If it's a last name, but not a first name, then switch over the names, so that the last
		# name appears at the end of the string.
		if ($isLastName && !$isFirstName)
		{
			$this->_name	= str_ireplace($firstName, '', $this->_name);
			$this->_name 	.= ' ' . $firstName;
			
			$message = sprintf('"%1$s" is a surname but not a first name, therefore "%1$s" is the new surname."', $firstName, $lastName);
			Zephyr_Helper_Name_Abstract::addAssumption($message);
			
			return;
		}
	}
}