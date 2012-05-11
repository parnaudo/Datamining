<?php

class Zephyr_Helper_Address_Zip extends Zephyr_Helper_Address_Helper
{
	protected $_extractMatchIndex = 2;
	protected $_filterMatchIndex = 2;
	
	protected function _getRegex()
	{
		return array(
				'single' 	=> '~([^\d])\s(\d{4,5})$~',
				'multiple'	=> '~([^\d])\s(\d{5}-\d{4,5})$~',
				'multiple2'	=> '~([^\d])\s(\d{9})$~');
	}
	
	protected function _getReplacement()
	{
		return '$1';
	}
}