<?php 

/**
 * Models the TOR service installed on the local server.
 * Comments added by Karl.
 *  
 * @author Adam
 */
class Zephyr_Tor
{
	private static $_instance;
	
	private $_enabled;
	
	private $_torAddress		= '127.0.0.1';
	private $_torPort 			= 9050;
	private $_torControlPort 	= 9051;
	private $_torPassword 		= '0vodkah6';
	private $_lastIdentityCreated;
	
	private function __construct()
	{
		$this->_enabled = false;
	}
	
	/**
	 * Returns the singleton that represents the TOR serive.
	 * 
	 * @return Zephyr_Tor
	 */
	public static function getInstance()
	{
		if (!isset(self::$_instance))
		{
			self::$_instance = new self();
		}
		
		return self::$_instance;
	}
	
	/**
	 * Enable the TOR service.
	 */
	public function enable()
	{
		Zephyr_Output::debugStatic('TOR enabled.');
		
		$this->_enabled = true;
	}
	
	/**
	 * Disable the TOR service.
	 */
	public function disable()
	{
		Zephyr_Output::debugStatic('TOR disabled.');
		
		$this->_enabled = false;
	}

	public function toggle()
	{
		return $this->_enabled ? $this->disable() : $this->enable();
	}
	
	/**
	 * Set the TOR service address.
	 */
	public function setAddress($ipAddress)
	{
		$this->_torAddress = $ipAddress;
	}
	
	/**
	 * Return the TOR service address.
	 */
	public function getAddress()
	{
		return $this->_torAddress;
	}
	
	/**
	 * Set the TOR service port.
	 */
	public function setPort($portNumber)
	{
		$this->_torPort = (int) $portNumber;
	}
	
	/**
	 * Return the TOR service port.
	 */
	public function getPort()
	{
		return $this->_torPort;
	}
	
	/**
	 * Set the TOR service control port.
	 */
	public function setControlPort($portNumber)
	{
		$this->_torControlPort = (int) $portNumber;
	}
	
	/**
	 * Get the TOR service control port.
	 */
	public function getControlPort()
	{
		return $this->_torControlPort;
	}
	
	/**
	 * Set the TOR service password.
	 */
	public function setPassword($password)
	{
		$this->_torPassword = $password;
	}
	
	/**
	 * Return the TOR service address.
	 */
	public function getPassword()
	{
		return $this->_torPassword;
	}
	
	/**
	 * Determines if the TOR service is enabled for fututre Zephyr_Request's.
	 */
	public function isEnabled()
	{
		return (bool) $this->_enabled;
	}
	
	/**
	 * Creates a new TOR identity.
	 */
	public function newIdentity()
    {
    	if (!$this->_enabled)
    	{
    		return false;
    	}
    	
    	if ($this->isTooSoonForNewIdentity())
    	{
    		return false;
    	}
    	
        $fsock = fsockopen($this->getAddress(), $this->getControlPort(), $errorNumber, $errorMessage, 10);
        
        if (!$fsock)
        {
			return false;
		}

        fputs($fsock, sprintf('AUTHENTICATE "%s"', $this->_torPassword). "\r\n");
        $response = fread($fsock, 1024);
        
        list($code, $text) = explode(' ', $response, 2);
        
        if ($code != '250')
        {
            return false;
        }

        fputs($fsock, "signal NEWNYM\r\n");
        $response = fread($fsock, 1024);
        
        list($code, $text) = explode(' ', $response, 2);
        
        if ($code != '250')
        {
            return false;
        }
        
        fclose($fsock);
        
        Zephyr_Output::debugStatic('New identity created.');
        
        $this->_lastIdentityCreated = date('Y-m-d H:i:s');
        return true;
    }
    
    /**
     * Determines if it's too soon to create a new identity.
     * 
     * @author Karl
     */
    private function isTooSoonForNewIdentity()
    {
    	# If we've not created an idenity yet 
    	if (!$this->_lastIdentityCreated)
    	{
    		return false;
    	}
    	
    	# Add 30 seconds to the last time we created an idenitty
    	$date = new DateTime($this->_lastIdentityCreated);
		$date->add(new DateInterval('PT30S'));
		
		# Calculate the time remaining until we can created a new idenity
		$timeRemaining = $date->getTimestamp() - time();

		return ($timeRemaining > 0);
    }
}