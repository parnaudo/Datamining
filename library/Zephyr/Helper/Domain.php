<?php

class Zephyr_Helper_Domain
{
	public function filterSubDomains($url)
	{
		$parts	= explode('.', $url);
		$parts	= array_reverse($parts);
		$domain	= array();
		$tlds = self::getTlds();

		foreach ($parts as $part)
		{
			if (in_array(strtoupper($part), $tlds))
			{
				$domain[] = $part;
				continue;
			}
			
			$domain[] = $part;
			break;
		}

		return implode('.', array_reverse($domain));
	}
	
	public static function getTlds()
	{
		$filepath = dirname(dirname(__FILE__)) . '/_data/tlds.txt';
		$content 	= file_get_contents($filepath);
		$list		= explode("\r\n", $content);
		return $list;
	}
}