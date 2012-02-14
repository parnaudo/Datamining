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


$Start = getTime();
$getParticles="select isotopeId,particles.atomId,value,name,matters.matterId from particles 
INNER JOIN matters ON particles.matterId = matters.matterId
INNER JOIN ".$insertTable." ON ".$insertTable.".atomId = particles.atomId
where  value !='' AND value !='-' 
";
//Get all particles relevant to our subjects
$result = mysql_query($getParticles) or die(mysql_error());
$numRows = mysql_num_rows($result);
$stringreplace=array(" ","-","&","(",")","|","/",",",".",";","=","#",":","'","+","?");	
$countColumns=0;	
$countUpdates=0;
while($row = mysql_fetch_array($result)){
	$menumber='';
	$fellowshipyear='';
	$updateQuery='';
	if(preg_match("/First Name/i",$row['name'])){	
	$updateQuery = "update ".$insertTable." SET firstName = '".$row['value']."' where atomId='".$row['atomId']."'";
	$countUpdates++;
	
	
	
	}
	elseif(preg_match("/Last Name/i",$row['name'])){	
		$updateQuery = "update ".$insertTable." SET lastName ='".addslashes($row['value'])."' where atomId='".$row['atomId']."'";
		
		$countUpdates++;
	}
/*	elseif(preg_match("/zip/i",$row['name'])){
		if(preg_match("	/zipsector/i", $row['name'])){
			break;
		}
		else{	
		$updateQuery = "update ".$insertTable." SET zipcode ='".$row['value']."' where atomId='".$row['atomId']."'";
		$countUpdates++;
		}
	}*/
	elseif(preg_match("/phone/i",$row['name'])){	
		$updateQuery = "update ".$insertTable." SET phone ='".$row['value']."' where atomId='".$row['atomId']."'";
		$countUpdates++;	
	}
	
	elseif(preg_match("/medical degree from/i",$row['name']) || preg_match("/medschool/i",$row['name']) || preg_match("/medical school name/i",$row['name']))
	{
		
		$updateQuery = "update ".$insertTable." SET medschool ='".addslashes($row['value'])."' where atomId='".$row['atomId']."'<br>";
		$countUpdates++;
		
		
		
	}
	elseif(preg_match("/me_number/i",$row['name']) || preg_match("/me number/i",$row['name']) || preg_match("/menumber/i",$row['name'])|| preg_match("/me_10/i",$row['name']))
	{
		$menumber = str_pad($row['value'], 10, "0", STR_PAD_LEFT);
		
		$updateQuery = "update ".$insertTable." SET menumber ='".$menumber."' where atomId='".$row['atomId']."'";
		
		$countUpdates++;
		
	}
	elseif(preg_match("/medical degree year/i", $row['name'])){
		
		$updateQuery = "update ".$insertTable." SET medschoolyear ='".$row['value']."' where atomId='".$row['atomId']."'";
		$countUpdates++;
	
	}
	elseif(preg_match("/residence/i",$row['name']) || preg_match("/residency location/i",$row['name']))
	{
		$updateQuery = "update ".$insertTable." SET residency ='".addslashes($row['value'])."' where atomId='".$row['atomId']."'";
		$countUpdates++;
		
		}
	elseif(preg_match("/residency year/i", $row['name'])){
		
		$updateQuery = "update ".$insertTable." SET residencyyear ='".$row['value']."' where atomId='".$row['atomId']."'";
		$countUpdates++;
	
	}
	elseif(preg_match("/Fellowship Location/i",$row['name']))
	{
		$updateQuery = "update ".$insertTable." SET fellowship ='".addslashes($row['value'])."' where atomId='".$row['atomId']."'";
		$countUpdates++;
	
		}
	elseif(preg_match("/fellowship year/i", $row['name']) || preg_match("/fellowship begin/i", $row['name'])){
		if(preg_match("/fellowship year/i", $row['name'])){
			
			$fellowshipyear = substr($row['value'],0,4);
			
		}
		else {
			$fellowshipyear = $row['value'];
		}
		$updateQuery = "update ".$insertTable." SET fellowshipyear ='".$fellowshipyear."' where atomId='".$row['atomId']."'";
		
		$countUpdates++;
		
	}
	else{
		
	}
	mysql_query($updateQuery);
}
	$End = getTime(); 
	echo "Time taken = ".number_format(($End - $Start),2)." secs";
	echo "All done with ".$numRows." rows with ".$countUpdates." updates!";
?>