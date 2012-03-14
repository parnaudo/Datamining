<?php

/*
This script accepts as an input a list of authors names and queries the pubmed database for all the papers attributed to the author. It then parses all the authors listed on the paper, marks the first, second and last author and records them to a sepaarate table. 

Written by Paul Arnaudo 2/17/12 
*/
include("lib/init.php");	
$table='relationship';
$Start = getTime();
$lastEntry=''; 
$matchFlag=0;
//remove old data
//clearAuthorTables();

//query to get doctor set, can really be from anywhere
$scoringArray=array( array(NULL,1,2,500,'x'),
					 array(1,NULL,.8,.6,.2),
					 array(2,.8,NULL,.4,.1),
					 array(500,.6,.4,NULL,.1),
					 array('x',.2,.1,.1,.05)
					);

					

clearTable($table);
$getRelationships="SELECT distinct  coAuthor, authorAtomId from coAuthorInstance where query='' and authorAtomId=82387 order by authorAtomId,coAuthor DESC";
$result=mysql_query($getRelationships);
//Get distinct author and coauthor records
while($row=mysql_fetch_array($result)){
	$insertQuery='';
	//see if its the same name without a middle initial
	if(stripos($lastEntry,$row['coAuthor'])===0){
		$matchFlag=1;
		
	}
	else{
		$matchFlag=0;
		$insertQuery="INSERT INTO relationship (coAuthor,authorAtom) VALUES ('".$row['coAuthor']."','".$row['authorAtomId']."')";
	}
	//insert into relationship table
	mysql_query($insertQuery);
	
	$coAuthorQuery="SELECT numAuthors, coAuthorPosition, authorPosition from coAuthorInstance INNER JOIN papers on papers.id=paper  where query='' AND coAuthor='".$row['coAuthor']."' AND authorAtomId=".$row['authorAtomId'];
	echo $coAuthorQuery;
	$coAuthorResult=mysql_query($coAuthorQuery);

	//get all coauthor instances with author and coauthor
	while($rowauthor=mysql_fetch_array($coAuthorResult)){
		$numAuthorModifier= round(1/($rowauthor['numAuthors']-1),2);
		$coordinates = scoringTransform($rowauthor['coAuthorPosition'],$rowauthor['authorPosition']);
		$score=$scoringArray[$coordinates[0]][$coordinates[1]];
		$score=($score*$numAuthorModifier)*10;
		if($matchFlag===1){
			$updateQuery="UPDATE relationship SET relationship = (relationship + ".$score."),paperCount=(paperCount+1) WHERE coAuthor='".$lastEntry."' AND authorAtom=".$row['authorAtomId'];
		}
		else{
			$updateQuery="UPDATE relationship SET relationship = (relationship + ".$score."),paperCount=(paperCount+1) WHERE coAuthor='".$row['coAuthor']."' AND authorAtom=".$row['authorAtomId'];
		}
		echo $updateQuery."<BR>";
		mysql_query($updateQuery);
	
		
	}
	$lastEntry=$row['coAuthor'];
}				
$End = getTime(); 
echo "Time taken = ".number_format(($End - $Start),2)." seconds";
?>