<?php

class Zephyr_Dom_Package
{
	private $_list;
	
	/**
	 * Package all of the DOM* classes into their Zephyr equivalents, so that we can 
	 * continue to extend DOMDocument.
	 *
	 * @param DOMNodeList $nodes
	 * @throws Exception
	 * @return Zephyr_Dom_List
	 */
	public function __construct(DOMNodeList $nodes, DOMDocument $dom)
	{
		# Create the list that we'll populate.
		$list = new Zephyr_Dom_List();
		
		# Loop through all of the nodes, injecting each one into Zephyr_Dom_List.
		foreach ($nodes as $node)
		{
			# Converts things like DOMElement into Zephyr_Dom_Element.
			$className	= get_class($node);
			$className	= str_replace('DOM', '', $className);
			$className 	= sprintf('Zephyr_Dom_%s', $className);
			
			# If this class does not exist, then throw an exception.
			if (!class_exists($className))
			{
				throw new Exception('Cannot find DOM class: ' . $className);
			}
			
			# Package the DOM class into a special Zephyr class representing the DOM.
			$class = new $className($node);
			
			$class->injectDom($dom);
			$list->add($class);
		}

		$this->_list = $list;
	}
	
	public function getItems()
	{
		return $this->_list;
	}
}