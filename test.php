<?php 
include("lib/init.php");	
$author="arnaudo pa";
$noMiddleInitial="Laird J";
$test=strpos($middleInitial," ");
$testQuery= "SELECT id FROM authors WHERE name LIKE '".$author."'";
$result=mysql_query($testQuery);
$rows = mysql_num_rows($result);
if($rows > 0){
	echo "Rows";
}
else {
	echo "none";
}
?>