<?php

/**
 * 
 * @author Adam & Karl
 */
class Zephyr_Dom_List implements Iterator, ArrayAccess, Countable
{
	/**
	 * Keeps a track of the current position in the array.
	 *
	 * @var unknown_type
	 */
	private $_position = 0;
	
	/**
	 * Contains a list of the items.
	 *
	 * @var unknown_type
	 */
	private $_list = array();
	
	/**
	 * Inject an item into the array.
	 *
	 * @param mixed $item
	 */
	public function add($item)
	{
		$this->_list[] = $item;
	}

	/**
	 * Reset to the beginning of the array.
	 *
	 */
    function rewind()
    {
        $this->_position = 0;
    }

    /**
     * Fetch the current array item.
     *
     * @return mixed
     */
    function current()
    {
        return @$this->_list[$this->_position];
    }

    /**
     * Return the current index.
     *
     * @return integer
     */
    function key()
    {
        return $this->_position;
    }

    /**
     * Move the array index forward one.
     *
     */
    function next()
    {
        ++$this->_position;
    }

    /**
     * Checks whether the current index is a valid one or not.
     *
     * @return boolean
     */
    function valid()
    {
        return (bool) isset($this->_list[$this->_position]);
    }

    /**
     * Check whether an offset exists.
     *
     * @param integer $index
     * @return boolean
     */
	public function offsetExists($index)
	{
		return (bool) array_key_exists($index, $this->_list);
	}

	/**
	 * Get the value at a particular offset.
	 *
	 * @param integer $index
	 * @return mixed
	 */
	public function offsetGet($index)
	{
		return $this->_list[$index];
	}

	/**
	 * Set the value at a particular offset.
	 *
	 * @param integer $index
	 * @param string $value
	 * @return Zephyr_Dom_List
	 */
	public function offsetSet($index, $value)
	{
		$this->_list[$index] = $value;
		return $this;
	}

	/**
	 * Unset an the specified index.
	 *
	 * @param integer $index
	 * @return Zephyr_Dom_List
	 */
	public function offsetUnset($index)
	{
		unset($this->_list[$index]);
		return $this;
	}

	/**
	 * Coount the items in the list
	 *
	 * @return integer
	 */
	public function count()
	{
		return count($this->_list);
	}
}