<?php

/**
 * 
 * @author Adam
 */
class Zephyr_Dom_Attr extends Zephyr_Dom_Abstract
{
	/**
	 * Get the value of the node.
	 *
	 * @return string
	 */
	public function getText()
	{
		return $this->_item->value;
	}
}