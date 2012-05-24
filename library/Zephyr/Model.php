<?php 

/**
 * A basic "model" class for modeling data.
 * 
 * @author Karl
 */
class Zephyr_Model
{
	protected $_magicProperties = array();

	public function fromArray(array $data)
	{
		foreach ($data as $key => $value)
		{
			$this->$key = $value;
		}
		
		return $this;
	}	
	
	public function toArray()
	{
		return $this->_magicProperties;
	}
	
	public function __get($key)
	{
		if (!array_key_exists($key, $this->_magicProperties))
		{
			throw new Exception("Tried to access undefined property '$key'");
		}
		
		return $this->_magicProperties[$key];
	}
	
	public function __set($key, $value)
	{
		$this->_magicProperties[$key] = $value;
		return $this;
	}
	
	public function __isset($key)
	{
		return array_key_exists($key, $this->_magicProperties);
	}
}