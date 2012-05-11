<?php 

/**
 * Adapter for exporting models to a csv document.
 * 
 * @author Karl
 */
class Zephyr_Export_Document_Csv extends Zephyr_Export_Document
{
	/**
	 * Returns a resource the the output file.
	 */
	public function getFile($mode)
	{
		if (!$file = fopen($this->getFilePath(), $mode))
		{
			throw new Zephyr_Exception('Unable to open output file for writing.');
		}
		
		return $file;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Zephyr_Export_Document::exportHeaders()
	 */
	public function exportHeaders()
	{
		$file = $this->getFile('w+');
		
		fputcsv($file, $this->getHeaders());
		
		fclose($file);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Zephyr_Export_Document::exportModels()
	 */
	public function exportModels(array $models)
	{
		$file = $this->getFile('a+');
		
		foreach ($models as $model)
		{
			if ($this->hasExported($model)) continue;
			
			$this->_exportLine($file, $this->getRowFromModel($model));
			
			$this->registerExport($model);
		}
		
		fclose($file);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Zephyr_Export_Document::exportModel()
	 */
	public function exportModel($model)
	{
		if ($this->hasExported($model)) return false;
		
		$file = $this->getFile('a+');

		$this->_exportLine($file, $this->getRowFromModel($model));
		
		$this->registerExport($model);
		
		fclose($file);
		return true;
	}
	
	/**
	 * Outputs a single line in to the csv document.
	 */
	private function _exportLine($file, $data)
	{
		$data = array_map(function($value) { return sprintf('"%s"', $value); }, $data);
		
		$data = implode(',', $data);
		$data = $data . "\n";
		fwrite($file, $data);
	}
}