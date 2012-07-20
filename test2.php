<?php 
include("lib/init.php");
/*
clearTable('yearCounts');
$getAtoms="SELECT distinct atomId from authors where atomId!=0";
$result=mysql_query($getAtoms);
while($row=mysql_fetch_array($result)){
	$author=new publishingInfo($row['atomId']);
	$insertQuery="INSERT INTO yearCounts (atomId) VALUES ('".$row['atomId']."')";
	mysql_query($insertQuery);
	$papers=$author->getAuthorInstances();
	foreach($papers as $paperId){

		$year=$author->getYear($paperId);
		$updateQuery="UPDATE yearCounts set ".$year."year=(".$year."year+1) where atomId=".$row['atomId'];
		mysql_query($updateQuery);
	}
}


$test=' ';
echo date('Y', strtotime($test));*/
//$year=$author->getYear(7968091);
/*

$node=1712046;
$test=new publishingInfo($node);
$paper=18397361;
$sql="SELECT paper from coAuthorInstance c";
$sqlResult=mysql_query($sql);
$yearArray=array();
while($sqlRow=mysql_fetch_array($sqlResult)){	
	$count=$test->getYear($sqlRow['paper']);
	if($count!==FALSE && $count!==TRUE){
		if(in_array($count,$yearArray)){
			
		}
		else{
		//echo "YEAR:".$count."<BR>";
			$yearArray[]=$count;
			//var_dump($yearArray);
		}
	}
}
sort($yearArray);
$table="yearCounts";
foreach($yearArray as $column){

		$alterTable="ALTER TABLE ".$table." ADD ".$column."year int(10) DEFAULT 0";
		echo $alterTable."<BR>";
		mysql_query($alterTable);

}
*/
	$sql="SELECT atomId from node n";
	$result=mysql_query($sql);
	while($row=mysql_fetch_array($result)){
			$coAuthorCount=0;
			$sql="select paper from coAuthorInstance c INNER JOIN authors a on coAuthor=a.id where atomId=".$row['atomId'];
			$sqlResult=mysql_query($sql);
			while($sqlRow=mysql_fetch_array($sqlResult)){

				$coAuthorSelect= "SELECT count(id) as count from coAuthorInstance where paper=".$sqlRow['paper'];
				$coAuthorResult=mysql_query($coAuthorSelect);
				while($coAuthorRow=mysql_fetch_array($coAuthorResult)){
					$coAuthorCount=$coAuthorCount+($coAuthorRow['count']-1);
				}
			}	
			$updateQuery="UPDATE node SET numCoauthors='".$coAuthorCount."' WHERE atomId=".$row['atomId'];
			echo $updateQuery."<BR>";
			mysql_query($updateQuery);
			$sql="select count(atomId) as count from coAuthorInstance c INNER JOIN authors a on coAuthor=a.id where atomId=".$row['atomId'];
			$sqlResult=mysql_query($sql);
			while($sqlRow=mysql_fetch_array($sqlResult)){
				$updateQuery="UPDATE node SET numPublications='".$sqlRow['count']."' WHERE atomId=".$row['atomId'];
				echo $updateQuery."<BR>";
				mysql_query($updateQuery);
			}

			$sql="select count(atomId) as count from coAuthorInstance c INNER JOIN authors a on coAuthor=a.id where atomId=".$row['atomId']." and authorPosition=1";
			$sqlResult=mysql_query($sql);
			while($sqlRow=mysql_fetch_array($sqlResult)){
				$updateQuery="UPDATE node SET numPublicationsFirstAuthor='".$sqlRow['count']."' WHERE atomId=".$row['atomId'];
				echo $updateQuery."<BR>";
				mysql_query($updateQuery);
			}
	}
?>