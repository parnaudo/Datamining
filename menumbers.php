
<?php
/*
This script attempts to reconcile me numbers and their schools. Without having any type of key for ME numbers, might be of value to start mining numbers and matching them to medical schools. 
*/
include("lib/init.php");	 
$count=0;
$table="menumbers";
function linkMEnumber($table){
	$sql="select name,particles.value, particles.atomId FROM particles inner join matters on matters.matterId=particles.matterId where matters.matterId IN (select matterId from matters where (name like '%me number%' or name like '%me_number%' or name like '%menumber%' or name like '%me_10%'))";
	$result = mysql_query($sql) or die(mysql_error());
	$numRows = mysql_num_rows($result);
	
	// Print out the contents of each row into a table 
	while($row = mysql_fetch_array($result)){
		if($row['atomId'] !=0 && $row['value']!=''){
			$getMedschool="select particles.value FROM particles INNER JOIN matters ON matters.matterID=particles.matterID WHERE matters.matterId IN (2917,5149,5681,3761,549,2399,2472) AND particles.atomID=".$row['atomId']." AND particles.value!=''";
			$medschoolResult = mysql_query($getMedschool) or die(mysql_error());
			$rowMedschool = mysql_fetch_array($medschoolResult);
			if($rowMedschool['value']!=''){
				$schoolID=substr($row['value'],0,5);
				
				if(in_array($rowMedschool['value'],$MEinfo)){
					echo $rowMedschool['value']. " Already in array";
					echo "<br>";
				}
				else{
					$MEinfo = array("menumber" => strval($schoolID), "university"=>$rowMedschool['value']);
					$insertQuery="INSERT INTO ".$table." (menumber, universityname) VALUES ('".$schoolID."', '".$MEinfo['university']."')";
					mysql_query($insertQuery);
					echo $insertQuery;
					echo "<br>";
					
				/*echo $MEinfo["menumber"] ." - ". $MEinfo["university"];
				echo "<br />";*/
				}
				$count++;
			}
		}
	}
	echo $numRows. " results with ".$count." medical education number hits";
}
clearTable($table);
//createMETable($table);
linkMEnumber($table);

//looking to make menumbers associated with medschools

?>