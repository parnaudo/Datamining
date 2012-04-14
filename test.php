<?php

/*
This script creates an address rank based on the number of instances they have in the data set

Written by Paul Arnaudo 3/29/12 
*/
include("lib/init.php");	

$mysql = new mysql($connection);

$physicians = "select distinct paper, coAuthorPosition,numAuthors,topneurologistsnetworkmeasures.id, SJR from coauthorinstance INNER JOIN papers on papers.id=paper  INNER JOIN topneurologistsnetworkmeasures on coauthorinstance.coAuthor=topneurologistsnetworkmeasures.id 
LEFT JOIN journal ON (journal.ISSN=papers.ISSN OR journal.Title=papers.journal)";
$result=mysql_query($physicians);
while($row=mysql_fetch_array($result)){
	$updateQuery='';
	$journalRank=.1;
	switch ($row['coAuthorPosition']) {
    	case 1:
   //     echo "coauthor 1";
			$position=10;
        	break;
   		 case 2:
  //      echo "coauthor 2";
			$position=8;
       		 break;
    	 case 500:
			$position=6;
   //     echo "coauthor 500";
       		 break;
   		 default:
			$position=4;
    //  	echo "coauthor X";
	}
	
	//echo $row['paper']."  ".$row['coAuthorPosition']."  ".$row['numAuthors']."  ".$row['SJR']."  ".$position;
	$numAuthorModifier= round(1/($row['numAuthors']-1),2);
	if(empty($row['SJR'])){
		$journalRank=.1;
		}
	else{
		$journalRank=$row['SJR'];
		}
		$score=$position*$journalRank*$numAuthorModifier;
		$updateQuery="UPDATE topneurologistsnetworkmeasures SET SCImagoProminenceScore=(SCImagoProminenceScore+".$score.") WHERE Id=".$row['id'];
		mysql_query($updateQuery);
		echo $updateQuery;
}


/*
foreach($physicians as $key){
	foreach($key as $row){
		echo $row['paper']."  ".$row['coAuthorPosition']."  ".$row['numAuthors']."  ".$row['id']."<BR>";
	
	}
}
*/
?>