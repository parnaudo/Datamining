<?php
include("lib/init.php");	
//$tabledata = array();	 
$atomArray=array();
$columnArray=array();
$count=0;
$tables= array();
$mixtureSearch= array(531);
$mixtureSearchString = implode(",",$mixtureSearch);
$insertTable ="docinstance";
//CREATE TABLE QUEREIES
$testTableQuery="SHOW TABLES FROM ".$CFG->dbname." LIKE ".$insertTable;
//Check if table has been created
clearTable($insertTable);
$Start = getTime();
$getIsotopes="select distinct isotopeId,particles.atomId from particles 
INNER JOIN ".$insertTable." ON ".$insertTable.".atomId = particles.atomId
where  value !=''";
//Get all particles relevant to our subjects
$result = mysql_query($getIsotopes) or die(mysql_error());
$numIsotopes = mysql_num_rows($result);
$numParticles = 0;
$stringreplace=array(" ","-","&","(",")","|","/",",",".",";","=","#",":","'","+","?");	
$countColumns=0;	
$countUpdates=0;
//insert one row for each isotop
while($row = mysql_fetch_array($result)){
	$insertQuery= " INSERT INTO ".$insertTable." (atomId,isotopeId) VALUES ('".$row['atomId']."','".$row[	'isotopeId']."')";
	
//	echo $insertQuery;
	mysql_query($insertQuery);
	$getParticles = " select isotopeId,atomId,value,name,matters.matterId from particles 
	INNER JOIN matters ON particles.matterId = matters.matterId
	where isotopeId=".$row['isotopeId']." and value !='' and value !='-'";
	$particleResult = mysql_query($getParticles) or die(mysql_error());
	while ($particlerow= mysql_fetch_array($particleResult)){
		$updateQuery='';
		if(preg_match("/Faculty Location/i",$particlerow['name'])){	
		$updateQuery = "update ".$insertTable." SET name = '".$particlerow['value']."' where atomId='".$particlerow['atomId']."' AND isotopeId = '".$particlerow['isotopeId']."'";
		$countUpdates++;
		
	
	
		}
		elseif(preg_match("/Faculty appointment/i",$particlerow['name'])){	
		$updateQuery = "update ".$insertTable." SET position ='".$particlerow['value']."' where atomId='".$particlerow['atomId']."' AND isotopeId = '".$particlerow['isotopeId']."'";
		
		$countUpdates++;
		
		
		}
		elseif(preg_match("/Organization/i", $particlerow['name'])){
		$updateQuery = "update ".$insertTable." SET name ='".$particlerow['value']."' where atomId='".$particlerow['atomId']."' AND isotopeId = '".$particlerow['isotopeId']."'";
			
		}
		elseif(preg_match("/Tenure from/i", $particlerow['name'])){
		$updateQuery = "update ".$insertTable." SET tenurefrom ='".$particlerow['value']."' where atomId='".$particlerow['atomId']."' AND isotopeId = '".$particlerow['isotopeId']."'";
		$countUpdates++;
		}
		elseif(preg_match("/Tenure to/i", $particlerow['name'])){
		$updateQuery = "update ".$insertTable." SET tenureto ='".$particlerow['value']."' where atomId='".$particlerow['atomId']."' AND isotopeId = '".$particlerow['isotopeId']."'";
		$countUpdates++;	
		}
		elseif(preg_match("/Type/i", $particlerow['name'])){
		$updateQuery = "update ".$insertTable." SET type ='".$particlerow['value']."' where atomId='".$particlerow['atomId']."' AND isotopeId = '".$particlerow['isotopeId']."'";
		$countUpdates++;	
		}
		elseif(preg_match("/position/i", $particlerow['name'])){
			if(preg_match("/Author position/i", $particlerow['name'])){
				$updateQuery='';
			}
			else{
				$updateQuery = "update ".$insertTable." SET position ='".$particlerow['value']."' where atomId='".$particlerow['atomId']."' AND isotopeId = '".$particlerow['isotopeId']."'";
				$countUpdates++;
			}
		}
		elseif(preg_match("/event name/i", $particlerow['name'])){
		$updateQuery = "update ".$insertTable." SET name ='".$particlerow['value']."' where atomId='".$particlerow['atomId']."' AND isotopeId = '".$particlerow['isotopeId']."'";
		$countUpdates++;	
		}
		elseif(preg_match("/institution/i", $particlerow['name'])){
		$updateQuery = "update ".$insertTable." SET name ='".$particlerow['value']."' where atomId='".$particlerow['atomId']."' AND isotopeId = '".$particlerow['isotopeId']."'";
		$countUpdates++;
		}
		elseif(preg_match("/hospital/i", $particlerow['name'])){
		$updateQuery = "update ".$insertTable." SET name ='".$particlerow['value']."' where atomId='".$particlerow['atomId']."' AND isotopeId = '".$particlerow['isotopeId']."'";
		$countUpdates++;
		}
		elseif(preg_match("/trial name/i", $particlerow['name'])){
		$updateQuery = "update ".$insertTable." SET name ='".$particlerow['value']."' where atomId='".$particlerow['atomId']."' AND isotopeId = '".$particlerow['isotopeId']."'";
		$countUpdates++;
		}
		elseif(preg_match("/Start date/i", $particlerow['name'])){
		
		$date = cleanDate($particlerow['value']);
		$updateQuery = "update ".$insertTable." SET tenurefrom ='".$date."' where atomId='".$particlerow['atomId']."' AND isotopeId = '".$particlerow['isotopeId']."'";
		
		$countUpdates++;
		}
		elseif(preg_match("/End date/i", $particlerow['name'])){
		$date = cleanDate($particlerow['value']);
		$updateQuery = "update ".$insertTable." SET tenureto ='".$date."' where atomId='".$particlerow['atomId']."' AND isotopeId = '".$particlerow['isotopeId']."'";
		$countUpdates++;	
		}
		elseif($row['matterId']==='157'){
		$date = cleanDate($particlerow['value']);
		$updateQuery = "update ".$insertTable." SET tenureto ='".$date."' where atomId='".$particlerow['atomId']."' AND isotopeId = '".$particlerow['isotopeId']."'";
		$countUpdates++;	
		}
		mysql_query($updateQuery);
	}
}
cleanTable($insertTable);
	$End = getTime(); 
	echo "Time taken = ".number_format(($End - $Start),2)." secs";
	echo "All done with ".$numRows." rows with ".$countUpdates." updates!";
?>