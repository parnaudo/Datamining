<?php 

class Zephyr_Helper_Address_Parser
{
	public function parse($text)
	{
		# Parse items from the text, creating an array of types
		# E.g. Address1, Address2, City, State, etc.
		$address = $this->_parseItems($text);
		
		return $address;
	}
	
	private function _parseItems($originalText)
	{
		$text 		= $originalText;
		$address 	= array();
		$debug 		= array();
		$types 		= array('Country', 'Zip', 'State', 'City');
		
		# Loop through each parser and parse the string for as much data as we can
		foreach ($types as $type)
		{
			$address[$type] = '';
			
			$parser = "Zephyr_Helper_Address_Parser_$type";
			$parser = new Zephyr_Helper_Address_Parser_Item($type);
			$results = $parser->parse($text, $originalText);
			$text = $results['filtered'];
			$value = $results['extracted'];
			$before = $results['before'];
				
			$address[$type] = $value;
			!strlen($value) || $debug[$type] =  "Found $type '$value' from '$before'";
		}
		
		$debug = array_reverse($debug);
		
		# Validate the address and add any errors for debugging
		$address = $this->_validate($address);
		
		$csz = array($address['City'], $address['State'], $address['Zip'], $address['Country']);
		$csz = array_filter($csz, 'strlen');
		
		$address['CSZ'] = implode(', ', $csz);  
		$address['Address'] = $text;
		$address['messages']['debug'] = $debug;
				
		$address = array_reverse($address);
		
		return $address;
	}
	
	private function _validate($address)
	{
		$errors = array();
		$warnings = array();
		
		# Find errors
		$types = array('City', 'State', 'Zip');
		foreach ($types as $type)
		{
			if (strlen($address[$type]))
			{
				continue;
			}
			
			$errors[] = 'Unable to find value for ' . $type;
		}
		
		# Find warnings
		$types = array('Country');
		foreach ($types as $type)
		{
			if (strlen($address[$type]))
			{
				continue;
			}
			
			$warnings[] = 'Unable to find value for ' . $type;
		}
	
		# Add any errors to the address
		$address['messages']['errors'] = $errors;
		$address['messages']['warnings'] = $warnings;
			
		return $address;
	}
}