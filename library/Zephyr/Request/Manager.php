<?php 

class Zephyr_Request_Manager
{
	private $_maxRetries = 3;
	protected $_sleepRange = array('min' => 5, 'max' => 7);
	
	/**
	 * Process the request and return the response - from cache if available.
	 */
	public function getResponse(Zephyr_Request $request)
	{
		for ($i = 0; $i < $this->_maxRetries; $i++)
		{
			if ($i)
			{
				Zephyr_Output::logStatic('Retrying request...');
			}
			
			try
			{
				$response = $request->getResponse();

                if ($this->_isValid($response, $request))
				{
					# If the cache has been deleted, we can assume that security,
					# such as captcha, has been bypassed and that we need to
					# re-request the data 
					if (!$request->hasCacheFile())
					{
						return $request->getResponse();
					}
					
					return $response;
				}
			}
			catch (Zend_Http_Client_Exception $e)
			{
				# The request failed.  Ignore these for now and let the request
				# manager handle this.
			}

			$this->_onFailure($request);
		}
		
		throw new Zephyr_Exception_Request_Failed('Request failed.');
	}

	/**
	 * Called when a request fails validation.
	 */
	protected function _onFailure(Zephyr_Request $request)
	{
		Zephyr_Output::logStatic('Request failed.');
		
		$request->deleteCache();
		
		Zephyr_Tor::getInstance()->newIdentity();
		
		$request->deleteCookie();
		
		$this->sleep();
	}
	
	/**
	 * Validates a response and determines if the request failed.
	 */
	protected function _isValid(Zend_Http_Response $response, Zephyr_Request $request)
	{
		if ($response->isError())
		{
			throw new Zephyr_Exception_Request_Failed($response->getStatus());
		}
		
		$responseSize = Zephyr_ZendResponseFix::getBodyLength($response);

		if (!$responseSize)
		{
			Zephyr_Output::debugStatic('Error: Empty response.');
			return false;
		}
		
		return true;
	}

	/**
	 * Foces the application to sleep for a random duration. 
	 * This simulates human behaviour.
	 */
	private function sleep()
	{
		$sleep = rand($this->_sleepRange['min'], $this->_sleepRange['max']);
		
		Zephyr_Output::logStatic('Sleeping for %d seconds...', $sleep);
			
		sleep($sleep);
	}
	
	/**
	 * Set the range that the application will sleep for between failed requests.
	 */
	public function setSleepRange($min, $max)
	{
		$this->_sleepRange = array('min' => $min, 'max' => $max);
	}
	
	/**
	 * Set the maximum number of attempts to try a request.
	 */
	public function setMaxRetries($maxRetries)
	{
		$this->_maxRetries = $maxRetries;
	}
}