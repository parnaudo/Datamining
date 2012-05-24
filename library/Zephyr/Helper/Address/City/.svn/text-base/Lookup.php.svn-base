<?php 

class Zephyr_Helper_Address_City_Lookup
{
	private function _getWikipediaLinksFromGoogle($place)
	{
		$query = "site:wikipedia.org $place (city OR town OR village OR census-designated)";
		$url = "http://www.google.co.uk/search?q=" . urlencode($query);
	
		$request = new Zephyr_Request($url);
		$response = $request->processRequest();
	
		$dom = new Zephyr_Dom($response);
	
		$results = $dom->query('//cite[contains(text(), "wikipedia.org")]/ancestor::div[@class="s"]/preceding-sibling::h3/a/@href');
		$links = array();
	
		foreach ($results as $link)
		{
			$parts = parse_url('http://www.google.com' . $link->getText());
			parse_str($parts['query'], $parts);
			$link = $parts['q'];
			if (!preg_match('~wiki/[^/]+(,|$)~', $link))
			{
				continue;
			}
			if (preg_match('~.(jpg|png|bmp|gif)$~i', $link))
			{
				continue;
			}
			$links[] = $link;
		}

		return $links;
	}
	
	private function _getPlaceFromWikipedia($url, $place)
	{
		$request = new Zephyr_Request($url);
		$dom = new Zephyr_Dom($request->getResponse());
	
		$geographyBox = $dom->query('//table[contains(@class, "geography")]');
	
		if (!$geographyBox->count())
		{
			return array('url' => $url, 'error' => 'Does not have a geography table.');
		}
	
		$content = $dom->query('//div[@class="mw-content-ltr"]');
		$content = $content->current();
		$name = $dom->query('//h1');
		$name = $name->current()->getText();
		$name = explode(',', $name);
		$name = $name[0];
		$name = preg_replace('~\(.*?\)~', '', $name);
		$name = trim($name, "\t\r\n ,.");
	
		if (!preg_match("~$place~i", $name) && (soundex($name) != soundex($place)))
		{
			return array('url' => $url, 'error' => 'Place cannot be found in the name.');
		}

		# We use the actual name here, not the place name, and check if this place is a city, town, etc.
		if (!preg_match("~(?:^|\s|\b)$name\s[^.]+(city|township|town|village|suburb|census-designated place)~is", $content->getText(), $matches))
		{
			return array('url' => $url, 'error' => 'Does not appear to be a place.');
		}
	
		$type = $matches[1];
	
		return array(
				'name' => $name,
				'type' => $type);
	}
	
	public function getMatches($place)
	{
		$results = array();
		
		foreach ($this->_getWikipediaLinksFromGoogle($place) as $link)
		{
			$result = $this->_getPlaceFromWikipedia($link, $place);
		
			if (array_key_exists('error', $result))
			{
				continue;
			}
		
			$results[] = $result;
		}
		
		return $results;
	}
	
	public function getExactMatch($place)
	{
		$results = array();
		
		foreach ($this->_getWikipediaLinksFromGoogle($place) as $link)
		{
			$result = $this->_getPlaceFromWikipedia($link, $place);
		
			if (array_key_exists('error', $result))
			{
				continue;
			}
		
			if (strtolower($result['name']) == strtolower($place))
			{
				return $place;
			}
		}
		
		return false;
	}
}