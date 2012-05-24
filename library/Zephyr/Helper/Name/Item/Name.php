<?php

class Zephyr_Helper_Name_Item_Name
{
	private $_credentials;
	private $_suffixes;
	private $_positions;
	
	private $_nickname;
	
	private $_firstName;
	private $_middleName;
	private $_middleInitial;
	private $_lastName;
	
	/**
	 *
	 * @param array $credentials
	 */
	public function setCredentials(array $credentials)
	{
		$this->_credentials = $credentials;
	}
	
	public function addCredentials(array $credentials)
	{
		$credentials 		= array_merge($this->_credentials, $credentials);
		$this->_credentials	= $credentials;
	}
	
	/**
	 *
	 * @param array $suffixes
	 */
	public function setSuffixes(array $suffixes)
	{
		$this->_suffixes = $suffixes;
	}
	
	/**
	 * Set the positions.
	 *
	 * @param array $positions
	 */
	public function setPositions(array $positions)
	{
		$this->_positions = $positions;
	}
	
	/**
	 *
	 * @param string $nickname
	 */
	public function setNickname($nickname)
	{
		$nickname 	= $this->_trim($nickname);
		$nickname	= $this->_normaliseIfCapitalised($nickname);
		$nickname	= ucfirst($nickname);
		
		$this->_nickname = $nickname;
	}
	
	/**
	 *
	 * @param string $firstName
	 * @param boolean $allowSingleLetter
	 */
	public function setFirstName($firstName, $allowSingleLetter = false)
	{
		$firstName 	= $this->_trim($firstName);
		$firstName	= $this->_normaliseIfCapitalised($firstName);
		$firstName	= ucfirst($firstName);
		
//		if (!$allowSingleLetter && strlen($firstName) == 1)
//		{
//			$this->setMiddleInitial($firstName);
//			$firstName = null;
//			
//			if (!$this->getFirstName())
//			{
//				$this->setFirstName($this->getMiddleName());
//				$this->setMiddleName('');
//			}
//		}
		
		$this->_firstName = $firstName;
	}
	
	/**
	 *
	 * @param string $middleName
	 * @param boolean $allowSingleLetter
	 */
	public function setMiddleName($middleName, $allowSingleLetter = false)
	{
		$middleName = $this->_trim($middleName);
		$middleName	= $this->_normaliseIfCapitalised($middleName);
		$middleName = ucfirst($middleName);
		
		if (!$allowSingleLetter && strlen($middleName) == 1)
		{
			$this->setMiddleInitial($middleName);
			$this->_middle = null;
			return;
		}
		
		if (preg_match('~^[A-Z]{1} [A-Z]{1}$~', $middleName) && !$this->getMiddleInitial())
		{
			$this->setMiddleInitial($middleName);
			$this->_middleName = null;
			
			return;
		}
		
		$this->_middleName = $middleName;
	}
	
	/**
	 *
	 * @param string $middleInitial
	 */
	public function setMiddleInitial($middleInitial)
	{
		$middleInitial 			= $this->_trim($middleInitial);
		
		# Remove any of the usual nonsense from the middle initial, and leave us just A-Z. Sometimes though we'll
		# have two middle initials, which we'll want to separate with a space, so spaces are also acceptable.
		$middleInitial			= preg_replace('~[^A-Z\s]~', '', $middleInitial);
		
		# Convert the middle initial to uppercase.
		$this->_middleInitial 	= strtoupper($middleInitial);
	}
	
	/**
	 * 
	 * @param string $lastName
	 */
	public function setLastName($lastName)
	{
		$lastName			= $this->_trim($lastName);
		$lastName			= ucfirst($lastName);
		$lastName			= $this->_normaliseIfCapitalised($lastName);
		$lastName			= str_replace('.', '', $lastName);
		
		# Since connectives should always be displayed as lower-case, we'll need to convert those to lower-case.
		$connectives	= array('de las', 'van der', 'van de', 'von der', 'de los', 'de la', 'de', 'da', 'del', 'di', 'du', 'van', 'von', 'dela', 'li');
		$toLowerCase	= function($matches) { return strtolower($matches[0]); };
		$regExp			= sprintf('~(^|\s)+(%s)\s+~i', implode('|', $connectives));
		$lastName		= preg_replace_callback($regExp, $toLowerCase, $lastName);
		
		$this->_lastName 	= $lastName;
	}
	
