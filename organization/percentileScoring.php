<?php

/*
This script creates an address rank based on the number of instances they have in the data set

Written by Paul Arnaudo 3/29/12 
*/
include("../lib/init.php");
$table='nodepruned';
$sql = "select column_name from information_schema.columns where table_name='".$table."' and column_name='reach'";
$result=mysql_query($sql);
while($row=mysql_fetch_array($result)){
	
	echo stripos($row['column_name'],'Id');
	if(stripos($row['column_name'],'Id')!==0 && stripos($row['column_name'],'Label')!==0 ){
		$column=$row['column_name']."Percentile";
		$alterTable="ALTER TABLE ".$table." ADD ".$column." FLOAT(10)";
		echo $alterTable."<BR>";
		mysql_query($alterTable);
		updatePercentiles($table,$row['column_name'],$column);
	}
}
/*updatePercentiles("topneurologistsnetworkmeasures","paperCount","PaperCountPercentile");
updatePercentiles("topneurologistsnetworkmeasures","ClosenessCentrality","ClosenessPercentile");
updatePercentiles("topneurologistsnetworkmeasures","BetweennessCentrality","BetweennessPercentile");
updatePercentiles("topneurologistsnetworkmeasures","SCImagoProminenceScore","SCImagoProminenceScorePercentile");*/
function updatePercentiles($table,$field,$percentileField){
	$query="SELECT count(".$field.") as totalCount from ".$table;
	$query=mysql_query($query);
	$row=mysql_fetch_array($query);
	$totalCount=$row['totalCount'];
	$query="SELECT atomId,".$field." FROM ".$table;
	echo $query;
	$result=mysql_query($query);
	while($row=mysql_fetch_array($result)){
	
		$getCounts="SELECT count(".$field.") as lowCount from  ".$table." where ".$field." < ".$row[$field];
		$result2=mysql_query($getCounts);
		$row2=mysql_fetch_array($result2);
		$percentile=$row2['lowCount']/$totalCount;
		$updateQuery="UPDATE ".$table." set ".$percentileField."=".$percentile." WHERE atomId=".$row['atomId'];
		echo $updateQuery."<BR>";
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