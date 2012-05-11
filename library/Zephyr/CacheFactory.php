<?php 

class Zephyr_CacheFactory
{
	public $requestCacheLifetime = 2629744;
	
	# Name helper cache
	
	private static function _createNameHelperCache($cachePath)
	{
		$oneMonth = 2629744;
	
		$frontendConfig = array(
				'lifetime' => $oneMonth,
				'automatic_serialization' => true
		);
	
		$backendConfig = array(
				'hashed_directory_level' => 1,
				'cache_dir' => $cachePath
		);
	
		return Zend_Cache::factory('Core', 'File', $frontendConfig, $backendConfig);
	}
	
	public static function getNameHelperCache($cachePath)
	{
		static $cache = null;
	
		if (!$cache)
		{
			$cache = self::_createRequestCache($cachePath);
		}
	
		return $cache;
	}
	
	# Request cache
	
	private static function _createRequestCache($cachePath)
	{
		$oneMonth = 2629744;
		
		$frontendConfig = array(
			'lifetime' => $oneMonth,
			'automatic_serialization' => true
		);
				
		$backendConfig = array(
			'hashed_directory_level' => 1,
			'cache_dir' => $cachePath
		);
		
		return Zend_Cache::factory('Core', 'File', $frontendConfig, $backendConfig);
	}
	
	public static function getRequestCache($cachePath)
	{
		static $cache = null;
		
		if (!$cache)
		{
			$cache = self::_createRequestCache($cachePath);
		}
		
		return $cache;
	}
}