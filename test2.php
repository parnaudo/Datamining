<?php 
include("lib/init.php");
$Start = getTime(); 
$table="education";
clearTable($table);
$dataminer=new dataMiner;
//query to get doctor set, can really be from anywhere, I'm pulling from a temporary doctor table that has first, last and middle 
$queryDoctors = "select atomId,medschool,residency,fellowship from schizo ";
$result=mysql_query($queryDoctors);
while($row=mysql_fetch_array($result)){
	$find=';';
	$textManipulate= new textManipulate;
	$occurenceArray=$textManipulate->findOccurences($row['medschool'], $find);
	$test=$textManipulate->separateOccurences($occurenceArray,$row['medschool']);
	$test=$textManipulate->separateDates($test);
	$i=0;
	for($i=0;$i<sizeof($test);$i++){
		$insertQuery="INSERT INTO ".$table." (atomId,type,name,years) VALUES ('". $row['atomId']."','". $test['type'][$i]."','". $test['name'][$i]."','". $test['date'][$i]."')";
		echo $insertQuery;
	}
	//$test=$textManipulate->separateDegrees($test['string']);
	//var_dump($test['date']);
}
	//$uids=$dataminer->eSearch($query,0);
//if there are papers, insert an author record	
$End = getTime(); 
echo "Time taken = ".number_format(($End - $Start),2)." secs";

?>