<?php 

class Zephyr_Helper_Encoding_Utf8 implements Zephyr_Helper_Encoding_Decorator
{
	public function fix($text)
	{
		$encoding = mb_detect_encoding($text);
		if ($encoding != 'UTF-8')
		{
			$text = mb_convert_encoding($text, 'UTF-8', $encoding);
		}
		return $text;
	}
}