<?php 

/**
 * Defines the headers and export properties for an export document.
 * 
 * @author Karl
 */
class Zephyr_Export_Template
{
	private $_model;
	
	public function __construct($model = null)
	{
		if ($model)
		{
			$this->setProperties($model);
		}
	}
	
	/**
	 * Set an array of properties to export.
	 */
	public function setProperties($model)
	{
		foreach ($model->toArray() as $name => $value)
		{
			$properties[] = $name;
		}
		$this->_properties = $properties;
	}
	
	/**
	 * Return the column name from a property name.
	 */
	protected function getColumnFromProperty($property)
	{
		return $property;
	}

	/**
	 * Returns the column names (headers) to be used in this export.
	 */
	public function getColumns()
	{
		$properties = $this->getProperties();
		$columns = array_map(array($this, 'getColumnFromProperty'), $properties);
		return $columns;
	}

	/**
	 * Return an array of properties to be exported from the model.
	 */
	public function getProperties()
	{
		if (empty($this->_properties))
		{
			throw new Zephyr_Exception('No properties found in export template.');
		}
		return $this->_properties;
	}
}