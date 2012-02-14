
<?php

include("lib/init.php");	 
$count=0;

function linkMEnumber($searchstring){
	$sql="select name,particles.value, particles.atomId FROM particles inner join matters on matters.matterId=particles.matterId where matters.name like '%$searchstring%'";
	$result = mysql_query($sql) or die(mysql_error());
	$numRows = mysql_num_rows($result);
	
	// Print out the contents of each row into a table 
	while($row = mysql_fetch_array($result)){
		if($row['atomId'] !=0 && $row['value']!=''){
			$getMedschool="select particles.value FROM particles INNER JOIN matters ON matters.matterID=particles.matterID WHERE matters.name LIKE '%medschool%' AND particles.atomID=".$row['atomId'];
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
					$insertQuery="INSERT INTO universitymenumbers (menumber, universityname) VALUES ('".$schoolID."', '".$MEinfo['university']."')";
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
createMETable("universitymenumbers");
linkMEnumber(me_number);
linkMEnumber(menumber);
linkMEnumber("me number");
linkMEnumber("me_10");
//looking to make menumbers associated with medschools

?>