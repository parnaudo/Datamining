<?php 
include("../lib/init.php");
	$node=1712046;
	$threshold=1;
	$table='nodecomplete';
	$sql = "select column_name from information_schema.columns where table_name='".$table."' AND table_schema='".$connection['db']."'";
	echo $sql;
	$select="SELECT atomId FROM $table";
	$table='edgeCache';
	
	echo $select;
	$result=mysql_query($select);
	while($row=mysql_fetch_array($result)){
			$test=new networkAnalysis($row['atomId'],$table,$threshold);
			$testTargets=$test->reach();
			$updateQuery="UPDATE nodecomplete SET reach='".$testTargets."' WHERE atomId=".$row['atomId'];
			echo $updateQuery;
			mysql_query($updateQuery);
	}	
?>