<?php 

/**
 * Provides a centralised mechanism for handling application output.
 * All output methods can be used with sprintf like directives,
 * for example: Zephyr_Output::logStatic('Something happened on %s', date('Y-m-d'));
 * 
 * @author Karl
 */
class Zephyr_Output
{
	# Enable debug output?
	public static $debug = false;

    private static $_rawEscaped = true;

	private static $_singleton;
	
	private function __constuct() 
	{
		# Intentionally empty		
	}

	/**
	 * Output a message, prepend a timestamp and append a newline.
	 */
	public function log($message)
	{
		# Create a timestamp to prepend to each line
		$timestamp = sprintf("[%s] ", date('H:i:s'));
	
		# Grab method arguments
		$args = func_get_args();
	
		# Prepend the timestamp to the message
		$args[0] = $timestamp . $args[0] . "\n";
	
		# Print the message 
		if (count($args) > 1)
		{
			call_user_func_array('printf', $args);
		}
		else
		{
			echo $args[0];
		}
	
		return $this;
	}
	
	/**
	 * Output a message without any modification. 
	 */
	public function raw($message)
	{
		$args = func_get_args();
		
		if (count($args) > 1)
		{
			call_user_func_array('printf', $args);
		}
		else
		{
			echo $args[0];
		}

		return $this;
	}
	
	/**
	 * If in debug mode, output a message, prepend a timestamp and append a newline.
	 * If not in debug mode, output nothing.
	 */
	public function debug($message)
	{
		if (self::$debug)
		{
			return call_user_func_array(array($this, 'log'), func_get_args());
		}
		
		return $this;
	}
	
	/**
	 * Output an exception.
	 */
	public function exception(Exception $exception)
	{
		if (!self::$debug)
		{
			$this->log('Error Occurred: %s', $exception->getMessage());
			return $this;	
		}
		
		$this->raw("\nException Occurred:\n\n");
		$this->raw("Message:\t%s\n", $exception->getMessage());
		$this->raw("File:\t\t%s\n", $exception->getFile());
		$this->raw("Line:\t\t%s\n", $exception->getLine());
		##die(print_r($exception->getTrace(), true));
		return $this;
	}
	
	# Static wrapper methods
	
	public static function logStatic($message)
	{
        if (!self::$_rawEscaped)
        {
            self::rawStatic("\n");
        }

		$output = self::getSingleton();
		return call_user_func_array(array($output, 'log'), func_get_args());
	}

	public static function rawStatic($message)
	{
        self::$_rawEscaped = (bool) (substr($message, -1)=="\n");

		$output = self::getSingleton();
		return call_user_func_array(array($output, 'raw'), func_get_args());
	}
	
	public static function debugStatic($message)
	{
		$output = self::getSingleton();
		return call_user_func_array(array($output, 'debug'), func_get_args());
	}
	
	public static function exceptionStatic(Exception $exception)
	{
		$output = self::getSingleton();
		return call_user_func_array(array($output, 'exception'), func_get_args());
	}
	
	# Singleton pattern
	
	public static function getSingleton() 
	{
		if (!self::$_singleton)
		{
			self::$_singleton = new self();
		}
		
		return self::$_singleton;
	}
}