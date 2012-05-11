<?php

class Zephyr_Helper_Name_Normalise_Default
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
		$name = trim($name, ',- ');
		
		# Sometimes when we remove a position we're left with a stray period.
		# This little bit of code removes that stray period.
		$name = preg_replace('~([.,]?)\s*\.~', '$1', $name);
	
		# Places spaces after commas to break up credentials at the end.
		$name = preg_replace('~([A-Z]{1}),([A-Z]{1})~i', '\\1, \\2', $name);
		
		# Contract any spaces larger than one to only one space.
		$name 	= preg_replace('~\s{2,}~i', ' ', $name);
		
		# Sorts out things like: David Martin, M.D.,MPH
		$name	= str_replace('.,', '., ', $name);
		
		# Sort out a few things that may cause problems.
		$name	= str_replace('FRCP(c)', 'FRCPc', $name);
		$name	= str_replace('FRCP(C)', 'FRCPc', $name);
		$name	= str_replace('FRCS(c)', 'FRCSc', $name);
		$name	= str_replace('FRCS(C)', 'FRCSc', $name);
		
		$name	= str_replace('MRCP(UK)', 'MRCPUK', $name);
		$name	= str_replace('MRCP (UK)', 'MRCPUK', $name);
		$name	= str_replace('MRCOG(UK)', 'MRCOGUK', $name);
		$name	= str_replace('MRCOG (UK)', 'MRCOGUK', $name);
		
		$name	= str_replace('COHN-S/CM', 'COHN-SCM', $name);
		$name	= str_replace('CDONA/LTC', 'CDONALTC', $name);
		$name	= str_replace('COHN/CM', 'COHNCM', $name);
		$name	= str_replace('D. Phil', 'D.Phil', $name);
		
		$name	= str_replace('diplomate', '', $name);
		$name	= str_replace('Clinical Psychology', 'Clin Psy', $name);
		$name	= str_replace('Prof.Dr', 'Prof. Dr', $name);
		
		# Remove any nonsense that people might put into their name.
		$name	= preg_replace('~(\*|\~)~i', '', $name);
		
//		if (!preg_match('~[a-z]$~', $name))
//		{
//			# Solves things where credentials are joined together with hyphens. Example: MD-PhD
			$name = preg_replace('~([A-Z])(\-|/)([A-Z])~', '\\1 \\3', $name);
//		}
			
		# Adds a space between the first initials in things like: "J.W Bolton"
		$name = preg_replace('~^([A-Z]\.)([A-Z]\.)\s+~', '\\1 \\2 ', $name);
			
		# Adds a space between the first initials in things like: "JK Rowling"
		$name = preg_replace('~^([A-Z]{1})([A-Z]{1})\s+~', '\\1 \\2 ', $name);
		
		# Contract any spaces larger than one to only one space.
		$name 	= preg_replace('~\s{2,}~i', ' ', $name);
		
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