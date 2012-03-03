<?php
/*
This script accepts a list of names and zipcodes and attempts to identify their specialty from the NPI API. 

Written by Paul Arnaudo 2/24/12 
*/
include("lib/init.php");	
$Start = getTime(); 
//target set
$queryDoctors = "SELECT atomId, firstName,zipcode, lastName FROM tempdoc WHERE specialty IS NULL";
$result = mysql_query($queryDoctors) or die(mysql_error());
while($row=mysql_fetch_array($result)){
/*

		*/
	 $specialty='';		
	 $specialtyArray=array();
	 $query = $row['firstName']." ".$row['lastName']. " ". $row['zipcode']; //your query terms
 	 print "Searching for: $query\n";
  		$params = array(
		'first_name' => $row['firstName'],
    	'last_name' => $row['lastName'],
    	'zip' => $row['zipcode'],
		'org_name' => '',
    	'state' => 'CA',
		'city_name' => '',
		'taxonomy' => '',
    	'is_person' => 'true',
		'is_address' => 'false',
		'format' => 'json',
    	);
  //NPI API URL
  	$url = 'http://docnpi.com/api/index.php?' . http_build_query($params);
	$homepage = file_get_contents($url);
	$json = json_decode($homepage);
	$jsonArray = (array) $json;
 //parse JSON
	foreach($jsonArray as $value){
			
			foreach($value->tax_array as $specialty){
				$specialtyArray[]=$specialty;
			}
	}	
	$specialty=implode(" | ",$specialtyArray);
	//if values then input
	if(!empty($specialty)){
		$specialtyQuery = "UPDATE tempdoc SET specialty='".$specialty."' WHERE atomId='".$row['atomId']."'";
		mysql_query($specialtyQuery);
		echo $specialtyQuery."<br>";
	}
}
$End = getTime(); 
echo "Time taken = ".number_format(($End - $Start),2)." secs";

?>
