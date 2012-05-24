<?php 

/**
 * Models a single HTTP request to a given URL.
 * 
 * @author Karl & Adam
 */
ini_set('memory_limit', '268435456');

class Zephyr_Request
{
	public static $defaultCachePath;
	public static $defaultCookiePath;
	
	private $_url;
	private $_method;
	private $_params;
	private $_referer;
	private $_headers = array();
	private $_proxy = array();
	private $_cacheFileName;
	private $_fromCache = false;
	
	private $_cookiesEnabled = true;
	private $_cacheEnabled = true;
	
	private $_userAgent = 'Googlebot/2.1 (+http://www.google.com/bot.html)';
	
	/*
	 * A static variable to keep track of the last request URL.  Providing
	 * a very simple "history" of the last URL visited.  Note, this is the
	 * effective URL (after any redirects) and therefore might not match
	 * the original request URL.
	 */
	private static $_lastRequestUrl;
	
	public function __construct($url, $method = 'get', array $params = array())
	{
		$this->setUrl($url);
		$this->_method 	= $method;
		$this->_params 	= $params;
	}

	public function getReferer() { return $this->_referer; }
	
	# -----------------------------------------------------------------------------------------
	# Common request methods
	# -----------------------------------------------------------------------------------------
	
	/**
	 * Returns the URL passed in to the construct, this may not contain the GET query.
	 * If you need the URL to contain the GET query you should use getRequestUrl().
	 */
	public function getUrl()
	{
		return $this->_url;
	}
	
	/**
	 * Allows the user to set the URL to be used in the request.
	 */
	public function setUrl($url)
	{
		if (empty($url))
		{
			throw new Zephyr_Exception('URL is not valid.');
		}
	
		$this->_url = $url;
	}
	
	/**
	 * Returns the full request URL, including the GET query.
	 */
	public function getRequestUrl()
	{
		if ($this->_method == 'post' || !count($this->_params))
		{
			return $this->_url;
		}
	
		$url = $this->_url;
		$hasQuery = strpos($url, '?');
		$glue = $hasQuery ? '&' : '?';
		$query = http_build_query($this->_params);
		return $url . $glue . $query;
	}
	
	/**
	 * Returns any params specified which have been/will be used in the request
	 */
	public function getParams()
	{
		return $this->_params;
	}
	
	# -----------------------------------------------------------------------------------------
	# Advanced (not-so-common) request methods
	# -----------------------------------------------------------------------------------------
	
	/**
	 * Allow user to overwrite the user agent.  This is useful
	 * for scraping Google because you can't have the user agent
	 * set to GoogleBot (our default) for Google.  Setting it to
	 * null is sufficient - which defaults to CURLs default user agent, 
	 * but you could also provide another user agent to use.
	 *  
	 * @param unknown_type $userAgent
	 */
	public function setUserAgent($userAgent)
	{
		$this->_userAgent = $userAgent;
	}
	
	/**
	 * Add a single HTTP header to the request.
	 */
	public function addHttpHeader($header)
	{
		$this->_headers[] = $header;
	}
	
	/**
	 * Returns the referer to use for this request
	 */
	private function _getReferer()
	{
		if ($this->_referer)
		{
			return $this->_referer;
		}
	
		return self::$_lastRequestUrl;
	}
	
	/**
	 * Allows the user to specify a proxy to be used for this request.
	 *
	 * @author Adam
	 */
	public function setProxy($ip, $port, $username = null, $password = null)
	{
		$this->_proxy = array
		(
				'ip' 		=> $ip,
				'port' 		=> $port,
				'username' 	=> $username,
				'password'	=> $password
		);
	}
	
	/**
	 * Allows the user to specify a referer to be used for this request.
	 */
	public function setReferer($referer)
	{
		$this->_referer = $referer;
	}
	
	# -----------------------------------------------------------------------------------------
	# Cookie related methods
	# -----------------------------------------------------------------------------------------
	
	public function disableCookies()
	{
		$this->_cookiesEnabled = false;
	}
	
