<?php 
include("../lib/init.php");
$Start = getTime(); 
$table="edge";
$dataminer=new dataMiner;
$queryNodes = "select distinct source, count(target) as degree from ".$table." group by source order by degree desc;";
//$queryDoctors = "select * from neurologist where paperCount>10 and paperCountFullAuthor>6 order by paperCount Desc";
$result = mysql_query($queryNodes) or die(mysql_error());
$allNodes=array();
$test=array();
while($row=mysql_fetch_array($result)){
	$node=$row['source'];
	$test[]=getNode($row['source'],$table);
/*	print_r($test);
	echo "<BR>";*/
}
	
$array1=array(2,6,8,10,11);
$array2=array(6,8,9);
$array3=array(4,6,7);
$array4=array(1,3,5);
$array5=array(12,13,14);
$array6=array(1);
$set=array($array1,$array2,$array3,$array4,$array5,$array6);

//var_dump($set);
$test2=saturationNodes($test,3);
var_dump($test2);
function getNode($node,$table){
//Get all targets for nodes in a bidirectional edge table, will be ordered in terms of outdegree
	$filter="WHERE source=".$node;
	$nodeSelect="SELECT target from ".$table." ".$filter;
	$result=mysql_query($nodeSelect);
	while($row=mysql_fetch_array($result)){	
		$targets[]=intval($row['target']);
	}
	$nodeInfo=array(
		'index'=>intval($node),
		'values'=>$targets
	);
//return index as source node, values will be all target IDs
	return $nodeInfo;
}
function saturationNodes($set,$number){
	$count=0;
/*
loads info from the array created in getNodes function. This is then split into two arrays, one for storing the index, and one for creating a two dimensional array that will be compared in arrayCompare. 
also accepts the number of nodes the user is looking to find.

*/	
	foreach($set as $key=>$value){
		$compareArray[$count]=$value['values'];
		$indexArray[$value['index']]=$count;
		$count++;
	}
	$bestNodes=array();
//Loop through set of array elements and compare the best with the previous set, return the best set of nodes
	for($q=0;$q < $number; $q++){
		$newSet=arrayCompare($compareArray,$indexArray);	
		$compareArray[]=$newSet['bestSet'];
		$bestNodes=is_array($bestNodes) && is_array($newSet['bestIndex']) ? array_unique(array_merge($bestNodes,$newSet['bestIndex'])) : $bestNodes;
//take best set
		$q++;
	}
	return $bestNodes;
}

function arrayCompare($set,$index){
	$bestSet=array();
	$testSet=array();

	$bestCount=0;
	$count=0;
//compare all sets of nodes and look for the best indexes to return	
	for($i=0;$i < sizeof($set);$i++){
		for($k=0;$k <sizeof($set);$k++){
			if($k!==$i){
				$testSet=array_unique(array_merge($set[$i],$set[$k]));
				$count=count($testSet);
			}
			if($count > $bestCount){
				$bestSet=$testSet;
				$bestIndex=array_filter(array(array_search($i,$index),array_search($k,$index)));
				
				$bestCount=$count;
			}
			
		}
	}
	$return=array(
		'bestSet'=>$bestSet,
		'bestIndex'=>$bestIndex
	);
//return the best index found in the set along with the best set which can be used to process total amount of coverage later
	return $return;
}





$End = getTime(); 
echo "Time taken = ".number_format(($End - $Start),2)." secs";

?>