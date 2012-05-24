<?php

abstract class Zephyr_Helper_Address_Helper
{
	protected $_text;
	protected $_regexIndex = null;
	protected $_lastMatches = array();
	protected $_filterMatchIndex = 1;
	protected $_extractMatchIndex = 1;
	
	public function detect($text)
	{
		$result = $this->_processText($text, 'detect');
		return (bool) $result->success; 
	}
	
	public function extract($text)
	{
		$result = $this->_processText($text, 'extract');
		if ($result->success)
		{
			return $this->_filterOutput($result->matches[$this->_extractMatchIndex]);
		}
		return '';
	}
	
	public function filter($text)
	{
		$result = $this->_processText($text, 'filter');
		if ($result->success)
		{
			$regex = (array) $this->_getRegex();
			$regex = $regex[$this->_regexIndex];
			$replacement = $this->_getReplacement();
			
			$count = 0;
			$text = preg_replace($regex, $replacement, $text, 1, $count);
			return $this->_filterOutput($text);
		}
		return $text;
	}
	
	protected function _processText($text, $type)
	{
		$this->_text = $this->_filterInput($text);
		$result = (object) array('success' => false, 'matches' => null);
		$regexs	= (array) $this->_getRegex();		
	
		# Determine which index in the $matches array we want to 
		# perform validation on
		$matchIndex = array(	'filter' 	=> $this->_filterMatchIndex, 
								'extract' 	=> $this->_extractMatchIndex,
								'detect'	=> $this->_extractMatchIndex);

		# Assign the match index into the matchIndex variable 
		$matchIndex = $matchIndex[$type];
		
		foreach ($regexs as $index => $regex)
		{
			if (preg_match($regex, $this->_text, $matches))
			{
				$targetText = $matches[$matchIndex];
				
				if ($this->_validate($targetText))
				{
					$this->_regexIndex = $index;
					$result->success = true;
					$result->matches = $matches;
					
					$this->_lastMatches = $matches;
					break;
				}
			}
		}
		
		return $result;
	}
	
	protected function _validate($output)
	{
		return true;
	}
	
	abstract protected function _getRegex();
	
	protected function _getReplacement()
	{
		return '';
	}
	
	protected function _filterInput($text)
	{
		$text = trim($text, ' ,.-/#');
		$text = str_replace(chr('32'), ' ', $text);
		return $text;
	}
	
	protected function _filterOutput($text)
	{
		$text = trim($text, ' ,.-/#');
		return $text;
	}
}