<?php
require_once dirname(__FILE__) . '/bootstrap.php';

class Zephyr_RequestTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		Zephyr_Request::$defaultCachePath = TEST_PATH . '/data/cache/';
		Zephyr_Request::$defaultCookiePath = TEST_PATH . '/data/cookies/';
	}
	
	public function testGetResponseWithoutCache()
	{
		$request = new Zephyr_Request('http://www.google.com');
		$request->setUserAgent(null);
		$request->disableCache();
		$request->disableCookies();
		$request->deleteCache();
		
		$response = $request->getResponse();
		
		$this->assertTrue($response->isSuccessful());
		$this->assertFalse($request->fromCache());
		$this->assertFalse($request->hasCacheFile());
		$this->assertFalse($response->isError());
	}
	
	public function testGetResponseWithCache()
	{
		$request = new Zephyr_Request('http://www.google.com');
		$request->setUserAgent(null);
		$request->disableCookies();
		$request->deleteCache();
		
		$response = $request->getResponse();
		
		$this->assertTrue($response->isSuccessful());
		$this->assertFalse($request->fromCache());
		$this->assertTrue($request->hasCacheFile());
		$this->assertFalse($response->isError());
		
		$response = $request->getResponse();
		
		$this->assertTrue($response->isSuccessful());
		$this->assertTrue($request->fromCache());
		$this->assertTrue($request->hasCacheFile());
		$this->assertFalse($response->isError());
		
		$request->deleteCache();
	}
	
	public function testDeleteCacheActuallyWorks()
	{
		$request = new Zephyr_Request('http://www.google.com');
		$request->setUserAgent(null);
		$request->disableCookies();
		
		$response = $request->getResponse();
		$response = $request->getResponse();
		
		$this->assertTrue($request->hasCacheFile());
		
		$request->deleteCache();
		
		$this->assertFalse($request->hasCacheFile());
	}

	public function testCookiesAreCreatedAndDeletedCorrectly()
	{
		$request = new Zephyr_Request('http://www.talkphp.com');
		$request->setUserAgent(null);
		$request->disableCache();
		
		$response = $request->getResponse();
		
		$this->assertTrue($response->isSuccessful());
		$this->assertFalse($request->fromCache());
		$this->assertFalse($request->hasCacheFile());
		$this->assertFalse($response->isError());
		$this->assertTrue($request->deleteCookie());
	}
	
	public function testCacheIsCreatedAndDeletedCorrectly()
	{
		$request = new Zephyr_Request('http://www.talkphp.com');
		$request->setUserAgent(null);
		
		$response = $request->getResponse();
		
		$this->assertTrue($response->isSuccessful());
		$this->assertTrue($request->hasCacheFile());
		$this->assertFalse($response->isError());
		$this->assertTrue($request->deleteCookie());
		$this->assertTrue($request->deleteCache());
	}
}

