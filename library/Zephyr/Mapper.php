<?php

class Zephyr_Mapper
{
	/**
	 * @var Zend_Db_Table
	 */
	public $table;
	
	protected $_tableClass;
	protected $_modelClass = 'Zephyr_Model';
	protected $_collectionClass = 'Zephyr_Collection';
	
	public function __construct()
	{
		$this->table = new $this->_tableClass;
	}
	
	public function loadMany($result)
	{
		$collection = $this->createCollection();
		foreach ($result as $row)
		{
			$collection[] = $this->createModel($row);
		}
		return $collection;
	}
	
	public function createCollection()
	{
		return new $this->_collectionClass;
	}
	
	public function createModel($data = array())
	{
		if ($data === false || is_null($data))
		{
			return null;
		}
		
		$model = new $this->_modelClass;
		
		if ($data instanceof Zend_Db_Table_Row)
		{
			$model->fromArray($data->toArray());
		}
		elseif (($data instanceof Iterator) || 
				(is_array($data)))
		{
			$model->fromArray($data);
		}
		
		return $model;
	}
	
	public function save(Zephyr_Model $model)
	{
		$cols = $this->table->info(Zend_Db_Table_Abstract::COLS);
		
		$filtered = array();
		$data = $model->toArray();
		
		foreach ($cols as $col)
		{
			!array_key_exists($col, $data) || $filtered[$col] =  $data[$col];
		}
		
		if (!isset($model->id))
		{
			$model->id = $this->table->insert($filtered);
		}
		else
		{
			$where = $this->table->getAdapter()->quoteInto('id = ?', $model->id, Zend_Db::INT_TYPE);
			$this->table->update($filtered, $where);
		}
	}

    public function delete(Zephyr_Model $model)
    {
        $this->table->delete('id = ' . $model->id);
    }
}