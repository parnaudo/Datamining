<?php

class Zephyr_Helper_Name_Assumption_CredentialIsActuallyName extends Exception
{
	public function __construct($name, $assumedAttribute)
	{
		$message = sprintf('Assuming the "%s" in "%s" is part of a name, not a credential.', $name, $assumedAttribute);
		parent::__construct($message);
	}
}