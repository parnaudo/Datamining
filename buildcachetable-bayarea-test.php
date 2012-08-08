<?php
include("lib/init.php");
	
//$tabledata = array();	 
$insertArray=array();
$columnArray=array();
$countQuery=0;
$count=0;
$tables=array();
$mixtureSearch= array(531);
$countColumns=0;	
$countRows=0;
$mixtureSearchString = implode(",",$mixtureSearch);
$insertTable ="CachePhysicianTable";
$atomArray=array();
$join="INNER JOIN matters ON particles.matterId = matters.matterId
INNER JOIN tempzipcode ON particles.value = tempzipcode.zip";
$query="matters.name LIKE '%zip%'";
$atomArray=getAtomId($join,$query);
//Get matters that have more than 100 instances
$importantMatters = getMostPopulatedMatters(1);
//CREATE TABLE QUEREIES

//Check if table has been created
if($numRows=0){
	createTable($insertTable);
//Create if it hasn't
}
else{
	deleteTable($insertTable);
	createTable($insertTable);
	//Drop and create if it has
}
foreach($atomArray as $key => $ID){
$getMatterIds="select distinct particles.atomId,matters.matterId, name, particles.value from particles 
INNER JOIN matters ON matters.matterId=particles.matterId 
WHERE atomId=".$ID." AND particles.value !='' AND particles.value!='-' 
ORDER BY particles.atomId, particles.isotopeId";	

//Get all particles relevant to our subjects
$result = mysql_query($getMatterIds) or die(mysql_error());
$countQuery++;
$numRows = mysql_num_rows($result);
	while($row = mysql_fetch_array($result)){
		$countRows++;
	//check if atom has been added	
		if(in_array($row['atomId'],$insertArray)){					
			}
		else{
			$insertArray[] = $row['atomId'];
			$insertQuery = "INSERT INTO ".$insertTable." (atomId) VALUES ('".$row['atomId']."')";
			
			mysql_query($insertQuery)or die(mysql_error());
			$countQuery++;

			
		//Insert if not
		}
		$columnName=$row['matterId'].$row['name'];
		$columnNameLength=strlen($columnName);
		if($columnNameLength >= 64){
				$columnName=substr($columnName,0,62);
		}
		$columnName=cleanString($columnName);
		//remove spaces, bad symbols and truncate for size restrictions for column
		
			if(in_array($columnName,$columnArray)){
				}
			//check for column name
			else{
				$countRows++;
				$alterQuery = "ALTER TABLE ".$insertTable." ADD ".$columnName." VARCHAR(40)";
				mysql_query($alterQuery)or die(mysql_error());
				$countQuery++;

			
			$columnArray[]=$columnName;
			//Insert if not
			}
			 $updateQuery = "UPDATE ".$insertTable." SET ".$columnName."='".mysql_real_escape_string($row['value'])."' WHERE atomId=".$row['atomId'];
			mysql_query($updateQuery)or die(mysql_error());
			$countQuery++;

			//echo $updateQuery. "<br>";
		
		

	}
}	

	echo "All done with ".$numRows." rows and ".$countRows." columns! ". $countQuery." queries executed!";
?>