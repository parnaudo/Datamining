<?php

class Zephyr_Helper_Name_FirstNames
{
	private static $_instance;
	private $_lines;
	
	private function __construct()
	{
		$document 		= dirname(dirname(dirname(__FILE__))) . '/_data/first-names.csv';
		$content		= file_get_contents($document);
		
		$this->_lines 	= explode("\r", $content);
	}
	
	public static function getInstance()
	{
		if (!isset(self::$_instance))
		{
			self::$_instance = new self();
		}
		
		return self::$_instance;
	}
	
	public function exists($firstName)
	{
		$regExp	= sprintf('~^%s$~i', $firstName);
		return (bool) count(preg_grep($regExp, $this->_lines));
	}
}