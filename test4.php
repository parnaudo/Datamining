<?php 
include("lib/init.php");
$Start = getTime(); 
$dataminer=new dataMiner;
$queryDoctors = "select * FROM ACS where id=14850";
$result = mysql_query($queryDoctors) or die(mysql_error());
while($row=mysql_fetch_array($result)){
  	$query=array();
	$count=0;
	$query[]=authorPubmedTransform($row['firstName'],$row['middleName'],$row['lastName']); //your query term, searches for both middle name and middle initials	
	$query[]=$row['firstName']." ".$row['middleName']." ".$row['lastName']. "[FULL AUTHOR NAME]";
	$uids=$dataminer->eSearch($query,1);
	echo $uids."<BR>";
}
	
?>