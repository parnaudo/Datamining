<?php
include("../lib/init.php");
$sql="select lastName,atomId from ocre where atomId NOT IN (select distinct atomId from authors where atomId!=0)";
$result=mysql_query($sql);
while($row=mysql_fetch_array($result)){	
	$testSql="SELECT * from authors where lastName like '".$row['lastName']."'";
	echo $testSql;
	$testresult=mysql_query($testSql);
	$testrow=mysql_fetch_array($testresult);
	$test=mysql_num_rows($testresult);
	echo "TEST".$test;
	$updateQuery="UPDATE authors set atomId=".$row['atomId']." where id=".$testrow['id'];
	echo $updateQuery;
	echo "name: ".$testrow['name']." atomId: ".$testrow['atomId']."  | ".$row['atomId']."<BR>";
}
?>