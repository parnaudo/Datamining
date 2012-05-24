<?php 

/**
 * A gateway and adapter for the OCR classes.
 * 
 * @author Karl
 */
class Zephyr_Ocr
{
	private $_service;
	
	private function __construct()
	{
		# Intentionally empty
	}
	
	/**
	 * Set the service to use for future OCR requests.
	 */
	public function setService($service)
	{
		$this->_service = $service;
	}
	
	/**
	 * Returns the text from an image path, using the registered OCR service.
	 */
	public function getText($imagePath)
	{
		return $this->_service->getText($imagePath);
	}
	
	/**
	 * Creates instances of Zephyr_Ocr configured for a specified service. 
	 */
	public static function factory($service)
	{
		$serviceInstance = null;
		
		switch (strtolower($service))
		{
			case 'strangedots';
				$serviceInstance = new Zephyr_Ocr_StrangeDots();
				break;
				
			default:
				throw new Zephyr_Exception('Unknown OCR service');
				break;
		}
		
		$ocr = new self();
		$ocr->setService($serviceInstance);
		return $ocr;
	}
}