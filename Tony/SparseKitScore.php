<?php 
include("../lib/init.php");
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
$table="sparsekitocre";
authorCounts($table);
function authorCounts($table){

	$sql="select distinct s.atomId,paper,authorPosition, authorCount from $table s INNER JOIN papers on paper=id";
	echo $sql;
	$result=mysql_query($sql);
	while($row=mysql_fetch_array($result)){
		$authorCount=$row['authorCount'];
		$authorPosition=floatval($row['authorPosition']);
		echo gettype($authorPosition);
		$value=((floatval(1)/$authorPosition) * (1/($authorCount)) )*100;
		echo "author count: $authorCount & author position: $authorPosition = $value <BR>";
		$updateQuery="UPDATE $table set weight=$value where atomId=".$row['atomId']." AND paper=".$row['paper'];
		echo $updateQuery;
		mysql_query($updateQuery);
	
	}	
}	
?>