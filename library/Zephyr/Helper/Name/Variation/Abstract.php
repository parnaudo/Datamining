<?php

abstract class Zephyr_Helper_Name_Variation_Abstract
{
	protected $_item;
	protected $_name;
	protected $_matched;
	
	public function __construct($name, Zephyr_Helper_Name_Item_Name $item)
	{
		$this->_item 	= $item;
		$this->_name	= $name;
	}
	
	protected function _breakUp()
	{
		$parts = preg_split('~(\s+|,)~', $this->_name);
		
		foreach ($parts as $index => $part)
		{
			$part = trim($part);
			
			if ($part)
			{
				continue;
			}
			
			unset($parts[$index]);
		}
		
		return array_values($parts);
	}
	
	protected function _beforeFirstComma()
	{
		if (!preg_match('~^(.+?),~i', $this->_name, $matches))
		{
			return null;
		}
		
		return $matches[1];
	}
	
	protected function _afterFirstComma()
	{
		if (!preg_match('~^.+?,(.+)~i', $this->_name, $matches))
		{
			return null;
		}
		
		return $matches[1];
	}
	
	protected function _split($name, $pattern)
	{
		$regExp	= sprintf('~%s~i', $pattern);
		$parts 	= preg_match($regExp, $name, $matches);
		
		array_shift($matches);
		return $matches;
	}
	
	public function isAppropriate()
	{
		if (isset($this->_expectedParts))
		{
			# Remove any items that don't contain any letters at all.
			$callback = function($input)
			{
				return (bool) preg_match('~[a-z]~i', $input);
			};
			
			$parts 	= explode(' ', $this->_name);
			$parts 	= array_filter($parts, $callback);
			
			if (count($parts) != $this->_expectedParts)
			{
				return false;
			}
			
			return true;
		}
		
		$padding 	= ($this->_padAttributes) ? '\s+' : null;
		$regExp 	= sprintf('~%1$s(%2$s)%1$s%3$s~i', $padding, implode('|', $this->_expectedAttributes), $this->_characterRight);
		
		if (preg_match($regExp, $this->_name, $matches))
		{
			$this->_matched = $matches[1];
			return true;
		}
		
		return false;
	}
}