	/**
	 * Returns the complete path to the related cookie file, including the file name and extension.
	 */
	private function _getCookiePath()
	{
		$cookiePath = self::$defaultCookiePath;
		
		# Throw an exception when the user forgets to specify a cookie path
		if (!$cookiePath)
		{
			throw new Zephyr_Exception_Request('No default cookie path.');
		}
		
		# Split the URL into parts
		$urlParts = parse_url($this->_url);
	
		# Try to create the cookie directory
		if (!file_exists($cookiePath) && !mkdir($cookiePath, 0777))
		{
			throw new Zephyr_Exception_Request('Unable to create directory for cookies.');
		}
		
		# Grab the domain helper
		$domainHelper = new Zephyr_Helper_Domain();
		
		# Filter out any subdomains from the domain
		$label = $domainHelper->filterSubDomains($urlParts['host']);
		
		# TODO: add a filterTld to the domainHelper class and remove this hack.
		# Hack: Force any cookies relating to Google to be saved into the same cookie file. 
		strpos($label, 'google') === false || $label = 'google';
		
		return sprintf('%s/%s.txt', $cookiePath, $label);
	}
	
	/**
	 * Deletes the cookie file related to this request.
	 */
	public function deleteCookie()
	{
		$cookieFile = $this->_getCookiePath();
		return @unlink($cookieFile);
	}
	
	# -----------------------------------------------------------------------------------------
	# Cache related methods
	# -----------------------------------------------------------------------------------------
	
	public function disableCache()
	{
		$this->_cacheEnabled = false;
	}
	
	/**
	 * Returns a Zend_Cache object
	 */
	public function getCache()
	{
		return Zephyr_CacheFactory::getRequestCache($this->getCachePath());
	}
	
	/**
	 * Returns the path where cache files will be created
	 */
	public function getCachePath()
	{
		$cachePath = self::$defaultCachePath;
	
		# Throw an exception when the user forgets to specify a cache path
		if (!$cachePath)
		{
			throw new Zephyr_Exception_Request('No default cache path.');
		}
		
		# Try to create the default cache directory
		if (!file_exists($cachePath) && !mkdir($cachePath, 0777))
		{
			throw new Zephyr_Exception_Request('Unable to find/create the cache directory.');
		}
	
		# Split the URL into parts
		$urlParts = parse_url($this->_url);
		
		# Concatenate the catch path with the hostname
		$cachePath = sprintf('%s/%s', $cachePath, $urlParts['host']);
		
		# Try to create the cache directory
		if (!file_exists($cachePath) && !mkdir($cachePath, 0777))
		{
			throw new Zephyr_Exception_Request('Unable to create sub-directory inside the cache directory.');
		}
		
		return $cachePath;
	}

	/**
	 * Allows the user to set a new name for the cache file.
	 */
	public function setCacheFileName($cacheFileName)
	{
		$this->_cacheFileName = $cacheFileName;
	}

	/**
	 * Returns the name for the cache file, including the extension.
	 */
	private function _getCacheFileName()
	{
		if ($this->_cacheFileName)
		{
			return $this->_cacheFileName;
		}
		
		$this->_cacheFileName = $this->_url . '/' . $this->_method . '/' . http_build_query($this->_params);
		$this->_cacheFileName = md5($this->_cacheFileName);
		return $this->_cacheFileName;
		$this->_cacheFileName = preg_replace('~https?://~', '', $this->_cacheFileName);
		$this->_cacheFileName = str_replace('/', '_', $this->_cacheFileName);
		$this->_cacheFileName = preg_replace('~[^A-Za-z0-9_]+~', '', $this->_cacheFileName);
		return $this->_cacheFileName;
	}
	
	/**
	* Determines if a cache file exists for this request.
	*/
	public function hasCacheFile()
	{
		return $this->getCache()->test($this->_getCacheFileName()) !== false;
	}
	
	/**
	* Deletes the cache file related to this request.
	*/
	public function deleteCache()
	{
		return $this->getCache()->remove($this->_getCacheFileName());
	}
	
	/**
	 * Determines if the response came from the cache.
	 */
	public function fromCache()
	{
		return (bool) $this->_fromCache;
	}

	# -----------------------------------------------------------------------------------------
	# Helper methods
	# -----------------------------------------------------------------------------------------
	
