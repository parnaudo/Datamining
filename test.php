<?php

/*
This script creates an address rank based on the number of instances they have in the data set

Written by Paul Arnaudo 3/29/12 
*/
include("lib/init.php");
//updatePercentiles("topneurologistsnetworkmeasures","paperCount","PaperCountPercentile");
//updatePercentiles("topneurologistsnetworkmeasures","ClosenessCentrality","ClosenessPercentile");
//updatePercentiles("topneurologistsnetworkmeasures","BetweennessCentrality","BetweennessPercentile");
//updatePercentiles("topneurologistsnetworkmeasures","SCImagoProminenceScore","SCImagoProminenceScorePercentile");
//updatePercentiles("topneurologistsnetworkmeasures","ClinicalTrialsCount","ClinicalTrialsPercentile");	

	$query="select * from trials where id not in( select t.id from trials t INNER JOIN topneurologistsnetworkmeasures n ON (t.firstName=n.firstName AND t.lastName=n.lastName))";
	$result=mysql_query($query);
	while($row=mysql_fetch_array($result)){
		$insertQuery="INSERT INTO topneurologistsnetworkmeasures (firstName,lastName,ClinicalTrialsCount) VALUES ('".$row['FirstName']."','".$row['LastName']."','".$row['Count']."')";
		echo $insertQuery."<BR>";
		mysql_query($insertQuery);
	}

/*
foreach($physicians as $key){
	foreach($key as $row){
		echo $row['paper']."  ".$row['coAuthorPosition']."  ".$row['numAuthors']."  ".$row['id']."<BR>";
	
	}
}
*/
?>