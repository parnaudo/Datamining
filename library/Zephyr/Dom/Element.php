<?php

/**
 * 
 * @author Adam
 */
class Zephyr_Dom_Element extends Zephyr_Dom_Abstract
{
	/**
	 * Get the value of the node.
	 *
	 * @return string
	 */
	public function getText()
	{
		return $this->_item->nodeValue;
	}
	
	/**
	 * Returns a HTML formatted value of the node.
	 *
	 * @return unknown
	 */
	public function getHtml()
	{
		return $this->_toHtml($this->_item);
	}
	
	/**
	 * Get the name of the node.
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->_item->nodeName;
	}
	
	/**
	 * Get the attribute's value based on its name.
	 *
	 * @param string $name
	 * @return string
	 */
	public function getAttribute($name)
	{
		return $this->_item->getAttribute($name);
	}
	
	/**
	 * Query the remaining HTML.
	 *
	 * @param string $expression
	 * @return array
	 */
	public function query($expression)
	{
		# Any child XPath expressions mustn't be prepended with "//", but it's a common mistake, so
		# remove them.  Do the same with /?
		$expression = str_replace('//', '', $expression);
		#$expression = str_replace('/', '', $expression);
		
		# Find the nodes based on the expression that was passed in.
		$xpath 		= new DOMXPath($this->_dom);
		$nodes		= $xpath->query($expression, $this->_item);
		
		# Package all of the obtained nodes into their Zephyr_Dom equivalents.
		$package = new Zephyr_Dom_Package($nodes, $this->_dom);
		return $package->getItems();
	}
}