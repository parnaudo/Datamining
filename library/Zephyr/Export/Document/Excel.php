<?php 

/**
 * Adapter for exporting models to an Excel document.
 * 
 * @author Karl
 */
class Zephyr_Export_Document_Excel extends Zephyr_Export_Document
{
	private $_excel;
	
	public function __construct($filePath)
	{
		parent::__construct($filePath);
		
		require_once 'PHPExcel.php';
		$this->_excel = new PHPExcel();
	}
	
	/**
	 * Adds a new sheet to the excel document.
	 */
	public function addSheet($name)
	{
		# If the active sheet is called Worksheet then this is the default
		# worksheet so rather than adding one, simply rename this one.
		if ($this->_excel->getActiveSheet()->getTitle() == 'Worksheet')
		{
			$this->_excel->getActiveSheet()->setTitle($name);
		}
		# Otherwise, add a new sheet.
		else
		{
			$sheet = new PHPExcel_Worksheet($this->_excel, $name);
			$this->_excel->addSheet($sheet);
			$this->_excel->setActiveSheetIndexByName($name);
		}
	}
	 
	/**
	 * Save changes to the document.
	 */
	public function saveChanges()
	{
		$writer = PHPExcel_IOFactory::createWriter($this->_excel, 'Excel2007');
		$writer->save($this->getFilePath());
	}
	
	/**
	 * Retunrs a large array of letters to be used for column references.
	 * Excel references columns with a letter and number, such as A5.
	 */
	private function _getColumnLetters()
	{
		$columnLetters = range('A', 'Z');
		
		foreach (range('A', 'G') as $firstLetter)
		{
			foreach (range('A', 'Z') as $secondLetter)
			{
				$columnLetters[] = $firstLetter . $secondLetter;
			}
		}
		
		return $columnLetters;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Zephyr_Export_Document::exportHeaders()
	 */
	public function exportHeaders()
	{
		$columnLetters = $this->_getColumnLetters();
		
		foreach ($this->getHeaders() as $columnNumber => $columnText)
		{
			$columnLetter = array_shift($columnLetters);
			
			$this->_excel->getActiveSheet()->setCellValue($columnLetter . '1', $columnText);
		}
		
		$this->saveChanges();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Zephyr_Export_Document::exportModels()
	 */
	public function exportModels(array $models)
	{
		$rowNumber = 2;
		
		foreach ($models as $model)
		{
			if ($this->hasExported($model)) continue;
			
			$columnLetters = $this->_getColumnLetters();
			
			$cells = $this->getRowFromModel($model);
			
			if (empty($cells)) continue;
			
			foreach ($cells as $columnNumber => $value)
			{
				$columnLetter = array_shift($columnLetters);
				
				$value =  iconv('ISO-8859-1', 'UTF-8//TRANSLIT', $value);
				
				$this->_excel->getActiveSheet()->setCellValue($columnLetter . $rowNumber, $value);
			}
			
			$this->registerExport($model);
			
			$rowNumber++;
		}
		
		$this->saveChanges();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Zephyr_Export_Document::exportModel()
	 */
	public function exportModel($model)
	{
		throw new Zephyr_Exception('Currently not supported');
	}
}