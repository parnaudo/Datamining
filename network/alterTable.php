<?php
include("../lib/init.php");
$sql="select * from largecounts where atomId=401662";
$result=mysql_query($sql);
echo "TEST";
$alterQuery="
alter table $table add column numPublications int(5) DEFAULT 0;
alter table $table add column numPublicationsFirstAuthor int(5) DEFAULT 0;
alter table $table add column numCoauthors int(5) DEFAULT 0;
alter table $table add column reach float DEFAULT 0;
alter table $table add column SCImagoProminenceScore float DEFAULT 0;"
mysql_query($alterQuery);
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