	/**
	 * Returns the captcha text for a given image URL.
	 *
	 * @author Adam
	 */
	public function solveCaptcha($imageUrl)
	{
		# Clone the current object, and take a note the referer.
		$referer 	= $this->getRequestUrl();
		$request	= clone $this;
		
		# Set then necessary variables.
		$request->setUrl($imageUrl);
		$request->setReferer($referer);
		
		# Create a temp file of the image URL passed in.
		$image		= tempnam(sys_get_temp_dir(), 'img');
		file_put_contents($image, $request->getResponse()->getBody());
		
		# Uploading the picture to the server.
		$curl		= curl_init('http://www.strangedots.com/decaptyou/upload.php');
		$params		= array('picture' => "@{$image}");
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
		$output = curl_exec($curl);
		
		# Waiting for a response.
		do { $text = @file_get_contents($output); sleep(2); } while (!$text);
		
		return $text;
	}
	
	# -----------------------------------------------------------------------------------------
	# Core request/response processing methods
	# -----------------------------------------------------------------------------------------
	
	/**
	 * Returns response, from cache if available.
	 */
	public function getResponse()
	{
		$this->_fromCache = false;
	
		if (!$this->_cacheEnabled)
		{
			return $this->_processRequest();
		}
	
		if ($this->hasCacheFile())
		{
			$response = $this->getCache()->load($this->_getCacheFileName());
				
			$this->_fromCache = true;
		}
		else
		{
			$response = $this->_processRequest();
				
			$this->getCache()->save($response, $this->_getCacheFileName());
		}
	
		return $response;
	}
	
	/**
	 * Processes the request and returns the response, this method does not use the cache.
	 */
	private function _processRequest()
	{
		$this->_fromCache = false;
	
		Zephyr_Output::debugStatic('Request %s...', $this->getRequestUrl());
	
		$adapter = new Zend_Http_Client_Adapter_Curl();
		$client = new Zend_Http_Client($this->_url);
		$client->setAdapter($adapter);
		$adapter->setCurlOption(CURLOPT_RETURNTRANSFER, true);
		$adapter->setCurlOption(CURLOPT_FOLLOWLOCATION, true);
	
		if (count($this->_headers))
		{
			$client->setHeaders($this->_headers);
		}
	
		# If the request is a POST request
		if ($this->_method == 'post')
		{
			$client->setMethod(Zend_Http_Client::POST);
			!count($this->_params) || $client->setParameterPost($this->_params);
		}
		# Else this is a GET request
		else
		{
			count($this->_params) || $client->setParameterGet($this->_params);
		}
	
		# If TOR is enabled
		if (Zephyr_Tor::getInstance()->isEnabled())
		{
			# Configure CURL to utilise the TOR proxy
			$adapter->setCurlOption(CURLOPT_PROXY, sprintf('%s:%d', Zephyr_Tor::getInstance()->getAddress(), Zephyr_Tor::getInstance()->getPort()));
			$adapter->setCurlOption(CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
		}
	
		# If we have a referer
		if ($referer = $this->_getReferer())
		{
			$adapter->setCurlOption(CURLOPT_REFERER, $referer);
		}
	
		# If we have a proxy server
		if (array_key_exists('ip', $this->_proxy))
		{
			$adapter->setCurlOption(CURLOPT_PROXY, sprintf('%s:%s', $this->_proxy['ip'], $this->_proxy['port']));
	
			if ($this->_proxy['username'])
			{
				$adapter->setCurlOption(CURLOPT_PROXYUSERPWD, sprintf('%s:%s', $this->_proxy['username'], $this->_proxy['password']));
			}
		}
	
		# When querying Google the user agent must be disabled or changed.
		if ($this->_userAgent)
		{
			$adapter->setCurlOption(CURLOPT_USERAGENT, $this->_userAgent);
		}
	
		# When accessing content from Google cache cookies must be disabled.
		if ($this->_cookiesEnabled)
		{
			$adapter->setCurlOption(CURLOPT_COOKIEFILE, $this->_getCookiePath());
			$adapter->setCurlOption(CURLOPT_COOKIEJAR, $this->_getCookiePath());
		}
	
		# Allow self certified SSL
		$adapter->setCurlOption(CURLOPT_SSL_VERIFYHOST, false);
		$adapter->setCurlOption(CURLOPT_SSL_VERIFYPEER, false);
	
		# Process the request and return the response
		$response = $client->request();
	
		self::$_lastRequestUrl = curl_getinfo($adapter->getHandle(), CURLINFO_EFFECTIVE_URL);
	
		return $response;
	}
}