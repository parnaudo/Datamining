<?php

class Zephyr_Helper_Name_Normalise_SuspectedCredentials
{
	/**
	 * Contains the name.
	 *
	 * @var string
	 */
	private $_name;
	
	/**
	 * Standardise the name to remove any nonsense.
	 *
	 * @param string $name
	 */
	public function __construct($name)
	{
		# Attempts to break apart suspected credentials.
		$name = preg_replace('~([a-z]+)\.([a-z]+)$~i', '\\1. \\2', $name);
		
		# Set the name to the member variable.
		$this->_name = $name;
	}
	
	/**
	 * Returns the name.
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->_name;
	}
}