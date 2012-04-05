<?php

/*
This script creates an address rank based on the number of instances they have in the data set

Written by Paul Arnaudo 3/29/12 
*/
include("lib/init.php");	
$author="TEST PA";
$query="Test PA";
if(stripos($author,$query)===0){
						
						$authorIdentifier=$row['id'];
						$authorMatch=1;
						$physicianQuery=$query;
					echo "match";		
					}	
else{
echo "no match";
}					
?>