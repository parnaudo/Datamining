<?php

class Zephyr_Helper_Name_Component_Positions extends Zephyr_Helper_Name_Component
{
	/**
	 * Process the positions.
	 *
	 * @param string $name
	 */
	protected function _process($name)
	{
		$positions	= array();
		
		# Construct the regular expression.
		$regExp = sprintf('~(%s)~i', implode('|', $this->_getPositions()));
		
		# Attempt to find the known positions in the name.
		if (preg_match_all($regExp, $name, $matches))
		{
			# Loop through all of the positions that were discovered.
			foreach ($matches[1] as $matchedPosition)
			{
				# Add the position to the array.
				$positions[] = $matchedPosition;
				
				# Remove the position from the name.
				$name = str_replace($matchedPosition, '', $name);
			}
		}
		
		# Return the items.
		$this->_name = $this->_standardiseName($name);
		$this->_item->setPositions($positions);
	}
	
	/**
	 *
	 * @return array
	 */
	private function _getPositions()
	{
		return array('Assistant Professor', 'Consultant Psychiatrist', 'Professor', 'Prof', 'Psychiatrist', 'Consultant', 'Operating Officer', 'Assoc Prof', 'Assoc. Prof', 'Psycho', 'Psych', 'Phil', 'Philosophy', 'Psychology', 'Stud', 'Student');
	}
}