<?php

class Zephyr_Helper_Name_Component_Suffixes extends Zephyr_Helper_Name_Component
{
	protected function _process($name)
	{
		$suffixes = array();
		
		# Stuff a list of the suffixes into a regular expression.
		$regExp = sprintf('~(%s)($|\s|,|\.)~i', implode('|', $this->_getSuffixes()));
		
		$index = 0;
		
		# Loop through the suffixes, stripping them out when detected.
		while (preg_match($regExp, $name, $matches))
		{
			$index++;
			
			if ($index > 20)
			{
				throw new Exception('Too much suffix looping: ' . $this->_name);
			}
			
			# Do a little cleaning up of the discovered suffix.
			$suffix = trim($matches[1]);
			
			# Place the suffixes into the array.
			$suffixes[]	= $suffix;
			
			# Replace the name with what we found.
			$name = preg_replace(sprintf('~%s(\.|\,|\s|$)+~', preg_quote($suffix)), ' ', $name);
			$name = trim($name);
		}
		
		# Strip out the discovered suffix from the name.
		$this->_name = $this->_standardiseName($name);
		$this->_item->setSuffixes($suffixes);
	}
	
	/**
	 *
	 * @return array
	 */
	private function _getSuffixes()
	{
		$firstSuffixes 	= $this->_applyPatterns(array('Med', 'Dr', 'Doctor', 'Mr', 'Miss', 'Mrs', 'Col', 'Jr', 'Señorita', 'Señora', 'Señor', 'Sr', 'III', 'II', 'IV', 'VI'));
		$secondSuffixes	= $this->_applyPatterns(array('I', 'V'), '[,|\s]*', '\s+');
		
		return array_merge($firstSuffixes, $secondSuffixes);
	}
}