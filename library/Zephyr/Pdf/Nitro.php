<?php

class Zephyr_Pdf_Nitro
{
	private $_dom;
	private $_ignoredRows = array();

	private function _xpath($xpathExpression)
	{

	}

	public function toArray($xmlDocument)
	{
		$fields = $this->_parse($xmlDocument);
		$fields	= $this->_setKeys($fields);

		$fields['Ignored'] = $this->_ignoredRows;
		
		return $fields;
	}

	private function _setKeys(array $fields)
	{
		$firstField = $fields[0][0];
		if (!$firstField)
		{
			unset($fields[0][0]);
			$fields[0] = array_values($fields[0]);
		}

		$secondField = $fields[1][0];
		if (!$secondField)
		{
			unset($fields[1][0]);
			$fields[1] = array_values($fields[1]);
		}

		$fields = $this->_removeThoseWithoutEnoughColumns($fields);
		$header = array_shift($fields);

		foreach ($fields as $rowIndex => $columns)
		{
			foreach ($columns as $colIndex => $column)
			{
				if (!is_int($colIndex))
				{
					continue;
				}
			
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

		arsort($columnCounts);
		$columnCounts		= array_flip($columnCounts);
		$expectedColumns	= array_shift($columnCounts);
		
		$quarter			= null;
		$year				= null;

		foreach ($fields as $index => &$columns)
		{
			if (count($columns) != $expectedColumns)
			{
				$text = implode(' ', $columns);
				
				if (preg_match('~(?P<quarter>First|Second|Third|Fourth) Quarter (?P<year>\d{4})~i',$text, $matches))
				{
					$labels		= array('First' => 1, 'Second' => 2, 'Third' => 3, 'Fourth' => 4);
					
					$quarter 	= (int) $labels[$matches['quarter']];
					$year		= $matches['year'];
					
					if (stripos($text, 'thru'))
					{
						$quarter = null;
					}
				}
				
				$this->_ignoredRows[$index] = $fields[$index];
				unset($fields[$index]);
				continue;
			}
			
			$columns['MetaYear'] 	= $year;
			$columns['MetaQuarter'] = $quarter;
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

			foreach ($columns as $column)
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
						$content .= trim($value->nodeValue) .  ' ';
					}
				}

				$content = str_replace('T00:00:00.000', '', $content);
				$content = str_replace('"', ' ', $content);
				$content = preg_replace('~ {1,}~i', ' ', $content);
				$content = trim($content);

				$items[] = $content;
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