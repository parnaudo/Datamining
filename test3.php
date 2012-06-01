<?php 
include("lib/init.php");
$Start = getTime(); 
$table="edge_copy";
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
$test=saturationNodes($test,10);
var_dump($test);
function getNode($node,$table){

	$filter="WHERE source=".$node;
	$nodeSelect="SELECT target from ".$table." ".$filter;
	$result=mysql_query($nodeSelect);
	while($row=mysql_fetch_array($result)){	
		$targets[]=intval($row['target']);
	//	echo $row['target']." FROM NODE: ".$node."<BR>";
	}
	$nodeInfo=array(
		'index'=>intval($node),
		'values'=>$targets
	);

	return $nodeInfo;
}
function saturationNodes($set,$number){
	$count=0;
	foreach($set as $key=>$value){
		$compareArray[$count]=$value['values'];
		$indexArray[$value['index']]=$count;
		$count++;
	}
	$bestNodes=array();
	for($q=0;$q < $number; $q++){
		$newSet=arrayCompare($compareArray,$indexArray);	
		$compareArray[]=$newSet['bestSet'];
		$bestNodes=is_array($bestNodes) && is_array($newSet['bestIndex']) ? array_unique(array_merge($bestNodes,$newSet['bestIndex'])) : $bestNodes;

		$q++;
	}

	return $bestNodes;
}

function arrayCompare($set,$index){
	$bestSet=array();
	$testSet=array();

	$bestCount=0;
	$count=0;
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
	return $return;
}





$End = getTime(); 
echo "Time taken = ".number_format(($End - $Start),2)." secs";

?>