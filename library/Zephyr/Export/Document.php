<?php 

/**
 * The base class of a document that can be used for exporting.
 * All export documents will be derived from this class.
 * 
 * @author Karl
 */
abstract class Zephyr_Export_Document
{
	private $_headers = array();
	private $_exported = array();
	private $_template;
	private $_filePath;
	
	public function __construct($filePath)
	{
		$this->_filePath = $filePath;
	}
	
	public function setHeaders(array $headers)
	{
		return $this->_headers = $headers;
	}
	
	public function getHeaders()
	{
		if ($this->hasTemplate())
		{
			return $this->getTemplate()->getColumns();
		}

		return $this->_headers;
	}
	
	# File related methods

	/**
	 * Determines if this document exists on the file system.
	 */
	public function exists()
	{
		return file_exists($this->getFilePath());
	}
	
	/**
	 * Returns the file page to the document on the file system.
	 */
	public function getFilePath()
	{
		return $this->_filePath;
	}
	
	# Export related methods
	
	/**
	 * Outputs the headers to the document.
	 */
	abstract public function exportHeaders();
	
	/**
	 * Exports an array of models and outputs them into the document.
	 */
	abstract public function exportModels(array $models);
	
	/**
	 * Exports a single model and outputs it into the document.
	 */
	abstract public function exportModel($model);
	
	/**
	 * Determines if the specified model has been exported.
	 */
	protected function hasExported($model)
	{
		if (in_array($model->getIdent(), $this->_exported))
		{
			return true;
		}
		return false;
	}

	/**
	 * Registers an export so we can track what has been exported.
	 */
	protected function registerExport($model)
	{
		$this->_exported[] = $model->getIdent();
	}
	
	# Util methods
	
	/**
	 * Sets the document template to be used for export.
	 */
	public function setTemplate($template)
	{
		$this->_template = $template;
	}

	/**
	 * Determines if this document has a template set.
	 */
	public function hasTemplate()
	{
		return $this->_template;
	}
	
	/**
	 * Returns the template associated with this document.
	 */
	public function getTemplate()
	{
		if (!$this->_template)
		{
			throw new Zephyr_Exception('You must set a template before exporting');
		}
	
		return $this->_template;
	}

	/**
	 * Parses a model and returns an arrray of data to export,
	 * with the data structured in the same order as the headers.
	 */
	protected function getRowFromModel($model)
	{
		$row = array();
	
		if ($this->hasTemplate())
		{
			foreach ($this->getTemplate()->getProperties() as $property)
			{
				if (isset($model->$property))
				{
					$value = $model->$property;
				
					# remove line breaks and replace with a whitespace
					$value = str_replace("\r\n", '', $value);
					
					# Replace multple spaces with a single space
					$value = preg_replace('~\s+~', ' ', $value);
					
					# Replace double quotes with a 2 double quotes
					$value = str_replace('"', '""', $value);
					
					$row[] = $value;
				}
				else
				{
					$row[] = '';
				}
			}
		}
		else
		{
			foreach ($model->toArray() as $property => $value)
			{
				$value = $model->$property;
				
				# remove line breaks and replace with a whitespace
				$value = str_replace("\r\n", '', $value);
				
				# Replace multple spaces with a single space
				$value = preg_replace('~\s+~', ' ', $value);
				
				# Replace double quotes with a 2 double quotes
				$value = str_replace('"', '""', $value);
				
				$row[] = $value;
			}
		}
		
	
		return $row;
	}
}