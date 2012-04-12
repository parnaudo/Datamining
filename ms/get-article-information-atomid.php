<?php 
include("../lib/init.php");	
$dataCounts=array();
$dataBetweenness=array();
$dataCloseness=array();
$result = $mysql->query("select distinct name,topneurologistsnetworkmeasures.Id,count(paper) as paperCount,BetweennessCentrality,ClosenessCentrality from topneurologistsnetworkmeasures 
INNER JOIN coauthorinstance ON topneurologistsnetworkmeasures.Id=coauthorinstance.coauthor where topneurologistsnetworkmeasures.Id=1
group by topneurologistsnetworkmeasures.id
order by count(paper) desc");
foreach($result as $key){
	foreach($key as $row){
		print "UPDATE topneurologistsnetworkmeasures SET `paperCount`='".$row['paperCount']."' WHERE Id='".$row['Id']."'";
		$test=$mysql->query("UPDATE topneurologistsnetworkmeasures SET `paperCount`='".$row['paperCount']."' WHERE Id='".$row['Id']."'");
		/*	$data = array(
						'paperCount'=>$row['paperCount']
			);
		 	$test=$mysql->update('topneurologistsnetworkmeasures',$data,'Id='.$row['Id']);
		*/
		
	}
}
/*
function getPercentiles($data){	
	$percentileArray=array();
	$percentiles=array(10,20,30,40,50,60,70,80,90);
	foreach($percentiles as $key){
		$percentile=percentile($data,$key);
		$percentileArray[$key]=$percentile;
	}
	return $percentileArray;
}
*/
?> 