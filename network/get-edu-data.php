<?php 
include("../lib/init.php");
$Start = getTime(); 
$table="education";
$attribute=array('fellowship','residency','medschool');

clearTable($table);
foreach($attribute as $attribute){
	extractEducation($attribute,$table);
}
$dataminer=new dataMiner;
function extractEducation($attribute,$table){
	$queryDoctors = "select atomId,".$attribute." from node where ".$attribute."!=''";
	echo $queryDoctors;
	//$queryDoctors = "select atomId,medschool from schizo where medschool!='' and atomId IN (1711601)";
	//1711550,
	$result=mysql_query($queryDoctors);
	while($row=mysql_fetch_array($result)){
		$find=';';
		$test=mb_split($find,$row[$attribute]);
		//var_dump($test);
		$textManipulate= new textManipulate;
		//$occurenceArray=$textManipulate->findOccurences($row[$attribute], $find);
		//This data set has different education occurences split by ';' this splits them up and returns an array
		if($test!==FALSE){
			//$test=$textManipulate->separateOccurences($occurenceArray,$row[$attribute]);
			$test=$textManipulate->parseRecords($test);	
		}
		else{
			$test=array($row[$attribute]);
			$test=$textManipulate->parseRecords($test);	
		}
		$i=0;
		

		for($i=0;$i<sizeof($test['name']);$i++){
			switch($attribute){
			case 'residency':
				$test['type'][$i]=$attribute;
			case 'fellowship':
				$test['type'][$i]=$attribute;
			}
			$insertQuery="INSERT INTO ".$table." (atomId,type,name,years) VALUES ('". $row['atomId']."','". $test['type'][$i]."','". $test['name'][$i]."','". $test['date'][$i]."')";
			echo $insertQuery."<BR>";
			mysql_query($insertQuery);
		}
		//$test=$textManipulate->separateDegrees($test['string']);
		//var_dump($test['date']);
	}
}
	//$uids=$dataminer->eSearch($query,0);
//if there are papers, insert an author record	
$End = getTime(); 
echo "Time taken = ".number_format(($End - $Start),2)." secs";

?>