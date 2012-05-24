<?php

class Zephyr_Helper_City
{
	const DATABASE_FILEPATH	= '^/_data/db/geolocation.db';
	
	private $_db;
	private $_filePath;
	
	private $_countries;
	private $_states;
	private $_cities;
	
	public function __construct()
	{
		$this->_filePath	= str_replace('^', LIBRARY_PATH, self::DATABASE_FILEPATH);
		$this->_db 			= Zend_Db::factory('PDO_MYSQL', array('dbname' => 'geolocation',
																	'username' => 'root',
																	'password' => '0vodkah6',
																	'host' => 'localhost'));
		
//		if (!file_exists($this->_filePath))
//		{
//			file_put_contents($this->_filePath, null);
//			$this->_create();
//		}
	}
	
	private function _create()
	{
		Zephyr_Output::debugStatic('Creating and populating the geolocation database...');
		
		$filename	= basename($this->_filePath);
		$directory 	= dirname($this->_filePath);
		
		$file = realpath('./_data/worldcitiespop.txt');
		
		if (!$file) throw new Exception('Unable to continue because there is no "worldcitiespop.txt".');
		
		ini_set('memory_limit', 568435456);
		$content 	= file_get_contents($file);
		$lines		= explode("\n", $content);
		$sql		= null;
		
		$this->_db->beginTransaction();
		
		foreach ($lines as $index => $line)
		{
			if ($index % 500 == 0 && $index != 0)
			{
				$this->_db->commit();
				$this->_db->beginTransaction();
			}
			
			$parts 	= explode(',', $line);
			$line	= (object) array
			(
				'countryCode' 	=> $parts[0],
				'asciiCity' 	=> $parts[1],
				'city' 			=> $parts[2],
				'region' 		=> $parts[3],
				'population' 	=> $parts[4],
				'latitude' 		=> $parts[5],
				'longtitude' 	=> $parts[6]
			);
			
			foreach ($line as &$lineItem)
			{
				$lineItem = str_replace('"', "'", $lineItem);
			}
			
			if ($line->countryCode == 'Country')
			{
				continue;
			}
			
			Zephyr_Output::logStatic('Inserting %d/%d: %s', ($index + 1), count($lines), $line->asciiCity);
			$sql = sprintf('INSERT INTO geolocation (countryCode, asciiCity, city, region, population, latitude, longtitude) VALUES ("%s","%s","%s","%s","%s","%s","%s")',
				$line->countryCode, $line->asciiCity, $line->city, $line->region,
				$line->population, $line->latitude, $line->longtitude
			);
			
			try
			{
				$this->_db->query($sql);
			}
			catch (Exception $e)
			{
				file_put_contents('C:/Query.txt', $sql);
			}
		}
		
		$this->_db->commit();
	}
	
	public function addCountries(array $countries)
	{
		foreach ($countries as &$country)
		{
			$country = trim($country, '.,- ');
			$country = iconv('UTF-8', 'ISO-8859-1', $country);
			
			if ($country && strlen($country) != 2)
			{
				$country = $this->_transformCountry($country);
			}
			
			$find 		= array('uk', 'usa');
			$replace	= array('gb', 'us');
			$country	= str_ireplace($find, $replace, $country);
		}
		
		$this->_countries = $countries;
	}
	
	public function addStates(array $states)
	{
		foreach ($states as &$state)
		{
			$state 	= trim($state, '.,- ');
			$state 	= iconv('UTF-8', 'ISO-8859-1', $state);
			$state 	= $this->_findCommonContractions($state);
			
			if ($state && strlen($state) != 2)
			{
				$state = $this->_transformState(trim($state));
			}
		}
		
		$this->_states = $states;
	}
	
	public function addCities(array $cities)
	{
		foreach ($cities as &$city)
		{
			$city 	= trim($city, '.,- ');
			$city	= str_ireplace('St ', 'Saint ', $city);
			$city	= str_ireplace('St. ', 'Saint ', $city);
			
			$city = iconv('UTF-8', 'ISO-8859-1', $city);
			
			$city = str_replace('�', 'a', $city);
			
			$city 		= $this->_findCommonContractions($city);
			$city		= $this->_clean($city);
		}
		
		$this->_cities = $cities;
	}
	
	public function find()
	{
		# We don't want any duplicate entries, since this would slow down the SQL, so we'll remove them.
		$this->_cities 		= array_unique($this->_cities);
		$this->_countries 	= array_unique($this->_countries);
		$this->_states 		= array_unique($this->_states);
		
		# Encapsulation function to place the fieldname, followed by quotations around the value.
		$encapsulate = function(&$value, $key, $fieldName)
		{
			$value = $fieldName . ' = "' . $value . '"';
		};
		
		# Encapsulate all of the entries in the arrays in SQL fields and quotations.
		array_walk($this->_states, $encapsulate, 'region');
		array_walk($this->_cities, $encapsulate, 'city');
		array_walk($this->_countries, $encapsulate, 'countryCode');
		
		# Construct the SQL that we'll use to find the GeoLocation of the publication.
		$sql = sprintf("SELECT * FROM geolocation WHERE (%s) AND ((%s) OR (%s))",
						implode(' OR ', $this->_cities), implode(' OR ', $this->_countries), implode(' OR ', $this->_states));
					
		# Attempt to fetch the result from the database.	
		$result	= $this->_db->fetchRow($sql);
		
		# If there is no result, then simply return false.
		if (!$result) return false;
		
		# Otherwise we can return the beautiful SQL row.
		return $result;
	}
	
	private function _transformCountry($country)
	{
		$find = array('Great Britain', 'United States of America', 'The Netherlands', 'People\'s Republic of China');
		$replace = array('United Kingdom', 'United States', 'Netherlands', 'China');
		$country = str_replace($find, $replace, $country);
		
		$url = 'http://www.iso.org/iso/list-en1-semic-3.txt';
		$content = file_get_contents($url);
		
		$regExp = sprintf('~%s;([A-Z]{2})~mi', $country);
		
		if (!preg_match($regExp, $content, $matches))
		{
			return $country;
		}
		
		return strtolower($matches[1]);
	}
	
	private function _transformState($state)
	{
		$state = str_replace('.', '', $state);
		$url = 'http://www.50states.com/abbreviations.htm';
		$content = file_get_contents($url);
		$dom = new Zephyr_Dom($content);
		$node = $dom->query(sprintf('//td/a[text() = "%s"]/ancestor::td/following-sibling::td', ucfirst($state)))->current();
		
		if (!$node)
		{
			return $state;
		}
		
		return strtolower($node->getText());
	}
	
	private function _findCommonContractions($city)
	{
		$find = array('Ont', 'Mass');
		$replace = array('Ontario', 'Massachusetts');
		
		return str_replace($find, $replace, $city);
	}
	
	private function _clean($value)
	{
		$value = preg_replace('~\d+$~i', null, $value);
		
		return trim($value);
	}
}