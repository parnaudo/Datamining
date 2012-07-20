<?php 
include("../lib/init.php");
	$node=1712046;
	$threshold=7;
	$table='ocre';
	$sql = "select column_name from information_schema.columns where table_name='".$table."' AND table_schema='".$connection['db']."'";
	echo $sql;
	$table='edgeCache';
	$select="SELECT atomId FROM bito";
	$result=mysql_query($select);
	while($row=mysql_fetch_array($result)){
			$test=new networkAnalysis($row['atomId'],$table,$threshold);
			$testTargets=$test->reach();
			$updateQuery="UPDATE bito SET reach='".$testTargets."' WHERE atomId=".$row['atomId'];
			echo $updateQuery;
			mysql_query($updateQuery);
	}	
?>