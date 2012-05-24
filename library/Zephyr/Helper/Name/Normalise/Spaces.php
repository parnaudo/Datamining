<?php

class Zephyr_Helper_Name_Normalise_Spaces
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
		# Trim the name.
		$name = trim($name);
		
		# Contract any spaces larger than one to only one space.
		$name = preg_replace('~\s{2,}~i', ' ', $name);
		
		if (preg_match('~[a-z]~', $name))
		{
			# Adds a space between the first initials in things like: "JK Rowling"
			$name = preg_replace('~([A-Z]{1})([A-Z]{1})\s+~', '\\1 \\2 ', $name);
		}
		
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