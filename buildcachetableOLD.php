<?php
include("lib/init.php");	
//$tabledata = array();	 
$atomArray=array();
$columnArray=array();
$count=0;
$tables= array();
$mixtureSearch= array(531,532);
$mixtureSearchString = implode(",",$mixtureSearch);
$insertTable ="CachePhysicianTable";
$test = getMostPopulatedMatters(500);

//CREATE TABLE QUEREIES
$testTableQuery="SHOW TABLES FROM ".$CFG->dbname." LIKE ".$insertTable;
$createTable="CREATE TABLE ".$insertTable."(Id INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT, atomId INT(11))";
$testTable = mysql_query($getMatterIds);
$numRows = mysql_num_rows($result);
//Check if table has been created
if($numRows=0){
	mysql_query($createTable)or die(mysql_error());
	echo "Database doesn't exist!";
	echo "Database created";
//Create if it hasn't
}
else{
	mysql_query("DROP TABLE ".$insertTable."");
	echo "Database deleted!";
	mysql_query($createTable)or die(mysql_error());
	echo "Database created";
	//Drop and create if it has
}

$getMatterIds="select distinct particles.atomId,matters.matterId, name, particles.value from particles INNER JOIN mixtureatoms ON mixtureatoms.atomId=particles.atomId
INNER JOIN matters ON matters.matterId=particles.matterId 
WHERE mixtureId IN (".$mixtureSearchString.") AND particles.value !='' AND particles.value!='-' 
ORDER BY particles.atomId, particles.isotopeId";
//Get all particles relevant to our subjects
$result = mysql_query($getMatterIds) or die(mysql_error());
$numRows = mysql_num_rows($result);
$stringreplace=array(" ","-","&","(",")","|","/",",",".",";","=","#",":","'","+","?");	
	
$countRows=0;
	while($row = mysql_fetch_array($result)){
		$countRows++;
	//check if atom has been added	
		if(in_array($row['atomId'],$atomArray)){					
			}
		else{
			$atomArray[] = $row['atomId'];
			$insertQuery = "INSERT INTO ".$insertTable." (atomId) VALUES ('".$row['atomId']."')";
			
			mysql_query($insertQuery)or die(mysql_error());
			
		//Insert if not
		}
		$columnName=trim($row['matterId'].$row['name']);
		$columnNameLength=strlen($columnName);
		if($columnNameLength >= 64){
				$columnName=substr($columnName,0,62);
		}
		$columnName=str_replace($stringreplace,"",$columnName);
		//remove spaces, bad symbols and truncate for size restrictions for column
		
		if(in_array($columnName,$columnArray)){
			//echo $columnName. " Already in array";
			//echo "<br>";
			}
			//check for column name
		else{
			$alterQuery = "ALTER TABLE ".$insertTable." ADD ".$columnName." VARCHAR(40)";
			mysql_query($alterQuery)or die(mysql_error());

		
		$columnArray[]=$columnName;
		//Insert if not
		}
			 $updateQuery = "UPDATE ".$insertTable." SET ".$columnName."='".mysql_real_escape_string($row['value'])."' WHERE atomId=".$row['atomId'];
			mysql_query($updateQuery)or die(mysql_error());
			//echo $updateQuery. "<br>";
			
		
	}
	echo "All done with ".$numRows." rows!";
?>