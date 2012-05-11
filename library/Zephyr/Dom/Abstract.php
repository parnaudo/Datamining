<?php

/**
 * 
 * @author Adam
 */
abstract class Zephyr_Dom_Abstract
{
	protected $_item;
	protected $_dom;
	
	/**
	 * All child nodes must implement this method.
	 *
	 */
	public abstract function getText();
	
	/**
	 * Takes whatever DOM* class was passed into it and assigns it to the variable.
	 *
	 * @param mixed $item
	 */
	public function __construct($item)
	{
		$this->_item = $item; 
	}
	
	/**
	 * Get the content of the node without stripping its HTML away.
	 *
	 * @param DOMElement $node
	 * @return string
	 */
	protected function _toHtml(DOMElement $node)
	{
		# If there are no child nodes, then we might as well return the current value.
		if (!count($node->childNodes))
		{
			return $node->nodeValue;
		}
	
		# Otherwise we will create a new DOMDocument, and then invoke the saveHTML() method.
		$dom = new DOMDocument();
	
		# Append all of the current child's nodes into the new DOMDocument.
		foreach($node->childNodes as $child)
		{
			$dom->appendChild($dom->importNode($child, true));
		}
	
		# And then return its HTML.
		return $dom->saveHTML();
	}
	
	public function injectDom(DOMDocument $dom)
	{
		$this->_dom = $dom;
	}
}