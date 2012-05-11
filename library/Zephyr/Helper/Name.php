<?php

class Zephyr_Helper_Name extends Zephyr_Helper_Name_Abstract
{
	public static $enableCaching = true;
	private static $_className;
	
	public static function getClassName()
	{
		return self::$_className;
	}
	
	protected function _process($name)
	{
		if (Zephyr_Helper_Name::$enableCaching == true && Zephyr_Helper_Name_Cache::isCached($this->_name))
		{
			$message = sprintf('"%s" is correct from the previous parse, therefore it is revived from cache.', $this->_name);
			Zephyr_Helper_Name_Abstract::addAssumption($message);
			
			$this->_item = Zephyr_Helper_Name_Cache::loadFromCache($this->_name);
			return;
		}
		
		$discoveredException = false;
		
		foreach ($this->_getClasses() as $className)
		{
			$class = new $className($name, $this->_item);
			
			if (!$class->isAppropriate() || $discoveredException)
			{
				continue;
			}
			
			if (preg_match('~Exception~i', $className))
			{
				$discoveredException = true;
			}
			
			self::$_className = $className;
			$class->process();
			Zephyr_Helper_Name_Cache::save($this->_name, $this->_item);
			return;
		}
		
		throw new Exception(sprintf('Could not handle name: "%s"', $name));
	}
	
	public function __toString()
	{
		$string	= null;
		$string .= '<strong>Credentials:</strong> ' . $this->_item->getCredentials() . '<br />';
		$string .= '<strong>Positions:</strong> ' . $this->_item->getPositions() . '<br />';
		$string .= '<strong>Suffixes:</strong> ' . $this->_item->getSuffixes() . '<br />';
		$string .= '<strong>First Name:</strong> ' . $this->_item->getFirstName() . '<br />';
		$string .= '<strong>Middle Name:</strong> ' . $this->_item->getMiddleName() . '<br />';
		$string .= '<strong>Middle Initial:</strong> ' . $this->_item->getMiddleInitial() . '<br />';
		$string .= '<strong>Last Name:</strong> ' . $this->_item->getLastName() . '<br />';
		$string .= '<strong>Nickname:</strong> ' . $this->_item->getNickname() . '<br />';
		$string .= '<strong>Used Class:</strong> ' . self::getClassName() . '<br />';
		
		$assumptions = Zephyr_Helper_Name_Abstract::getAssumptions();
		
		if ($assumptions)
		{
			$string .= '<h3>Assumptions</h3>';
			$string	.= '<ul>';
			
			foreach ($assumptions as $assumption)
			{
				$string .= '<li>' . $assumption . '</li>';
			}
			
			$string	.= '</ul>';
		}
			
		return $string;
	}
	
	private function _getClasses()
	{
		return array
		(
			'Zephyr_Helper_Name_Variation_Exception_Connective',
			'Zephyr_Helper_Name_Variation_Exception_LatinSurname',
			'Zephyr_Helper_Name_Variation_Exception_SpanishLetter',
//			'Zephyr_Helper_Name_Variation_Exception_SpanishSuffix',
			'Zephyr_Helper_Name_Variation_Exception_ItalianSuffix',
			'Zephyr_Helper_Name_Variation_One',
			'Zephyr_Helper_Name_Variation_Two',
			'Zephyr_Helper_Name_Variation_Three',
			'Zephyr_Helper_Name_Variation_Four',
			'Zephyr_Helper_Name_Variation_Five',
			'Zephyr_Helper_Name_Variation_Six'
		);
	}
}