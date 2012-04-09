<?php

/*
This script creates an address rank based on the number of instances they have in the data set

Written by Paul Arnaudo 3/29/12 
*/
include("lib/init.php");	
//get average and standard deviation
$queryAverage = "select avg(count) as average,STD(count) as stdv from (select count(id) as count from neurologist where paperCount!=0 group by address having count > 2 order by count desc) as counts";

$result = mysql_query($queryAverage) or die(mysql_error());
while($row=mysql_fetch_array($result)){
  $query='';
  $count=0;

	$stdev=$row['stdv'];
	$avg=$row['average'];
   }
//get all instances greater than 2 to lower set   
$queryCounts="select address,count(id) as count from neurologist where paperCount!=0 group by address having count > 2 order by count desc";
$result = mysql_query($queryCounts);
while($row=mysql_fetch_array($result)){
	$count=$row['count'];
	$address=$row['address'];
	if($count >= $avg && $count >= ($avg + $stdev)){
	
		echo $count ." > ". ($avg+$stdev)."<BR>";
		$updateQuery="UPDATE neurologist set addressRank='1' where address like '%".$address."%'";
		echo $updateQuery;	
	
	}
	elseif($count >= $avg && $count <= ($avg + $stdev)){
	
		echo $count ." < ". ($avg+$stdev)." AND ". $count ." > ". $avg ."<BR>";
		$updateQuery="UPDATE neurologist set addressRank='2' where address like '%".$address."%'";
	
	
	
	}
	elseif($count <= $avg && $count >= ($avg - $stdev)){
	
		echo $count ." > ". ($avg-$stdev)." AND ". $count ." < ". $avg ."<BR>";
		$updateQuery="UPDATE neurologist set addressRank='3' where address like '%".$address."%'";
	
	
	
	}
	else{
		$updateQuery="UPDATE neurologist set addressRank='4' where address='".$address."'";
		echo $count ."<". $avg."<BR>";
	}
  mysql_query($updateQuery);
}   
 
?>