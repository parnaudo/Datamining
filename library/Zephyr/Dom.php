<?php

/**
 * 
 * @author Adam
 */
class Zephyr_Dom
{
	/**
	 * Holds a reference to the DOMDocument, so that it can be used in such methods
	 * as the query method.
	 *
	 * @var DOMDocument
	 */
	private $_dom;
	
	/**
	 * Stores the content type (HTML/XML) so that when the query() is called again,
	 * it can be obtained automatically, instead of having to supply it again manually.
	 *
	 * @var string
	 */
	private $_contentType;
	
	/**
	 * Holds the content type, because when we begin to string XPath expressions together
	 * (by calling the query method in Zephyr_Dom_Element) a missing DOCTYPE will
	 * not parse the file correctly, therefore this DOCTYPE will be prepended to the data.
	 *
	 * @var string
	 */
	private $_docType;
	
	/**
	 * Place the HTML into the DOMDocument class.
	 *
	 * @param string $content
	 */
	public function __construct($content, $type = 'html')
	{
		# Load the HTML into DOMDocument.
		$this->_dom = new DOMDocument();
		
		# Load the content into the member variable.
		$this->_content = $content;
		
		# Store the content type in the member variable.
		$this->_contentType = $type;
		
		# Switch the type so that we can load the content in different ways.
		switch ($type)
		{
			# Load a HTML document.
			case ('html'):
				@$this->_dom->loadHtml(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
				break;
				
			# Load an XML document.
			case ('xml'):
				@$this->_dom->loadXML($content);
				break;
		}
		
		# Try and get the DOCTYPE from the content that was passed in, and store it in the
		# member variable if it can be gathered.
		if (preg_match('~(<!DOCTYPE .+?">)~i', $content, $matches))
		{
			$this->_docType = $matches[1];
		}
	}
	
	/**
	 * Perform an XPath query on the current DOMDocument.
	 *
	 * @param string $expression
	 * @param DOMElement $context
	 * @param DOMDocument $document
	 * @return Zephyr_Dom_List
	 */
	public function query($expression, DOMElement $context = null)
	{
		# Find the nodes based on the expression that was passed in.
		$xpath 		= new DOMXPath($this->_dom);
		$nodes		= $xpath->query($expression);

		# Package all of the obtained nodes into their Zephyr_Dom equivalents.
		$package = new Zephyr_Dom_Package($nodes, $this->_dom);
		return $package->getItems();
	}

	/**
	 * Factory method for performing an xpath query on a dom and returning a node list.
	 */
	public static function queryStatic($query, $context)
	{
		$dom = new self($context);
		return $dom->query($query);
	}
}