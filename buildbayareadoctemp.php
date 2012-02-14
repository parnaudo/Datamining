<?php
include("lib/init.php");	
//$tabledata = array();	 
$atomArray=array();
$columnArray=array();
$count=0;
$tables= array();
$mixtureSearch= array(531);
$mixtureSearchString = implode(",",$mixtureSearch);
$insertTable ="tempdoc";
//Get matters that have more than 100 instances
//$importantMatters = getMostPopulatedMatters(100);
//CREATE TABLE QUEREIES
$Start = getTime();
$testTableQuery="SHOW TABLES FROM ".$CFG->dbname." LIKE '%".$insertTable."%'";
//Check if table has been created
$result = mysql_query($testTableQuery) or die(mysql_error());
$numRows = mysql_num_rows($result);
if($numRows=0){
	createAttributesTable($insertTable);
//Create if it hasn't
}
else{
	deleteTable($insertTable);
	createAttributesTable($insertTable);
	//Drop and create if it has
}
	
$getAtomIds="select distinct atomId,value from particles
INNER JOIN zipcodes ON zipcodes.zipCodeValue=particles.value
where latitude > 37 AND latitude < 38 AND longitude < -121.6
";
echo $getAtomIds;
//Get all particles relevant to our subjects
$result = mysql_query($getAtomIds) or die(mysql_error());
$numRows = mysql_num_rows($result);
$stringreplace=array(" ","-","&","(",")","|","/",",",".",";","=","#",":","'","+","?");	
$countColumns=0;	
$countRows=0;
while($row = mysql_fetch_array($result)){
	$insertQuery = "INSERT INTO ".$insertTable." (atomId,zipcode) VALUES ('".$row['atomId']."','".$row['value']."')";
	mysql_query($insertQuery);
}
	$End = getTime(); 
	echo "Time taken = ".number_format(($End - $Start),2)." secs<br>";
	echo "All done with ".$numRows." rows";
?>
