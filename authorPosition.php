<?php

/*
This script accepts as an input a list of authors names and queries the pubmed database for all the papers attributed to the author. It then parses all the authors listed on the paper, marks the first, second and last author and records them to a sepaarate table. 

Written by Paul Arnaudo 2/17/12 
*/
include("lib/init.php");	

$Start = getTime(); 
//remove old data
//clearAuthorTables();

//query to get doctor set, can really be from anywhere
$queryDoctors = "SELECT distinct paper, coAuthorPosition, query from authors where id < 889231";
$result = mysql_query($queryDoctors) or die(mysql_error());
while($row=mysql_fetch_array($result)){
	$updateQuery='UPDATE authors SET authorPosition='.$row['coAuthorPosition'].' WHERE paper='.$row['paper'];
	mysql_query($updateQuery);
}
$End = getTime(); 
echo "Time taken = ".number_format(($End - $Start),2)." secs";
?>