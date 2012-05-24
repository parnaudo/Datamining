<?php 

/**
 * Parses a name and provides methods for accessing its attributes.
 * 
 * @author Adam
 */
abstract class Zephyr_Helper_Name_Abstract
{
	/**
	 * Contains the original name value for debugging purposes.
	 *
	 * @var string
	 */
	protected $_name;
	
	/**
	 * The item that is returned to the frontend.
	 *
	 * @var Zephyr_Helper_Name_Item_Name
	 */
	protected $_item;
	
	/**
	 * Contains any assumptions made in parsing the name.
	 *
	 * @param array
	 */
	private static $_assumptions = array();
	
	/**
	 * Whether or not to mess with the ordering if it's deemed necessary.
	 *
	 */
	const RETAIN_ORDER = 1;
	
	public function __construct($name, $flags = false)
	{
		$this->_name = trim($name);
		self::clearAssumptions();
		
		# Create the item that will be returned -- we will start to populate it straight away.
		$this->_item = new Zephyr_Helper_Name_Item_Name();
		
//		var_dump(Zephyr_Helper_Name_Cache::isCached($name));
		
		# If the current name is cached, then we can return the data.
		if (Zephyr_Helper_Name::$enableCaching == true && Zephyr_Helper_Name_Cache::isCached($name))
		{
			$this->_process($name);
			return;
		}
		
		Zephyr_Output::debugStatic('Processing Name: ' . $this->_name);
		
		# Normalise the name to remove any anomalies.
		$standardise 	= new Zephyr_Helper_Name_Normalise_Default($name);
		$name			= $standardise->getName();
		
		# Extract the suffixes from the name, and then get the filtered name.
		$suffixes		= new Zephyr_Helper_Name_Component_Suffixes($name, $this->_item);
		$name			= $suffixes->getName();
		
		# Normalise the suspected credentials only.
//		$standardise 	= new Zephyr_Helper_Name_Normalise_SuspectedCredentials($name);
//		$name			= $standardise->getName();
		
		# Extract the credentials from the name, and then get the filtered name.
		$credentials	= new Zephyr_Helper_Name_Component_Credentials($name, $this->_item);
		$name			= $credentials->getName();
		
		# Extract the positions from the name, and get the filtered name in return.
		$positions		= new Zephyr_Helper_Name_Component_Positions($name, $this->_item);
		$name			= $positions->getName();
		
		# Normalise the characters that remain.
		$standardise 	= new Zephyr_Helper_Name_Normalise_Characters($name);
		$name			= $standardise->getName();
		
		# Extract the nickname from the name, and then get the filtered name.
		$nickname		= new Zephyr_Helper_Name_Component_Nickname($name, $this->_item);
		$name			= $nickname->getName();

		if (!$flags)
		{
			# Check whether a surname is at the beginning of the string, and move it to the end if so.
			$lastName		= new Zephyr_Helper_Name_Component_LastName($name, $this->_item);
			$name			= $lastName->getName();
		}
		
		# Try and extract any credentials that weren't extracted before.
//		$nickname		= new Zephyr_Helper_Name_Component_UnlistedCredentials($name, $this->_item);
//		$name			= $nickname->getName();
		
		# Normalise the spaces to remove any anomalies.
		$standardise 	= new Zephyr_Helper_Name_Normalise_Spaces($name);
		$name			= $standardise->getName();
		
		# Process the name based on the specific class that was first called.
		$this->_process($name);
	}
	
	/**
	 * Returns the individual items for the name.
	 *
	 * @param string $method
	 * @param array $arguments
	 * @return Zephyr_Helper_Name_Item_Name
	 */
	public function __call($method, $arguments)
	{
		if (!method_exists($this->_item, $method))
		{
			print_r(debug_print_backtrace());
			throw new Exception('Unable to call method: ' . $method);
		}
		
		return $this->_item->$method();
	}
	
	/**
	 * Throw an exception when an error occurs, so that we don't continue if something uncertain occurred.
	 *
	 * @param int $errorNumber
	 * @param string $errorMessage
	 * @param string $errorFile
	 * @param int $errorLine
	 * @throws ErrorException
	 */
	public function throwError($errorNumber, $errorMessage, $errorFile, $errorLine)
	{
		$message = sprintf('Problem in Parsing "%s" ("%s" on Line %d - %s)', $this->_name, $errorMessage, $errorLine, $errorFile);
		
		# We don't want any pesky DOMDocument related errors.
		if (stripos($message, 'DOMDocument') !== false)
		{
			return;
		}
		
		throw new ErrorException($message, 0, $errorNumber, $errorFile, $errorLine);
	}
	
	public static function clearAssumptions()
	{
		self::$_assumptions = array();
	}
	
	/**
	 * Add an assumption made in the parsing of the current name.
	 *
	 * @param string $message
	 */
	public static function addAssumption($message)
	{
		self::$_assumptions[] = $message;
	}
	
	/**
	 * Get the assumptions made by the parsing.
	 *
	 * @return array
	 */
	public static function getAssumptions()
	{
		return self::$_assumptions;
	}
}