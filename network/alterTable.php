<?php
include("../lib/init.php");
$table="edge";
$threshold=1;
//alterNodeTable($table);

$pub= new publishingInfo(3814943);
$network=new networkAnalysis(3814943,$table,$threshold);
$pubCount=$pub->getPubCount();
$pubCountFirstAuthor=$pub->getPubCount(1);
$authorCount=$pub->getAuthorCount();
$reach=$network->reach();
echo "$pubCount : $pubCountFirstAuthor : $authorCount : $reach";
/*
	if($row['paperCount']==$row['truePaperCount']){
		$author=authorPubmedTransform($row['firstName'],$row['middleName'],$row['lastName']);
	}
	else{
		$author=  $row['firstName']." ".$row['middleName']." ".$row['lastName']." [FULL AUTHOR NAME]";
	}
	echo $author."<BR>";
	$testSql="SELECT * from authors where lastName like '".$row['lastName']."' and atomId!=0";
	echo $testSql;
	$testresult=mysql_query($testSql);
	$testrow=mysql_fetch_array($testresult);
	$test=mysql_num_rows($testresult);
	echo "TEST".$test;
	if($test==1){
	$updateQuery="UPDATE authors set atomId=".$row['atomId']." where id=".$testrow['id'];
	//mysql_query($updateQuery);
	echo "name: ".$testrow['name']." atomId: ".$testrow['atomId']."  | ".$row['atomId']."<BR>";
	}
*/
?>