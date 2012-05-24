<?php

class Zephyr_Pdf_StrangeDots
{
	private $_dom;
	
	private function _xpath($xpathExpression)
	{
		
	}
	
	public function toArray($xmlDocument)
	{
		$fields = $this->_parse($xmlDocument);
		$fields	= $this->_setKeys($fields);
		
		return $fields;
	}
	
	private function _setKeys(array $fields)
	{
		$fields = $this->_removeThoseWithoutEnoughColumns($fields);
		$header = array_shift($fields);
		
		foreach ($fields as $rowIndex => $columns)
		{
			foreach ($columns as $colIndex => $column)
			{
				$key	= preg_replace('~[^A-Z]~i', '', $header[$colIndex]);
				$label	= $key;
				$index	= 1;
				
				while (array_key_exists($key, $columns))
				{
					$key = $label . ++$index;
				}
				
				$columns[$key]	= $column;
				unset($columns[$colIndex]);
			}
			
			$fields[] = $columns;
			unset($fields[$rowIndex]);
		}
		
		$fields = array_values($fields);
		
		return $fields;
	}
	
	private function _removeThoseWithoutEnoughColumns(array $fields)
	{
		$columnCounts = array();
		
		foreach ($fields as $columns)
		{
			$number = count($columns);
			
			if (array_key_exists($number, $columnCounts))
			{
				$columnCounts[$number]++;
				continue;
			}
		
			$columnCounts[$number] = 1;
		}
		
		$columnCounts		= array_flip($columnCounts);
		$expectedColumns 	= max($columnCounts);
		
		foreach ($fields as $index => $columns)
		{
			if (count($columns) != $expectedColumns)
			{
				unset($fields[$index]);
			}
		}
		
		$fields	= array_values($fields);
		$header	= $fields[0];
		
		foreach ($fields as $index => $columns)
		{
			if ($columns == $header && $index != 0)
			{
				unset($fields[$index]);
			}
		}
		
		return $fields;
	}
	
	private function _parse($xmlDocument)
	{
		$fields		= array();
		$content	= file_get_contents($xmlDocument);
		
		$dom	= new DOMDocument();
		@$dom->loadHTML($content);
		
		$xpath	= new DOMXPath($dom);
		$rows	= $xpath->query('//row');
		
		foreach ($rows as $index=> $row)
		{
			$dom		= new DOMDocument();
			@$dom->loadHTML($this->_getHtml($row));
			
			$xpath		= new DOMXPath($dom);
			$columns	= $xpath->query('//cell');
			
			$items		= array();
			
			foreach ($columns as $i => $column)
			{
				$dom		= new DOMDocument();
				@$dom->loadHTML($this->_getHtml($column));
				
				$xpath		= new DOMXPath($dom);
				$values		= $xpath->query('//font');
				
				if (is_null($values->item(0)))
				{
					$content = $column->nodeValue;
				}
				else
				{
					$content = null;
					
					foreach ($values as $value)
					{
						$content .= trim($value->nodeValue) . ' ';
					}
				}
				
				$content = str_replace('T00:00:00.000', '', $content);
				$items[] = preg_replace('~\s{1,}~i', ' ', $content);
			}
			
			$fields[] = $items;
		}
		
		return $fields;
	}
	
	private function _getHtml($node)
	{
		if (!$node) 
		{
			return '';
		}
		
		if (!count($node->childNodes))
		{
			return $node->nodeValue;
		}
	
		$dom = new DOMDocument();
	
		foreach($node->childNodes as $child)
		{
			$dom->appendChild($dom->importNode($child, true));
		}
	
		return $dom->saveHTML();
	}
}