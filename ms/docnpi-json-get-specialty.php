<?php
/*
This script accepts a list of names and zipcodes and attempts to identify their specialty from the NPI API. 

Written by Paul Arnaudo 2/24/12 
*/
include("../lib/init.php");	
$Start = getTime(); 
$count=0;
//target set
$table="neurologist";
clearTable($table);

/*

		*/
	 $specialty='';		
	 $specialtyArray=array();
	 $query = "Neurology"; //your query terms
 	 print "Searching for: $query\n";
  		$params = array(
		'first_name' => '',
    	'last_name' => '',
    	'zip' => '',
		'org_name' => '',
    	'state' => '',
		'city_name' => '',
		'taxonomy' => 'code_179',
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
			$fname=$value->first_name;
			$lname=$value->last_name;
			$address=$value->address;
			$city=$value->city;
			$state=$value->state;
			$zip=$value->zip;
			$id=$value->npi;
			foreach($value->tax_array as $specialty){
				$specialtyArray[]=$specialty;
			}
	$query ="INSERT INTO ".$table." (firstName, lastName, address,city, state, id,zipcode) VALUES ('".$fname."','".$lname."','".$address."','".$city."','".$state."','".$id."','".$zip."')";
	mysql_query($query);
	$count ++;
	}	
	
	//if values then input


$End = getTime(); 
echo "Time taken = ".number_format(($End - $Start),2)." secs with ".$count." updates";

?>
