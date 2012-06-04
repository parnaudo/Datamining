<?php 
include("lib/init.php");
$Start = getTime(); 
$dataminer=new dataMiner;
//query to get doctor set, can really be from anywhere, I'm pulling from a temporary doctor table that has first, last and middle 
$queryDoctors = "select medschool,residency,fellowship from schizo where atomId=109039 ";
$result=mysql_query($queryDoctors);
while($row=mysql_fetch_array($result)){
	$find=';';
	$textManipulate= new textManipulate;
	$occurenceArray=$textManipulate->findOccurences($row['medschool'], $find);
	$test=$textManipulate->separateOccurences($occurenceArray,$row['medschool']);
	$test=$textManipulate->separateDates($test);
	$test=$textManipulate->separateDegrees($test['string']);
	var_dump($test['date']);
}
	//$uids=$dataminer->eSearch($query,0);
//if there are papers, insert an author record	
$End = getTime(); 
echo "Time taken = ".number_format(($End - $Start),2)." secs";

?>