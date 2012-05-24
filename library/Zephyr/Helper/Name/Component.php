<?php

abstract class Zephyr_Helper_Name_Component
{
	protected $_name;
	protected $_item;
	
	public function __construct($name, Zephyr_Helper_Name_Item_Name $item)
	{
		$this->_item 	= $item;
		$this->_name	= $name;

		$this->_process($name);
	}
	
	public function getName()
	{
		return $this->_name;
	}
	
	protected function _standardiseName($name)
	{
		# Normalise the name to remove any anomalies.
		$standardise  	= new Zephyr_Helper_Name_Normalise_Default($name);
		return $standardise->getName();
	}
	
	protected function _applyPatterns(array $items, $pattern = '[\.,\s]*', $append = null)
	{
		foreach ($items as &$item)
		{
			$item = preg_replace('~~i', $pattern, $item);
			$item = preg_replace(sprintf('~^%s~', preg_quote($pattern)), '(^|\s|,|\-|/|\.){1}', $item);
			
			if (!is_null($append))
			{
				$item .= $append;
			}
		}
		
		return $items;
	}
}