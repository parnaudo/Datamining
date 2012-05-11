<?php

/**
 * 
 * @author Adam
 */
class Zephyr_Dom_Text extends Zephyr_Dom_Abstract
{
	/**
	 * Get the value of the node.
	 *
	 * @return string
	 */
	public function getText()
	{
		return $this->_item->wholeText;
	}
}