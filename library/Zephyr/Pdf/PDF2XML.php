<?php

class Zephyr_Pdf_PDF2XML
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
		$rows	= $xpath->query('//row/entry[@colnum="1"]');
		
		foreach ($rows as $index => $row)
		{
			$row 			= $row->parentNode;
			$additionalRows	= array();
			$nextSibling	= $row;
			
			do
			{
				$nextSibling 	= $nextSibling->nextSibling;
				
				if (!$nextSibling)
				{
					continue;
				}
				
				$firstColumn	= $nextSibling->childNodes->item(0);
				
				if ($firstColumn->getAttribute('colnum') != 1)
				{
					$additionalRows[]	= $nextSibling;
				}
			}
			while ($nextSibling && $firstColumn->getAttribute('colnum') != 1);
			
			$dom		= new DOMDocument();
			@$dom->loadHTML($this->_getHtml($row));
			
			$xpath		= new DOMXPath($dom);
			$columns	= $xpath->query('//entry');
			
			$items		= array();
			
			foreach ($columns as $column)
			{
				$dom		= new DOMDocument();
				@$dom->loadHTML($this->_getHtml($column));
				
				$xpath		= new DOMXPath($dom);
				$value		= $xpath->query('//blockvalue')->item(0)->nodeValue;
				$colNum		= $column->getAttribute('colnum');
				
				$items[$colNum] = $value;
			}
			
			foreach ($additionalRows as $row)
			{
				foreach ($row->childNodes as $child)
				{
					$colNum	= $child->getAttribute('colnum');
					$value	= $child->childNodes->item(0)->childNodes->item(0)->nodeValue;
					
					if (!isset($items[$colNum]))
					{
						$items[$colNum] = trim($value);
						continue;
					}
					
					$items[$colNum] .= "\n" . trim($value);
				}
			}
			
			ksort($items);
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