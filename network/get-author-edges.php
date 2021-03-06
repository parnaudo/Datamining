<?php

/*
This script builds relationship scores based off of authorship instances mined from pubmed.

Written by Paul Arnaudo 3/19/12 
*/
include_once("../lib/init.php");	
$table='relationship';
$Start = getTime();
$lastEntry=''; 
$matchFlag=0;
//remove old data
clearTable($table);
clearTable("edge");
//clearAuthorTables();

//query to get doctor set, can really be from anywhere
$scoringArray=array( array(NULL,1,2,500,'x'),
					 array(1,NULL,10,8,6),
					 array(2,10,NULL,4,3),
					 array(500,8,4,NULL,2),
					 array('x',6,3,2,1)
					);

					

clearTable($table);

//include node n to make sure we don't get unwanted nodes 
$getPapers="SELECT DISTINCT `authors`.id,paper,authorCount,authorPosition FROM coAuthorInstance INNER JOIN authors ON coAuthor=authors.id INNER JOIN papers ON papers.articleId=coAuthorInstance.paper  WHERE authors.atomId!=0 and duplicateFlag=0";
$result=mysql_query($getPapers);
//Get distinct information on authors we are looking to search for
while($row=mysql_fetch_array($result)){
	$author=$row['id'];
	$paper=$row['paper'];
	$numAuthors=$row['authorCount'];
	$authorPosition=$row['authorPosition'];
	$getInstances="SELECT coAuthor, coAuthorPosition,query,duplicateFlag FROM coAuthorInstance inner join authors on coAuthorInstance.coAuthor=authors.id where paper='".$paper."' AND coAuthorPosition!='".$authorPosition."' ";
	$instanceResult=mysql_query($getInstances);
//GET all distinct coauthor information for each author id and paper
	while($rowInstance=mysql_fetch_array($instanceResult)){
	echo $rowInstance['duplicateFlag'];
		if($rowInstance['query']!='' && $rowInstance['duplicateFlag']==0){
			$targetDocFlag=1;	
		}
		else{
			$targetDocFlag=0;	
		}
		$numAuthorModifier= round(1/($numAuthors-1),2);
		$coordinates = scoringTransform($rowInstance['coAuthorPosition'],$authorPosition);
		$score=$scoringArray[$coordinates[0]][$coordinates[1]];
		//echo "AUTHOR POS: ".$authorPosition." COAUTHOR POS: ".$rowInstance['coAuthorPosition']."SCORE.".$score. " NUM AUTHOR MOD: ".$numAuthorModifier. " PAPER ID: ".$paper."<BR>";
		$score=($score*$numAuthorModifier);
		$relationTest= "SELECT id FROM relationship WHERE coAuthor=".$rowInstance['coAuthor']." AND authorAtom=".$author;
		$resultRelation=mysql_query($relationTest);
		$relationFlag = mysql_num_rows($resultRelation);
//Look for instances already
		if($targetDocFlag > 0){
//ONly looking for physicians in group	
			if($relationFlag > 0 ){
				$rowRelation=mysql_fetch_array($resultRelation);
				$relation=$rowRelation['id'];
				$updateRelationQuery="UPDATE relationship SET relationship= (relationship + ".$score."), paperCount=(paperCount+1) WHERE id=".$rowRelation['id'];
				//echo $updateRelationQuery."<br>";
				mysql_query($updateRelationQuery);
				}	
		    else {
				$insertRelationQuery="INSERT INTO relationship (coAuthor, authorAtom, relationship, paperCount,targetDoc) VALUES ('".$rowInstance['coAuthor']."','".$author."','".$score."','1','".$targetDocFlag."')";

				echo $insertRelationQuery."<BR>";
				mysql_query($insertRelationQuery);			
				}
		}
	}


}
transferAuthorEdges('relationship');				
$End = getTime(); 
echo "Time taken = ".number_format(($End - $Start),2)." seconds";
?>