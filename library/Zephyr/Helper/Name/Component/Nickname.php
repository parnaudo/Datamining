<?php

class Zephyr_Helper_Name_Component_Nickname extends Zephyr_Helper_Name_Component
{
	/**
	 * Process the nickname.
	 *
	 * @param string $name
	 */
	protected function _process($name)
	{
		$nickname = null;
		
		# Construct the pattern for detecting the nickname -- if any.
		$regExp = sprintf('~%1$s(.+?)%1$s~i', '[\[|\]|"|\(|\)]+');
		
		if (preg_match($regExp, $name, $matches))
		{
			$nickname 	= trim($matches[1]);
			$name		= str_replace($matches[0], '', $name);
		}
		
		# Return the items.
		$this->_name = $this->_standardiseName($name);
		$this->_item->setNickname($nickname);
	}
}