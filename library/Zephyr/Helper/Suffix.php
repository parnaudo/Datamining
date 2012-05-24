<?php 

/**
 *  @author Karl
 */
class Zephyr_Helper_Suffix extends Zephyr_Helper_Address_Address2_Abstract
{
	protected function _getRegex()
	{
		$suffixes = array('SR\.?', 'JR\.?', 'III', 'II', 'V');
		$suffixes = implode('|', $suffixes);
		return "~(?:^|\s)($suffixes)([\s,]|$)~i";
	}
	
	protected function _getReplacement()
	{
		return ' ';
	}
}