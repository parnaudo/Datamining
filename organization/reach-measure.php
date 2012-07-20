<?php 
include("lib/init.php");
	$node=1712046;
	$threshold=7.99;
	$table='edgeCache';
	$select="SELECT atomId FROM nodepruned";
	$result=mysql_query($select);
	while($row=mysql_fetch_array($result)){
			$test=new networkAnalysis($row['atomId'],$table,$threshold);
			$testTargets=$test->reach();
			$updateQuery="UPDATE nodepruned SET reach='".$testTargets."' WHERE atomId=".$row['atomId'];
			echo $updateQuery;
			mysql_query($updateQuery);
	}
	$test=new networkAnalysis($node,$table,$threshold);
	$testTargets=$test->reach();
	var_dump($testTargets);
	
?>