<?php

/*
This script creates an address rank based on the number of instances they have in the data set

Written by Paul Arnaudo 3/29/12 
*/
include("lib/init.php");	
function updatePercentiles($table,$field,$percentileField){
	$query="SELECT count(".$field.") as totalCount from ".$table;
	$query=mysql_query($query);
	$row=mysql_fetch_array($query);
	$totalCount=$row['totalCount'];
	$query="SELECT Id,".$field." FROM ".$table;
	$result=mysql_query($query);
	while($row=mysql_fetch_array($result)){
	
		$getCounts="SELECT count(".$field.") as lowCount from  ".$table." where ".$field." < ".$row['paperCount'];
		$result2=mysql_query($getCounts);
		$row2=mysql_fetch_array($result2);
		$percentile=$row2['lowCount']/$totalCount;
		$updateQuery="UPDATE topneurologistsnetworkmeasures set PaperCountPercentile=".$percentile." WHERE Id=".$row['Id'];
		mysql_query($updateQuery);
	}
}
/*
foreach($physicians as $key){
	foreach($key as $row){
		echo $row['paper']."  ".$row['coAuthorPosition']."  ".$row['numAuthors']."  ".$row['id']."<BR>";
	
	}
}
*/
?>