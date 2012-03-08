<?php

/*
This script accepts as an input a list of authors names and queries the pubmed database for all the papers attributed to the author. It then parses all the authors listed on the paper, marks the first, second and last author and records them to a sepaarate table. 

Written by Paul Arnaudo 2/17/12 
*/
include("lib/init.php");	

$Start = getTime(); 
//remove old data
//clearAuthorTables();

//query to get doctor set, can really be from anywhere
$scoringArray=array( array(NULL,1,2,500,'x'),
					 array(1,NULL,.8,.6,.2),
					 array(2,.8,NULL,.4,.1),
					 array(500,.6,.4,NULL,.1),
					 array('x',.2,.1,.1,.05)
					);

					var_dump($scoringArray);
/*
$getRelationships="SELECT distinct  coAuthor, authorAtomId from authors where query='' and authorAtomId=29682 order by authorAtomId,coAuthor;";
$result=mysql_query($getRelationships);
while($row=mysql_fetch_array($result)){
	$insertQuery="INSERT INTO relationship (coAuthor,authorAtom) VALUES ('".$row['coAuthor']."','".$row['authorAtomId']."')";	
	mysql_query($insertQuery);
	
}*/
$query="SELECT   relationship.id,`authors`.coAuthor, authorAtomId, paper, coAuthorPosition,authorPosition from authors INNER JOIN relationship ON relationship.coAuthor=`authors`.coAuthor AND authorAtom=authorAtomId
where query='' and authorAtomId=29682 order by authorAtomId,coAuthor;
";
$result=mysql_query($query);

while($rowauthor=mysql_fetch_array($result)){
	echo $rowauthor['coAuthorPosition']; echo $rowauthor['authorPosition'];
	$coordinates = scoringTransform($rowauthor['coAuthorPosition'],$rowauthor['authorPosition']);
	$score=$scoringArray[$coordinates[0]][$coordinates[1]];
	$updateQuery="UPDATE relationship SET relationship = (relationship + ".$score.") WHERE id='".$rowauthor['id']."' ";
	mysql_query($updateQuery);

}
					
$End = getTime(); 
echo "Time taken = ".number_format(($End - $Start),2)." secs";
?>