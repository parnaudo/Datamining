<?php

/*
This script accepts as an input a list of authors names and queries the pubmed database for all the papers attributed to the author. It then parses all the authors listed on the paper, marks the first, second and last author and records them to a sepaarate table. 

Written by Paul Arnaudo 3/10/12 
*/
include("../lib/init.php");	
$dataminer=new dataMiner;
$Start = getTime(); 
//remove old data from tables
//clearAuthorTables();
$authorID=1;



//query to get doctor set, can really be from anywhere, I'm pulling from a temporary doctor table that has first, last and middle 
$table='ocreLarge';
$queryDoctors = "SELECT * FROM $table limit 50";


$result = mysql_query($queryDoctors) or die(mysql_error());
while($row=mysql_fetch_array($result)){
  $query='';
  $count=0;
  $AuthorQuery=array();
  $fullAuthorQuery=array();
  //$query=authorPubmedTransform($row['firstName'],$row['middleName'],$row['lastName']); //your query term, searches for both middle name and middle initial
  $middle=substr($row['middleName'],0,1);
  $filter="Multiple Sclerosis";	
  $fullAuthorQuery[] = "(".$row['firstName']." ".$row['middleName']." ".$row['lastName']. "[Full Author Name] OR ".$row['firstName']." ".$middle." ".$row['lastName']."[FULL AUTHOR NAME])"; 
  $AuthorQuery[]=authorPubmedTransform($row['firstName'],$row['middleName'],$row['lastName']); 	
  $fullAuthorQuery[]=$filter;
  $AuthorQuery[]=$filter; 
  $authorCount=$dataminer->eSearch($AuthorQuery,1);
  if($authorCount < 1){
  	$fullAuthorCount=0;
  }
  else{ 
  	$fullAuthorCount=$dataminer->eSearch($fullAuthorQuery,1);
  }
  // echo $fullAuthorQuery."= ".$fullAuthorCount." : ".$authorCount;	
  $updateQuery="UPDATE $table SET paperCountFullAuthor='".$fullAuthorCount."', paperCount='".$authorCount."' WHERE atomId=".$row['atomId'];
  echo $updateQuery."<BR>";
 mysql_query($updateQuery);

}
$End = getTime(); 
echo "Time taken = ".number_format(($End - $Start),2)." secs";
?>