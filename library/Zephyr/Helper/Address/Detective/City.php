<?php 

class Zephyr_Helper_Address_Detective_City extends Zephyr_Helper_Address_Detective_Abstract
{
	protected $_type = 'City';
	
	protected function _isValidWord($word)
	{
		return !is_numeric($word);
	}
	
	protected function _guess($tokens, $address)
	{
		foreach ($tokens as $city)
		{
			$lookup = new Zephyr_Helper_Address_City_Lookup();
			$guesses = $lookup->getMatches($city);

			if (!count($guesses))
			{
				return false;
			}
		
			Zephyr_Output::rawStatic("Looking for City in \"%s\"\n", $address);
			Zephyr_Output::rawStatic("Possible Matches:\n");
			
			foreach ($guesses as $index => $guess)
			{
				Zephyr_Output::rawStatic("%d. %s\n", $index + 1, $guess['name']);
			}
			
			if (Zephyr_Prompt::confirm("Is '$city' a valid city?"))
			{
				$citiesDatabase = new Zephyr_Tool_CreateCitiesDb();
				$citiesDatabase->addNewCity($city);
				return $city;		
			}
			
			if (Zephyr_Prompt::confirm("Is the city in the list?"))
			{
				do
				{
					$input = (int) Zephyr_Prompt::input("Please enter the city number");
					--$input;
				}
				while($input >= 0 && !array_key_exists($input, $guesses));
				
				if (array_key_exists($input, $guesses))
				{
					$citiesDatabase = new Zephyr_Tool_CreateCitiesDb();
					$citiesDatabase->addNewCity($guesses[$input]['name']);
					return $guesses[$input]['name'];
				}
			}
		}
		
		return false;
	}
}