<?php
include("lib/init.php");	
//$tabledata = array();	 
$searchArray=array("medschool");
$columnArray=array();
$count=0;
$tables= array();
$mixtureSearch= array(531);
$mixtureSearchString = implode(",",$mixtureSearch);
$insertTable ="CachePhysicianTable";
//Get matters that have more than 100 instances



//Check if table has been created


$getValues="select medschool from tempdoc where medschool!=''";
//Get all particles relevant to our subjects
$result = mysql_query($getValues) or die(mysql_error());
$numRows = mysql_num_rows($result);
$numRows = mysql_num_rows($result);
$stringreplace=array(" ","-","&","(",")","|","/",",",".",";","=","#",":","'","+","?");	
$countColumns=0;	
$countRows=0;
	while($row = mysql_fetch_array($result)){
		echo $row['medschool'];
	}
	echo "All done with ".$numRows." rows and ".$countRows." columns!";
?>