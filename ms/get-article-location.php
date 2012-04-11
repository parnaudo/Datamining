<?php

/*
This script accepts as an input a list of authors names and queries the pubmed database for all the papers attributed to the author. It then parses all the authors listed on the paper, marks the first, second and last author and records them to a sepaarate table. 

Written by Paul Arnaudo 3/10/12 
*/
include("../lib/init.php");	

$Start = getTime(); 
$dataMiner = new dataMiner;
$papers=$mysql->query("SELECT id FROM papers");
foreach($papers as $key){
	foreach($key as $row){
		$efetch = $dataMiner->eFetch($row['id']);
		if($efetch['address']==''){
			echo "NO ADDRESS<BR>";
		}
		else{
			$data = array(
						'address'=>$efetch['address']
			);
		 	$test=$mysql->update('papers',$data,'id='.$row['id']);
		 	var_dump($test);
			
		}
	
	}


}
$End = getTime(); 
echo "Time taken = ".number_format(($End - $Start),2)." secs";
?>