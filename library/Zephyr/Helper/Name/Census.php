<?php

class Zephyr_Helper_Name_Census
{
	private static $_instance;
	private $_lines;
	
	private function __construct()
	{
		$document 		= dirname(dirname(dirname(__FILE__))) . '/_data/us-census-2000.csv';
		$content		= file_get_contents($document);
		
		$this->_lines 	= explode("\n", $content);
	}
	 
	public static function getInstance()
	{
		if (!isset(self::$_instance))
		{
			self::$_instance = new self();
		}
		
		return self::$_instance;
	}
	
	public function find($surname)
	{
		$regExp	= sprintf('~^%s,~i', preg_quote($surname));
		$line 	= preg_grep($regExp, $this->_lines);
		
		if (!$line)
		{
			return null;
		}
		
		$line	= explode(',', array_pop($line));
		
		return (object) array
		(
			'name' => $line[0], 'rank' => (int) $line[1], 'count' => (int) $line[2], 'proportionPer100k' => (float) $line[3],
			'cumulativeProportionPer100k' => (float) $line[4], 'percentWhiteOnly' => (float) $line[5], 'percentBlackOpen' => (float) $line[6],
			'percentAsianPacific' => (float) $line[7], 'percentAmericanInianAlaskan' => (float) $line[8], 'percentTwoOrMore' => (float) $line[9],
			'percentHispanic' => (float) $line[10]
		);
	}
}