<?php

class Zephyr_Helper_Name_Variation_One extends Zephyr_Helper_Name_Variation_Abstract
{
	protected $_expectedParts = 1;
	
	public function process()
	{
		$this->_item->setLastName($this->_name);
	}
}