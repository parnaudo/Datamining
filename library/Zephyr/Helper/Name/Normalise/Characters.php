<?php

class Zephyr_Helper_Name_Normalise_Characters
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
		# Transforms things like S.A into S. A.
		$name = preg_replace('~([A-Z]{1})\.([A-Z]{1})~', '\\1. \\2', $name);
		
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