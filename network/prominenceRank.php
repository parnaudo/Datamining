<?php

/*
This script creates an address rank based on the number of instances they have in the data set

Written by Paul Arnaudo 3/29/12 
*/
include("../lib/init.php");	

$mysql = new mysql($connection);
$table="nodeComplete";
$physicians = "select distinct paper,coAuthorPosition,numAuthors,a.id,n.atomId,SJR from nodeComplete n INNER JOIN authors a on a.atomId=n.AtomId
INNER JOIN coAuthorInstance c on c.coAuthor=a.id 
INNER JOIN papers p on p.id=c.paper
LEFT JOIN journal  j ON (j.ISSN=p.ISSN OR j.Title=p.journal)";
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
			$position=1;
    //  	echo "coauthor X";
	}
	
	//echo $row['paper']."  ".$row['coAuthorPosition']."  ".$row['numAuthors']."  ".$row['SJR']."  ".$position;
	$numAuthorModifier= round(($row['numAuthors']-1)/($row['numAuthors']),2);
	if(empty($row['SJR'])){
		$journalRank=.1;
		}
	else{
		$journalRank=$row['SJR'];
		}
		$score=$position*$journalRank*$numAuthorModifier;
		$updateQuery="UPDATE nodeComplete SET SCImagoProminenceScore=(SCImagoProminenceScore+".$score.") WHERE atomId=".$row['atomId'];
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