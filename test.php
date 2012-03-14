<?php 
include("lib/init.php");	
$author="arnaudo pa";
$queryDoctors = "SELECT atomId, firstName,middleName, lastName from tempdoc where lastName!='' AND atomId=372548 ";

$result = mysql_query($queryDoctors) or die(mysql_error());
while($row=mysql_fetch_array($result)){
  $query='';
  $middle=substr($row['middleName'],0,1);
  echo $row['middleName']. " MIDDLE IS ".$middle;
}
?>