<?php

class Zephyr_Helper_Name_Assumption_NameIsActuallyCredential extends Exception
{
	public function __construct($name, $assumedAttribute)
	{
		$message = sprintf('Assuming the "%s" in "%s" is a credential, not a name.', $name, $assumedAttribute);
		parent::__construct($message);
	}
}