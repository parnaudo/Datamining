<?php

class Zephyr_Helper_Name_Variation
{
	protected $_item;
	
	public function __construct($name)
	{
		$this->_process($name);
	}
}