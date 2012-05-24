<?php 

class Zephyr_Pdf
{
	protected $_service;
	
	private function __construct()
	{
		
	}
	
	public function setService($service)
	{
		$this->_service = $service;
	}
	
	public function toArray($xmlDocument)
	{
		return $this->_service->toArray($xmlDocument);
	}
	
	public static function factory($service)
	{
		$serviceInstance = null;
		
		switch (strtolower($service))
		{
			case 'nitro';
				$serviceInstance = new Zephyr_Pdf_Nitro();
				break;
				
			case 'pdf2xml';
				$serviceInstance = new Zephyr_Pdf_PDF2XML();
				break;
				
			default:
				throw new Zephyr_Exception('Unknown PDF service');
				break;
		}
		
		$pdf = new self();
		$pdf->setService($serviceInstance);
		return $pdf;
	}
}