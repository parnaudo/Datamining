<?php 

class Zephyr_Helper_Address_State extends Zephyr_Helper_Address_Helper
{
	protected $_filterMatchIndex = 1;
	protected function _getRegex()
	{
		$insensitive = array('ALABAMA', 'ALASKA', 'AMERICAN SAMOA', 'ARIZONA', 'ARKANSAS', 'CALIFORNIA', 'COLORADO', 'CONNECTICUT', 'DELAWARE', 'DISTRICT OF COLUMBIA', 'FEDERATED STATES OF MICRONESIA', 'FLORIDA', 'GEORGIA', 'GUAM GU', 'HAWAII', 'IDAHO', 'ILLINOIS', 'INDIANA', 'IOWA', 'KANSAS', 'KENTUCKY', 'LOUISIANA', 'MAINE', 'MARSHALL ISLANDS', 'MARYLAND', 'MASSACHUSETTS', 'MICHIGAN', 'MINNESOTA', 'MISSISSIPPI', 'MISSOURI', 'MONTANA', 'NEBRASKA', 'NEVADA', 'NEW HAMPSHIRE', 'NEW JERSEY', 'NEW MEXICO', 'NEW YORK', 'NORTH CAROLINA', 'NORTH DAKOTA', 'NORTHERN MARIANA ISLANDS', 'OHIO', 'OKLAHOMA', 'OREGON', 'PALAU', 'PENNSYLVANIA', 'PUERTO RICO', 'RHODE ISLAND', 'SOUTH CAROLINA', 'SOUTH DAKOTA', 'TENNESSEE', 'TEXAS', 'UTAH', 'VERMONT', 'VIRGIN ISLANDS', 'VIRGINIA', 'WASHINGTON', 'WEST VIRGINIA', 'WISCONSIN', 'WYOMING', 'Armed Forces Africa', 'Armed Forces Americas', 'Armed Forces Canada', 'Armed Forces Europe', 'Armed Forces Middle East', 'Armed Forces Pacific');
		$sensitive = array('AL', 'AK', 'AS', 'AZ', 'AR', 'CA', 'CO', 'CT', 'DE', 'DC', 'FM', 'FL', 'GA', 'GU', 'HI', 'ID', 'IL', 'IN', 'IA', 'KS', 'KY', 'LA', 'ME', 'MH', 'MD', 'MA', 'MI', 'MN', 'MS', 'MO', 'MT', 'NE', 'NV', 'NH', 'NJ', 'NM', 'NY', 'NC', 'ND', 'MP', 'OH', 'OK', 'OR', 'PW', 'PA', 'PR', 'RI', 'SC', 'SD', 'TN', 'TX', 'UT', 'VT', 'VI', 'VA', 'WA', 'WV', 'WI', 'WY', 'AE', 'AA', 'AE', 'AE', 'AE', 'AP'); 
		
		$insensitive = implode('|', $insensitive);
		$sensitive = implode('|', $sensitive);

		$regex = array();
		$regex[] = "~\s($insensitive)$~i";
		$regex[] = "~\s($sensitive)$~";
		return $regex;
	}
}