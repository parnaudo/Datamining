<?php 

class Zephyr_Helper_Encoding
{
	private $_decorators = array('Utf8');
	
	public function fix($text)
	{
		foreach ($this->_decorators as $decorator)
		{
			$decorator = "Zephyr_Helper_Encoding_" . $decorator;
			$decorator = new $decorator;
			$text = $decorator->fix($text);
		}
		
		return $text;
	}
}