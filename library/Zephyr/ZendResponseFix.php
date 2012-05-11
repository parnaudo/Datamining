<?php 

# Fixes Zend_Response problems with this URL
# http://www.news-medical.net/news/20120326/Medtronic-announces-results-from-two-Symplicity-system-trials-on-treatment-resistant-hypertension.aspx?page=2
class Zephyr_ZendResponseFix
{
	public static function getBodyLength($response) 
	{
		$responseSize = strlen(@$response->getBody());
		if (!$responseSize)
		{
			$body = @$response->getBody();
			if (!strlen($body) && $response->getHeader('Content-encoding') == 'deflate')
			{
				$body = $response->getRawBody();
				$body = gzinflate($body);
				$body = trim($body, "\n\r\t ");
			}
			$responseSize = strlen($body);
		}
		return $responseSize;
	}
	
	public static function getBody($response) 
	{
		$body = @$response->getBody();
		if (!strlen($body) && $response->getHeader('Content-encoding') == 'deflate')
		{
			$body = $response->getRawBody();
			$body = gzinflate($body);
			$body = trim($body, "\n\r\t ");
		}
		return $body;
	}
}