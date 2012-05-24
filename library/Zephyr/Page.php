<?php 

/**
 * Simple class for modelling a page based on a URL.
 * 
 * @author Karl
 */
class Zephyr_Page extends Zephyr_Model
{
	/**
	 * Create a new page, optionally passing in a URL.
	 * Feel free to overload this construct method, but be warned
	 * that by doing so you will have to supply $this->url
	 * manually or overload process() and handle your own request process.
	 */
	public function __construct()
	{
		# Get any params passed into this method
		$params = func_get_args();
	
		# Grab the first param
		$firstParam = array_shift($params);
		
		# If we have a first param, and it's a string, and also looks like a URL...
		if ($firstParam && is_string($firstParam) && strpos($firstParam, 'http') === 0)
		{
			# Then we assume the user passed in a URL.
			# I'm not a lover of this type of "cleverness" because it's usually
			# confusing for the end-user.  However, this is non-instrusive
			# because it utilises the magic properties of the Zephyr_Model
			$this->url = $firstParam;
		}
		
		# Enable a default request manager
		$this->requestManager = new Zephyr_Request_Manager();
	}
	
	/**
	 * Get the content for this page (the body).
	 * 
	 * @return string
	 */
	public function getContent()
	{
		return Zephyr_ZendResponseFix::getBody($this->getResponse());
	}
	
	public function getResponse()
	{
		if (!isset($this->response))
		{
			$this->response = $this->process();
		}

		return $this->response;
	}
	
	/**
	 * Factory method for creating a new request object
	 * using the url, params, and method set on this page.
	 */
	public function createRequest()
	{
		# Determine method: post or get
		$method = isset($this->method) ? $this->method : 'get';
		
		# Get the params to pass along with this request
		$params = isset($this->params) ? $this->params : array();
		
		# Create the request object
		return new Zephyr_Request($this->url, $method, $params);
	}
	
	protected function _preRequest()
	{
	}
	
	/**
	 * Process the page and return a response.
	 * 
	 * @return Zend_Response
	 */
	public function process()
	{
		$this->request = $this->createRequest();

		# Provide a hook before we send the request, this allows users
		# to modify the request object in subclasses without having to
		# overload the whole process method.
		$this->_preRequest();
	
		# Process the request using a request manager if required
		if (isset($this->requestManager))
		{
			$response = $this->requestManager->getResponse($this->request);
		}
		# Else, just process the request directly
		else
		{
			$response = $this->request->getResponse(); 
		}
	
		# Return the response
		return $response;
	}
	
	/**
	 * Perform a query on this page, or the specified context (text).
	 */
	public function query($query, $context = null)
	{
		# Determine the context of this query
		$context || $context = $this->getContent();
	
		# Perform the XPATH query on the context
		return Zephyr_Dom::queryStatic($query, $context);
	}
}