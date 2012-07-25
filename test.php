<?php
include("lib/init.php");
$Start = getTime(); 
//remove old data from tables
$table="CIUpubinstances";
//clearTable($table);
$dataminer=new dataMiner;
$authorID=1;
$queryDoctors = "select * from ocrelarge";
$result = mysql_query($queryDoctors) or die(mysql_error());
while($row=mysql_fetch_array($result)){
	$filter=array();
	$test=0;
	$commonTestQuery="SELECT rank from names where name LIKE '".mysql_escape_string($row['lastName'])."'";
	$testResult=mysql_query($commonTestQuery);
	$testRow=mysql_fetch_array($testResult);
	$test=mysql_num_rows($testResult); 
	if($test>0){
		if($row['paperCountFullAuthor']==0){
			$count=$row['paperCountFullAuthor'];
		}
		elseif(!is_null($row['middleName'])){
			$filter[]="middleName LIKE '".substr($row['middleName'],0,1)."%'";
			$filter[]="firstName like '".substr($row['firstName'],0,2)."%'";
			$otherNameQuery="SELECT * FROM ocrelarge where lastName LIKE '".mysql_escape_string($row['lastName'])."' AND ".implode(' AND ',$filter);
			echo $otherNameQuery."<BR>";
			if(mysql_num_rows(mysql_query($otherNameQuery)) > 1){
				$count=$row['paperCountFullAuthor'];
			}
			else{
				$count=$row['paperCount'];
			}		
		}
		else{
			$count=$row['paperCountFullAuthor'];
		}
		$updateQuery="UPDATE ocreLarge SET truePaperCount=".$count.",commonName=".$testRow['rank']." WHERE atomId=".$row['atomId'];		
		mysql_query($updateQuery);
		echo $updateQuery."<BR>";
	}
	else{
		$count=$row['paperCount'];
		if($row['paperCountFullAuthor']==0){
			$count=$row['paperCountFullAuthor'];
		}
		$updateQuery="UPDATE ocreLarge SET truePaperCount=".$count." WHERE atomId=".$row['atomId'];
		mysql_query($updateQuery);
		echo $updateQuery."<BR>";	
	}
}
$End = getTime(); 
echo "Time taken = ".number_format(($End - $Start),2)." seconds";
?>