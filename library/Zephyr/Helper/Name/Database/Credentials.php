<?php

class Zephyr_Helper_Name_Database_Credentials
{
	private $_db;
	private $_filePath;
	
	public function __construct()
	{
		$this->_filePath	= dirname(dirname(dirname(dirname(__FILE__)))) . '/_data/credentials.db';

		$this->_db 			= Zend_Db::factory('PDO_SQLITE', array('dbname' => $this->_filePath));
		
		if (!file_exists($this->_filePath))
		{
			file_put_contents($this->_filePath, null);
			$this->_create();
		}
	}
	
	private function _create()
	{
		Zephyr_Output::debugStatic('Creating and populating the credentials database...');
		
		$filename	= basename($this->_filePath);
		$directory 	= dirname($this->_filePath);
		
		$this->_db->query("DROP TABLE IF EXISTS `credentials`;");
		
		$sql = "CREATE TABLE credentials (
			id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
			credential VARCHAR(32) NOT NULL);";
		
		$this->_db->query($sql);
		
		$credentials = array
		(
			'MD', 'HI', 'JY', 'SC', 'HY', 'NP', 'KK', 'FNP', 'RN', 'CNM', 'PharmD', 'MRCOGUK',
			'YK', 'BA', 'CTAGME', 'RH', 'LCSW', 'HW', 'PA', 'VS', 'MSW', 'LHD', 'BDS', 'MENG',
			'BCh', 'MA', 'MD', 'MS', 'MB', 'BS', 'PsyD', 'MED', 'DVM', 'DMSc', 'MSc', 'CCC-SLP',
			'DMD', 'CM', 'BCH', 'CHB', 'DSc', 'CHB', 'OD', 'BM', 'MSW', 'MPH', 'PhD', 'MHS', 'MFA',
			'DDS', 'DO', 'ScD', 'R', 'GA', 'J', 'EdD', 'B', 'RPh', 'DNursSci', 'EngD', 'FASCRS', 'MStJ',
			'DBA', 'DC', 'DD', 'FACS', 'FRCPc', 'FRCP', 'FRCSC', 'FAAEM', 'FACEP', 'FAHA', 'FESC', 'JD',
			'FACC', 'FACP', 'MB', 'FSIR', 'FCCP', 'MBBS', 'MSN', 'PhD Clin', 'MBChB', 'AS', 'BSW', 'MFU',
			'ABPP', 'DM', 'FACE', 'FRCSc', 'MSHI', 'FAAD', 'FRS', 'FCCM', 'DrPH', 'MDiv', 'FASGE', 'FACMT',
			'FACOG', 'MSEd', 'ACLAM', 'FAAMA', 'FAAP', 'MBBCh', 'MPhil', 'PA-C', 'FSCAI', 'AA', 'FASN',
			'MSHI', 'MBA', 'DPhil', 'MSCE', 'MMSc', 'SANE-A', 'DABR', 'AAS', 'AAN', 'ACLS', 'MHSc', 'FACNS',
			'ACNP-BC', 'ACNPC', 'ACRN', 'ADLS', 'ADN', 'ALCN', 'ANP-BC', 'AOCN', 'AOCNP', 'FACG', 'FASH',
			'AOCNS', 'APHN-BC', 'APN', 'ARNP', 'ASN', 'APRN', 'BLS', 'BDLS', 'BM', 'BN', 'FASGE', 'FACR',
			'BScN', 'BHSc', 'BSN', 'CANP', 'CATN-P', 'CATN-I', 'CAPA', 'CARN', 'CBN', 'CCCN', 'AGAF', 'DACLAM',
			'CCM', 'CCNS', 'CCRN', 'CCTC', 'CCTN', 'CTRN', 'CDDN', 'CDE', 'CDMS', 'CDN', 'CDONALTC', 'MBBS',
			'C-EFM', 'CEN', 'CETN', 'CFCN', 'CFN', 'CFNP', 'CFRN', 'CHES', 'CGN', 'CGRN', 'CHN', 'MRCOG',
			'CHPN', 'CHRN', 'CIC', 'CLC', 'CLNC', 'CMA', 'CM', 'CMC', 'CMCN', 'CMDSC', 'CMSRN', 'CNA', 'MAS',
			'CNE', 'CNL', 'CNLCP', 'CNM', 'CNML', 'CNN', 'CNOR', 'CNO', 'CNP', 'C-NPT', 'CNRN', 'C-TAGME',
			'CNS', 'CNSN', 'COCN', 'COHN', 'COHNCM', 'COHN-S', 'COHN-SCM', 'CORLN', 'CPAN', 'CPDN', 'RDMS',
			'CPEN', 'CPHQ', 'CPN', 'CPNA', 'CPNL', 'CPNP', 'CPON', 'CPSN', 'CRN', 'CRNA', 'CRNFA', 'RDCS',
			'CRNI', 'CRNL', 'CRNO', 'CRNP', 'CRRN', 'CRRN-A', 'CS', 'CSC', 'C-SPI', 'CT', 'CTN', 'MRCPUK',
			'CTRN', 'CTRS', 'CUA', 'CUCNS', 'CUNP', 'CURN', 'CVN', 'CWCN', 'CWOCN', 'CWS', 'DN', 'DNP',
			'DrNP', 'DNS', 'EdP', 'EN', 'ENPC', 'ET', 'FAAN', 'FAAPM', 'FAEN', 'FNC', 'FNP-C', 'FNP-BC',
			'FPNP', 'FRCN', 'FRCNA', 'GN', 'GNP', 'GPN', 'GRN', 'HNC', 'IBQH', 'IBCLC', 'ICC', 'INC', 'FAAOS',
			'IPN', 'LCCE', 'LNC', 'LNCC', 'LNP', 'LPN', 'LSN', 'LTC', 'LVN', 'MA', 'MAN', 'MICT', 'FARVO',
			'ME', 'MEd', 'MEMERGNSG', 'MHN', 'MICN', 'MN', 'MPH', 'MRCNA', 'MS', 'MSN', 'NCSN', 'RPVI', 'MHA',
			'NE-BC', 'NEA-BC', 'NNP-BC', 'NPC', 'NPP', 'NZCFN', 'OCN', 'ONC', 'PALS', 'PCCN', 'PNCS', 'DNB',
			'PHN', 'PHRN', 'PMHSNC-BC', 'PMHNP-BC', 'PNP-BC', 'RN', 'RIN', 'RN-BC', 'RNC', 'RNC-LRN', 'MMS',
			'RNC-MNN', 'RNC-NIC', 'RNC-OB', 'RNCS', 'RNFA', 'RPN', 'SANE-A', 'SANE-P', 'SEN', 'SHN', 'FASTRO',
			'SN', 'SPN', 'SRNA', 'SVN', 'TNCC-I', 'TNCC-P', 'TNP', 'TNS', 'WHNP-BC', 'WCC', 'FRACP', 'FRCS', 'RAc',
			'LPsy', 'DMFT', 'CASAC', 'LMFT', 'LCSW', 'LMSW', 'CRC', 'LMHC', 'LPC', 'LPC-BE', 'LCAT', 'Clin Psy',
			'FSFP', 'MCIS', 'DrPhil', 'DrMed', 'BVSc', 'DACVP', 'MBE', 'MDm', 'MRCPCH', 'ATC', 'BBA', 'MFA', 'DPM',
			'MFU', 'MHPE', 'MLIS', 'MMEd', 'MPE', 'MRCP', 'MSBC', 'MSci', 'PHR', 'CPC', 'CPS', 'CGC', 'LMT', 'AuD',
			'PD', 'Dipl', 'Phil', 'FACB', 'MRCPsych', 'BC', 'PMHNP', 'BAMS', 'FRANZCP', 'BCPP', 'LISW', 'BSEE',
			'LICDC', 'AB'
		);
		
		foreach ($credentials as $credential)
		{
			$credentials = array_merge($this->_getPermutations($credential), $credentials);
		}
		
		foreach ($credentials as $credential)
		{
			$sql = sprintf('INSERT INTO credentials (credential) VALUES ("%s")', $credential);
			$this->_db->query($sql);
		}
	}
	
	public function fetch($credential)
	{
		$sql = sprintf('SELECT * FROM credentials WHERE credential = "%s"', $credential);
		return $this->_db->fetchOne($sql);
	}
	
	public function add($credential)
	{
		$sql = sprintf('INSERT INTO credentials (credential) VALUES ("%s")', $credential);
		$this->_db->query($sql);
	}
	
	private function _getPermutations($credential)
	{
		$characters 	= array(',', '-', '.');
		$credentials 	= array();
		
		foreach ($characters as $chr)
		{
			$credentials[] = preg_replace('~~i', $chr, $credential);
			$credentials[] = trim(preg_replace('~~i', $chr, $credential), $chr);
			$credentials[] = ltrim(preg_replace('~~i', $chr, $credential), $chr);
			$credentials[] = rtrim(preg_replace('~~i', $chr, $credential), $chr);
		}
		
		return $credentials;
	}
}