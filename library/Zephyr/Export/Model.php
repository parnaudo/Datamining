<?php 

/**
 * Models a row in the export document.
 * 
 * @author Karl
 */
class Zephyr_Export_Model extends Zephyr_Model
{
	public function getIdent()
	{
		return md5(implode('', $this->toArray()));
	}
}