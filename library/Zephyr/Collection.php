<?php 

class Zephyr_Collection implements Iterator, ArrayAccess, Countable
{
	private $_position 	= 0;
    protected $_items 	= array();

    public function __construct(array $data = null) 
    {
    	$this->_items = array();
        $this->_position = 0;
        $this->_items = $data;
    }

    public function getItems()
    {
    	return $this->_items;
    }
    
    // Iterable
    
    public function &rewind() 
    {
        $this->_position = 0;
        return $this;
    }
    
    public function &current() 
    {
    	if (!count($this->_items))
    	{
    		return null;	
    	}
    	
    	if (!array_key_exists($this->_position, $this->_items))
    	{
    		$this->rewind();	
    	}
    	
    	return $this->_items[$this->_position];
    }

    public function setPosition($position)
    { 
    	$this->_position = $position;
    }
    
    public function key() 
    {
        return $this->_position;
    }

    public function next() 
    {
        ++$this->_position;
    }
    
    public function &each()
    {
    	return $this->_items[$this->_position++]; 
    }

    public function valid() 
    {
        return isset($this->_items[$this->_position]);
    }
    
    // Array Access
    
    public function offsetSet($offset, $value) 
    {
    	if (is_null($offset))
    	{
    		if (empty($this->_items))
    		{
    			$offset = 0;
    		}
    		else
    		{
    			$offset = count($this->_items);
    		}
    	}
    	
        $this->_items[$offset] = $value;
    }
    
    public function offsetExists($offset) 
    {
        return isset($this->_items[$offset]);
    }
    
    public function offsetUnset($offset) 
    {
        unset($this->_items[$offset]);
    }
    
    public function offsetGet($offset) 
    {
        return isset($this->_items[$offset]) ? $this->_items[$offset] : null;
    }
    
    // Countable
    
    public function count()
    {
    	return count($this->_items);
    }
    
    # Custom

    public function fromArray(array $data)
    {
    	$this->_items = $data;
    	return $this;
    }
}

?>