<?php
/*
This script accepts a list of names and zipcodes and attempts to identify their specialty from the NPI API. 

Written by Paul Arnaudo 2/24/12 
*/
include("../lib/init.php");	
$Start = getTime(); 
$count=0;
//target set
$queryDoctors = "SELECT id, firstName, lastName FROM ACS ";
$result = mysql_query($queryDoctors) or die(mysql_error());
while($row=mysql_fetch_array($result)){
/*

		*/
	 $address='';	
	 $specialty='';		
	 $specialtyArray=array();
	 $query = $row['firstName']." ".$row['lastName']. " ". $row['zipcode']; //your query terms
 	 print "Searching for: $query\n";
  		$params = array(
		'first_name' => $row['firstName'],
    	'last_name' => $row['lastName'],
		'zip'=> $row['zipCode'],
		'org_name' => '',
    	'state' => '',
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
			$address=$value->address;
			foreach($value->tax_array as $specialty){
				$specialtyArray[]=$specialty;
			}
	}	
	//$specialty=implode(",",$specialtyArray);
	$specialty=$specialtyArray[0];
	if(!empty($address)){
		$specialtyQuery = "UPDATE ACS SET address='".$address."' WHERE id='".$row['id']."'";
		echo $specialtyQuery;
		mysql_query($specialtyQuery);
		$count++;
	}
}
$End = getTime(); 
echo "Time taken = ".number_format(($End - $Start),2)." secs with ".$count." updates";

?>
