<?php 

abstract class Zephyr_Helper_Address_Detective_Abstract
{
	protected $_type = '';
	protected $_enablePrompts = true;
	
	private function _promptUserConfirm($address)
	{
		Zephyr_Output::rawStatic("Can't find '%s' in \"%s\"\n", $this->_type, $address);
		
		return Zephyr_Prompt::confirm("Is there a {$this->_type} in the address?");
	}
	
	private function _promptUserInput()
	{
		$input = Zephyr_Prompt::input('Please specify the ' . $this->_type);

		if (strlen($input))
		{
			return $input;
		}
		
		Zephyr_Output::rawStatic("Invalid input, please try again.");
			
		return $this->_promptUserInput();
	}

	private function _getTokens($fromText)
	{
		$words = explode(',', $fromText);
		$words = array_pop($words);
		$words = explode(' ', $words);
		$unparsedWords = array_reverse($words);
		$words = array();

		foreach ($unparsedWords as $word)
		{
			if (!$this->_isValidWord($word))
			{
				break;
			}
			
			$words[] = $word;
		}

		$guesses = array();

		for ($numWords = 1, $count = count($words); $numWords <= $count; $numWords++)
		{
			$guess = array();
				
			for ($wordIndex = 0; $wordIndex < $numWords; $wordIndex++)
			{
				$guess[] = $words[$wordIndex];
			}
				
			$guesses[] = implode(' ', $guess);
		}

		return $guesses;
	}
	
	protected function _isValidWord($word)
	{
		return true;
	}

	protected function _guess($tokens, $originalText)
	{
		return false;
	}

	public function detect($fromText, $originalText)
	{
		$tokens = $this->_getTokens($fromText);
		
		if ($guess = $this->_guess($tokens, $originalText))
		{
			return $guess;
		}
		
		if (!$this->_enablePrompts)
		{
			return false;
		}
		
		if (!$this->_promptUserConfirm($originalText))
		{
			return '';
		}

		return $this->_promptUserInput();
	}
}