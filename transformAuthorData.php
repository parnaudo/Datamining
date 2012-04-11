<?php
/*
This script accepts a list of names and zipcodes and attempts to identify their specialty from the NPI API. 

Written by Paul Arnaudo 2/24/12 
*/
include("lib/init.php");	
$Start = getTime(); 
//target set
$queryDoctors = "SELECT paper";
$result = mysql_query($queryDoctors) or die(mysql_error());
while($row=mysql_fetch_array($result)){
	
}
$End = getTime(); 
echo "Time taken = ".number_format(($End - $Start),2)." secs";

?>
