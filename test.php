<?php

/*
This script builds relationship scores based off of authorship instances mined from pubmed.

Written by Paul Arnaudo 3/19/12 
*/
include("lib/init.php");	
$queryDoctors = "SELECT * FROM `neurologist` where id=1104831619";

$result = mysql_query($queryDoctors) or die(mysql_error());
while($row=mysql_fetch_array($result)){
  $query='';
  $count=0;

 $query=authorPubmedTransform($row['firstName'],$row['middleName'],$row['lastName']);
 echo $query;
  }
 
?>