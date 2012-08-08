<?php

/*
This script creates an address rank based on the number of instances they have in the data set

Written by Paul Arnaudo 3/29/12 
*/
include("../lib/init.php");
$mysql = new mysql($connection);	
$dataMiner=new dataMiner;
$query="SELECT id from papers";
$rows=$mysql->query($query);
foreach($rows as $key){
	foreach($key as $variable){
		$test=$dataMiner->eSummary($variable['id']);
		$data=array(
					'ISSN'=>$test['ISSN']
					);			
		$mysql->update('papers',$data,"id=".$variable['id']);
	}
}


?>