	/**
	 *
	 * @return string
	 */
	public function getCredentials()
	{
		$credentials = $this->_credentials;
		
		# Don't process credentials if we don't actually have any.
		if (!$credentials)
		{
			return null;
		}
		
		# Remove any nonsense from the credentials, and leave us just A-Z characters, which we'll clean up below.
		$credentials	= preg_replace('~[^A-Z]~i', '', $credentials);
		
		# Convert all of the credentials to uppercase characters.
		$credentials	= array_map('strtoupper', $credentials);
		
		# These are the ones that should be replaced
		$find 			= array
		(
			'DSc', 'DLitt', 'DSc', 'EngD', 'EdD', 'DNursSci', 'RPh', 'BCh', 'PhD', 'BSPharm',
			'PharmD', 'MSc', 'DMSc', 'PsyD', 'DMus', 'ThD', 'DrPH', 'DPhil', 'PsyD', 'FRCPc',
			'PhDCLIN', 'MBChB', 'FRCSc', 'DrPH', 'MDiv', 'ScD', 'MSEd', 'MMSc', 'CTAGME',
			'MBBCh', 'PAC', 'MMSc', 'ACNPBC', 'ANPBC', 'APHNBC', 'BScN', 'BHSc', 'MRCPUK',
			'CATNP', 'CATNI', 'CDONALTC', 'CEFM', 'CNPT', 'COHNCM', 'COHNS', 'COHNSCM',
			'CRRNA', 'CSPI', 'DrNP', 'EdP', 'FNPC', 'FNPBC', 'MEd', 'MEMERGNSG', 'NEBC', 'AuD',
			'NEABC', 'NNPBC', 'PMHSNCBC', 'PMHNPBC', 'PNPBC', 'RNBC', 'RNCLRN', 'RNCMNN', 'MSci',
			'RNCNIC', 'RNCOB', 'SANEA', 'SANEP', 'TNCCI', 'TNCCP', 'WHNPBC', 'CCCSLP', 'MHSc', 'RAc',
			'MRCOGUK', 'MBE', 'MStJ', 'LPsy', 'Clin Psy', 'DrMed', 'DrPhil', 'BVSc', 'MDm', 'MMEd',
			'Dipl', 'MRCPsych', 'LAc'
		);
		
		# Replace the ones we find above with these.
		$replace		= array
		(
			'DSc', 'DLitt', 'DSc', 'EngD', 'EdD', 'DNursSci', 'RPh', 'BCh', 'PhD', 'BSPharm',
			'Pharm.D', 'MSc', 'DMSc', 'PsyD', 'DMus', 'ThD', 'DrPH', 'DPhil', 'PsyD', 'FRCP(c)',
			'PhD Clin', 'MBChB', 'FRCS(c)', 'DrPH', 'MDiv', 'ScD', 'MSEd', 'MMSc', 'C-TAGME',
			'MBBCh', 'PA-C', 'MMSc', 'ACNP-BC', 'ANP-BC', 'APHN-BC', 'BScN', 'BHSc', 'MRCP(UK)',
			'CATN-P', 'CATN-I', 'CDONA/LTC', 'C-EFM', 'C-NPT', 'COHN/CM', 'COHN-S', 'COHN-S/CM',
			'CRRN-A', 'C-SPI', 'DrNP', 'EdP', 'FNP-C', 'FNP-BC', 'MEd', 'MEmerg Nsg', 'NE-BC', 'AuD',
			'NEA-BC', 'NNP-BC', 'PMHSNC-BC', 'PMHNP-BC', 'PNP-BC', 'RN-BC', 'RNC-LRN', 'RNC-MNN', 'MSci',
			'RNC-NIC', 'RNC-OB', 'SANE-A', 'SANE-P', 'TNCC-I', 'TNCC-P', 'WHNP-BC', 'CCC-SLP', 'MHSc', 'RAc',
			'MRCOG(UK)', 'MBE', 'MStJ', 'LPsy', 'Clin Psy', 'DrMed', 'DrPhil', 'BVSc', 'MDm', 'MMEd',
			'Dipl', 'MRCPsych', 'LAc'
		);
		
		# Clean up the credentials by changing them into their original formats.
		$credentials = str_ireplace($find, $replace, $credentials);
		
		return implode(', ', array_reverse($credentials));
	}
	
	/**
	 *
	 * @return string
	 */
	public function getSuffixes()
	{
		$suffixes 	= $this->_suffixes;
		
		# If there are no suffixes, then there's nothing we can do.
		if (!$suffixes)
		{
			return null;
		}
		
		# Remove any commas, periods, and/or spaces from the suffixes.
		$suffixes	= preg_replace('~[,\.\s]~i', '', $suffixes);
		
		# Return an imploded string of the suffixes -- although typically we'll only have one suffix.
		return implode(', ', array_reverse($suffixes));
	}
	
	/**
	 * Get a list of the positions.
	 *
	 * @return string
	 */
	public function getPositions()
	{
		$positions 	= $this->_positions;
		
		# If there are no positions, then there's nothing we can do.
		if (!$positions)
		{
			return null;
		}
		
		# Remove any commas, periods, and/or spaces from the positions.
		$positions	= preg_replace('~[,\.]~i', '', $positions);
		
		# Return an imploded string of the positions -- although typically we'll only have one suffix.
		return implode(', ', array_reverse($positions));
	}
	
	/**
	 *
	 * @return string
	 */
	public function getNickname()
	{
		return $this->_nickname;
	}
	
	/**
	 *
	 * @return string
	 */
	public function getFirstName()
	{
		return $this->_firstName;
	}
	
	/**
	 *
	 * @return string
	 */
	public function getMiddleName()
	{
		return $this->_middleName;
	}
	
	/**
	 *
	 * @return string
	 */
	public function getMiddleInitial()
	{
		return $this->_middleInitial;
	}
	
	/**
	 *
	 * @return string
	 */
	public function getLastName()
	{
		return $this->_lastName;
	}
	
	/**
	 * Trim the name component to remove any spaces, commas, or periods from the beginning
	 * and end of the string.
	 *
	 * @param string $nameComponent
	 * @return string
	 */
	private function _trim($nameComponent)
	{
		return trim($nameComponent, ' ,.');
	}
	
	/**
	 * If the letter is all capitalised, then for beautify purposes we'll convert all the letters
	 * to lowercase, and then make the first letter of every word uppercase.
	 *
	 * @param string $name
	 * @return string
	 */
	private function _normaliseIfCapitalised($name)
	{
		if (preg_match('~[a-z]~', $name))
		{
			return $name;
		}
		
		$name 	= strtolower($name);
		$name	= ucwords($name);
		
		return $name;
	}
}