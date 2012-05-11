<?php 

/**
 *  @author Karl
 */
class Zephyr_Helper_Prefix extends Zephyr_Helper_Address_Address2_Abstract
{
	protected function _getRegex()
	{
		return '~^([A-Z]{2,})[\s,.]~';
	}
}