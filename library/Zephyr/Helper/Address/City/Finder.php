<?php 

class Zephyr_Helper_Address_City_Finder
{
	public function askUserForCity()
	{
		$input = Zephyr_Prompt::input('Please specify the city');
		
		if (!strlen($input))
		{
			Zephyr_Output::rawStatic("Invalid input, please try again.");
			return $this->askUserForCity();
		}
		
		return $input;
	}
	
	public function _guessCityFromAddress($parsedText)
	{
		if (preg_match('~(?:.+,|^)\s*([-A-Za-z\s.\']+){1,4}$~', $parsedText, $matches))
		{
			return array(trim($matches[1]));
		}
		
		$words = explode(',', $parsedText);
		$words = array_pop($words);
		$words = explode(' ', $words);
		$unparseWords = array_reverse($words);
		$words = array();
		
		foreach ($unparseWords as $word)
		{
			if (is_numeric($word))
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
	
	public function guessCity($address, $parsedText)
	{
		$cities = $this->_guessCityFromAddress($parsedText);
		
		foreach ($cities as $city)
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
	
	public function findCity($address, $parsedText)
	{
		if ($city = $this->guessCity($address, $parsedText))
		{
			return $city;
		}
		
		Zephyr_Output::rawStatic("Unable to find city from the following address:\n%s\n", $address);
		
		if (!Zephyr_Prompt::confirm('Is there a city in the address?'))
		{
			return '';
		}
		
		$city = $this->askUserForCity();
		
		$citiesDatabase = new Zephyr_Tool_CreateCitiesDb();
		$citiesDatabase->addNewCity($city);
		
		return $city;
	}
}