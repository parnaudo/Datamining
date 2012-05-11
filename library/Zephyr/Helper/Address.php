<?php 

class Zephyr_Helper_Address
{
	public static $enableCaching = true;
	public static $enableUserInput = true;
	
	private $_address;
	private $_strategy;
	
	public function __construct($address)
	{
		$parser = new Zephyr_Helper_Address_Parser();
		
		# Allow the user to pass in arrays to support legacy code.
		if (is_array($address))
		{
			$address = implode(', ', $address);
		}
		
		# Filter the input string before using it
		$address = $this->_filterInput($address);
		
		# If we're not using the cache
		if (!self::$enableCaching)
		{
			# Parse the address directly
			$this->_address = $parser->parse($address);
			return;
		}

		# Create the cache key
		$key = md5($address);
			
		# Get the cache object
		$cache = self::getCache();
	
		# Try to load from the cache...
		if (!$this->_address = $cache->load($key))
		{
			# Parse address
			$this->_address = $parser->parse($address);

			# Save to cache
			$cache->save($this->_address, $key);
		}
	}
	
	private function _filterInput($text)
	{
		$text = trim($text, ' ,.');
		$text = rtrim($text, '-#/\\');
		return $text;
	}

	public function getMessages()
	{
		return $this->_address['messages'];
	}
	
	public function __toArray()
	{
		return $this->_address;
	}

	public function __call($method, $params)
	{
		$field = substr($method, 3);
		
		if (!array_key_exists($field, $this->_address))
		{
			throw new Zephyr_Exception("Invalid method '$method' called on address.");
		}
		
		return $this->_address[$field];
	}
	
	public static function getCache()
	{
		if (!Zend_Registry::isRegistered('addressCache'))
		{
			$cacheManager = Zend_Registry::get('cacheManager');
			$cache = $cacheManager->getCache('addressCache');
			Zend_Registry::set('addressCache', $cache);
		}
		return Zend_Registry::get('addressCache');
	}
}