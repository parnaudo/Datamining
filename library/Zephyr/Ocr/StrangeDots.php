<?php 

/**
 * Proves access to the StrangeDots (which is actually Zephyr's because they own the code)
 * OCR web service.
 * 
 * @author Karl
 */
class Zephyr_Ocr_StrangeDots
{
	/**
	 * Uses the StrangeDots OCR web service to process an image and return the text.
	 */
	public function getText($image)
	{
		$request = new Zephyr_Request('http://www.strangedots.com/tesseract/?image=' . $image);
		$data = @json_decode($request->getResponse(), true);
		
		if (is_null($data))
		{
			return null;
		}
		
		return $data['email'];
	}
}