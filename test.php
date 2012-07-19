<?php
include("lib/init.php");
$Start = getTime(); 
//remove old data from tables
$table="CIUpubinstances";
//clearTable($table);
$dataminer=new dataMiner;
$authorID=1;
$filter="(URTICARIA [MESH FIELDS] OR URTICARIA [Title] OR URTICARIA [Journal])";
//query to get doctor set, can really be from anywhere, I'm pulling from a temporary doctor table that has first, last and middle 
//$queryDoctors = "select * from neurologist where id IN (1760442420)";
$queryDoctors = "select AtomId from TopPhysicians071212Ocre";
$result = mysql_query($queryDoctors) or die(mysql_error());
while($row=mysql_fetch_array($result)){
	$author=new publishingInfo($row['AtomId']);
	$count=$author->getPubCount();
	$updateQuery="UPDATE TopPhysicians071212Ocre SET paperCount=".$count." WHERE AtomId=".$row['AtomId'];
	echo $updateQuery."<BR>";
}
echo "ALL DONE";
?>