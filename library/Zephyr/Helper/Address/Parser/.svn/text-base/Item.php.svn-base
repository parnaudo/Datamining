<?php 

class Zephyr_Helper_Address_Parser_Item
{
	private $_type;

	public function __construct($type)
	{
		$this->_type = $type;
	}

	public function parse($fromText, $originalText)
	{
		$helper = 'Zephyr_Helper_Address_' . $this->_type;
		$helper = new $helper();
		$before = $fromText;
		
		if ($helper->detect($fromText))
		{
			$value = $helper->extract($fromText);
			$fromText = $helper->filter($fromText);
		}
		else
		{
			$value = $this->_notFound($fromText, $originalText);
		}

		return array('extracted' => $value, 'filtered' => $fromText, 'before' => $before);
	}

	protected function _notFound($fromText, $originalText)
	{
		if (!Zephyr_Helper_Address::$enableUserInput)
		{
			return '';
		}
	
		$detective = 'Zephyr_Helper_Address_Detective_' . $this->_type;
		$detective = new $detective();
		$detective->detect($fromText, $originalText);
	}
}