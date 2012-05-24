<?php 
include("lib/init.php");
$Start = getTime(); 
$dataminer=new dataMiner;
$filter="(MULTIPLE SCLEROSIS [MESH FIELDS] AND CLINICAL TRIAL [TYPE])";
//query to get doctor set, can really be from anywhere, I'm pulling from a temporary doctor table that has first, last and middle 
$queryDoctors = "select medschool,residency,fellowship from schizo where id IN (109028)";
$string="1970: BA, Miami University; 2001-2002: MD, George Washington University; 1975-79: NOTINTHELEA, Psychiatry, St. Vincent";
function stringProcess($string){
		$test=strpos($string,';');
		
		echo $test;
}
$find=';';
$textManipulate= new textManipulate;

$test=$textManipulate->findOccurences($string, $find);
print_r($test);
echo "<BR>";
$test=$textManipulate->separateOccurences($test,$string);
print_r($test);
echo "<BR>";
$test=$textManipulate->separateDates($test,$string);
$textManipulate->separateDegrees($test['string']);

//	echo $author."<BR>"
	if(!empty($filter)){
		$query[]=$filter;
	}
//	$uids=$dataminer->eSearch($query,0);
//if there are papers, insert an author record	
$End = getTime(); 
echo "Time taken = ".number_format(($End - $Start),2)." secs";

?>