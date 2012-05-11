<?php 

/**
 * Provides an easy way to prompt for user input in the CLI - also beeps!
 * 
 * @author Karl
 */
class Zephyr_Prompt
{
	private $_message;
	private $_whitelist;
	private $_response;
	
	/**
	 * Prepare a new prompt with the following message, requiring an input from the whitelist array
	 * 
	 * @param unknown_type $message
	 * @param unknown_type $whitelist
	 */
	public function __construct($message, array $whitelist)
	{
		$this->_message = $message;
		$this->_whitelist = (array) $whitelist;
	}
	
	/**
	 * Prompts the user for input and returns the response.
	 */
	public function getInput()
	{
		if (defined('TESTING'))
		{
			return null;
		}
		
		# Beep! Beep! Beep!
//		echo "\x07\x07\x07";
	
		$success = false;
		
		$validatedInput = null;
		
		while (!$success)
		{
			Zephyr_Output::rawStatic($this->_message . ': ');
			
			$input = trim(fgets(STDIN));
			
			if (strtolower($input) == "cancel")
			{
				break;
			}
			
			if (count($this->_whitelist) && !in_array($input, $this->_whitelist))
			{
				Zephyr_Output::rawStatic('Please input a corret value or type "cancel".' . "\n");
				continue;
			}
			
			$success = true;
			$validatedInput = $input;
			break;
		}
		
		return $validatedInput;
	}

	public static function confirm($message)
	{
		$prompt = new self($message . ' (y/n)', array('y', 'n'));
		return (bool) (strtolower($prompt->getInput()) == 'y');
	}

	public static function input($message)
	{
		$prompt = new self($message, array());
		return $prompt->getInput();
	}
}