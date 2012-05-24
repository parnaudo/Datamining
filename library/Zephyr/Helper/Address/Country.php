<?php

class Zephyr_Helper_Address_Country extends Zephyr_Helper_Address_Helper
{
	protected function _getRegex()
	{
		$regex = array();
		$regex[] = '~(?:\s|,\s?)(([A-Z][a-z]+\s?){1,})$~';
		$regex[] = '~(?:\s|,\s?)([A-Z]+\s[A-Z][a-z]+)$~i';
		$regex[] = '~(?:\s|,\s?)([A-Z]+)$~';
		return $regex;
	}
	
	protected function _validate($text)
	{
		if (preg_match('~^[A-Z]+$~', $text))
		{
			$codes = array('US', 'USA', 'UK');
			return in_array($text, $codes);			
		}
		
		$countriesDb = new Zephyr_Tool_CreateCountriesDb();
		return $countriesDb->hasCountry($text);
	}
}
