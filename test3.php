<?php 
include("lib/init.php");
$Start = getTime(); 
clearTable('edgeCache');
$table="relationship";
$dataminer=new dataMiner;
$query="SELECT * from education where type like 'md%' ";
$result = mysql_query($query) or die(mysql_error());
$sources=array();
while($row=mysql_fetch_array($result)){
$search=array("medical center","school of medicine","college of medicine","medical school");
	$name=substr($row['type'],0,2);
	
	$updateQuery="UPDATE education SET type='".mysql_escape_string($name)."' WHERE id=".$row['id'];
	echo $updateQuery."";
	mysql_query($updateQuery);
}
$End = getTime(); 
echo "Time taken = ".number_format(($End - $Start),2)." secs";

?>