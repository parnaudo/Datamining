<?php

class Zephyr_Helper_Name_Variation_Exception_ItalianSuffix extends Zephyr_Helper_Name_Variation_Abstract
{
	protected $_padAttributes		= false;
	protected $_characterRight		= '$';
	protected $_expectedAttributes 	= array
	(
		'oni', 'ssi', 'ari', 'chi', 'ano', 'cci', 'llo', 'lla', 'lli', 'one', 'lia', 'ardi', 'ano', 'tti', 'cco', 'zzi', 'eli', 'ome',
		'ore', 'rova', 'anova', 'enova', 'ini', 'ana', 'nte', 'oli', 'esi', 'uce', 'ale', 'oci', 'ghi', 'ssa', 'nso', 'nti', 'sso', 'ver', 'nzi', 'tto',
		'eri', 'rde', 'ere', 'ile', 'rni', 'ema', 'rbi', 'ola'
	);
	
	public function process()
	{
		$message = sprintf('"%1$s" has the suffix "%2$s" and is therefore an Italian name.', $this->_name, $this->_matched);
		Zephyr_Helper_Name_Abstract::addAssumption($message);
		
		if (preg_match('~(.+? .+?) (.+)~i', $this->_name, $matches))
		{
			$firstName 	= $matches[1];
			$lastName	= $matches[2];
			
			$this->_item->setFirstName($firstName);
			$this->_item->setLastName($lastName);
			
			return;
		}
		
		if (preg_match('~(.+?) (.+)~i', $this->_name, $matches))
		{
			$firstName 	= $matches[1];
			$lastName	= $matches[2];
			
			$this->_item->setFirstName($firstName);
			$this->_item->setLastName($lastName);
			
			return;
		}
	}
}