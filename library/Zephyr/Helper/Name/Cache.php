<?php

class Zephyr_Helper_Name_Cache extends Zephyr_Helper_Name_Variation_Abstract
{
	public static $cachePath; 
	
	public static function isCached($name)
	{
		return (bool) (self::loadFromCache($name) !== false);
	}
	
	# where is this called?  can only be something to do with that place
	public static function save($name, Zephyr_Helper_Name_Item_Name $item)
	{
		self::_saveToCache($name, $item);
	}
	
	private static function _saveToCache($name, $item)
	{
		# wher eis this called then?
		$key = md5($name);
		$cache = self::getCache();
		$cache->save(serialize($item), $key);
		return true;
	}
	
	public static function loadFromCache($name)
	{
		$key = md5($name);
		$cache = self::getCache();
		
		if ($return = $cache->load($key))
		{
			return unserialize($return);
		}
	
		return false;
	}
	
	public static function getCache()
	{
		if (!self::$cachePath)
		{
			throw new Zephyr_Exception('You must set a cache path for the name helper');
		}
		
		return Zephyr_CacheFactory::getNameHelperCache(self::$cachePath);
	}
}