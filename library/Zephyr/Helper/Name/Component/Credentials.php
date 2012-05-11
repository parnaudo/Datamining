<?php

class Zephyr_Helper_Name_Component_Credentials extends Zephyr_Helper_Name_Component
{
	/**
	 * Process the credentials.
	 *
	 * @param string $name
	 */
	protected function _process($name)
	{
		$unsureCredentials	= array('DO', 'J', 'B');
		$credentials 		= array();
		$credentialsDb		= new Zephyr_Helper_Name_Database_Credentials();
		
		while (preg_match('~\s+([^\s]+[\.]?)$~i', $name, $matches))
		{
			# Trim it.
			$credential = trim($matches[1]);
			
			# Try and fetch the credential from the database.
			if (!$credentialsDb->fetch($credential))
			{
				preg_match_all('~[A-Z]{1}~', $credential, $matches);
				
				# If there is only one or less uppercase letters in the string, then then in all likeliness
				# it isn't a credential at all, otherwise we'd be asking for every single surname.
				if (count($matches[0]) <= 1 && strpos($credential, '.') === false)
				{
					break;
				}
				
				$name	= str_replace($credential, null, $name);
				$parts	= explode(' ', $name);
				$parts	= array_filter($parts, 'strlen');
				
				# probably not a credential at all.
				if (count($parts) == 1)
				{
					break;
				}
				
				# If the string length of the credential exceeds 10, then it's not a credential.
				if (strlen($credential) > 10)
				{
					break;
				}
				
				# Prompt the user as to whether or not the current credential is valid.
				if (Zephyr_Prompt::confirm(sprintf('Is "%s" a valid credential in "%s"?', $credential, $this->_name)) != 'y')
				{
					break;
				}
				
				$credentialsDb->add($credential);
			}
			
			$credentials[] = $credential;
				
			# Replace the name with what we found.
			$name 	= preg_replace(sprintf('~%s$~', preg_quote($credential)), '', trim($name));
			$name	= trim($name);
		}
		
		# Since some credentials look like surnames -- mostly for Japanese/Chinese names, if we're
		# left with a name that's considered too short, then try to remove any unsure credentials.
		foreach ($unsureCredentials as $unsureCredential)
		{
			$regExp				= sprintf('~\s+%s\s+~i', $unsureCredential);
			$unsureCredential 	= preg_grep($regExp, $credentials);
			
			# If there is no unsure credential in the name, then we can break from this loop.
			if (!$unsureCredential)
			{
				break;
			}
			
			# If the name is 6 characters or less, and we found an unsure credential in the list of
			# credentials extracted, then possible the unsure credential is really the individual's
			# last name.
			if (strlen($name) <= 8 && count($unsureCredential))
			{
				# If the name has no lower-case values, and the extracted credential is all upper-case,
				# then it's a good assumption to make that the credential is actually a credential, despite
				# what we previously thought.
				if (!preg_match('~[a-z]~', $name) && preg_match('~[A-Z]~', $unsureCredential[0]))
				{
					$message = sprintf('"%1$s" is a valid credential, and since "%2$s" has no lower-case characters, this credential will remain as one.', $unsureCredential[0], $name);
					Zephyr_Helper_Name_Abstract::addAssumption($message);
				
					break;
				}
				
				$message = sprintf('"%1$s" is a valid credential, but the remaining name "%2$s" is 8 characters or less, therefore the credential is a part of the name.', $unsureCredential[0], $name);
				Zephyr_Helper_Name_Abstract::addAssumption($message);
				
				# Place the credential back into the name.
				$name .= ' ' . $credential;
				
				# Unset this item from the credentials.
				unset($credentials[count($credentials) - 1]);
			}
		}
		
		# Return the items.
		$this->_name = $this->_standardiseName($name);
		$this->_item->setCredentials($credentials);
	}
	
	/**
	 *
	 * @return array
	 */
	private function _getCredentials()
	{
		
//		# Sort the array so that the longest ones appear at the top, which means that none of the
//		# small ones will take out a piece of the string, when a longer one would have matched it, 
//		# but didn't get the chance to since it was snatched by a short one - sí?
//		usort($credentials, function($a, $b)
//		{
//		    return strlen($b) - strlen($a);
//		});
//		
//		$credentials = array_unique($credentials);
//		return array_chunk($credentials, 50);
	}
}