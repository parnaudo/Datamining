<?php 
include("lib/init.php");
$Start = getTime(); 
$dataminer=new dataMiner;
$queryNodes = "select source, count(target) as degree from edge group by source order by degree desc;";
//$queryDoctors = "select * from neurologist where paperCount>10 and paperCountFullAuthor>6 order by paperCount Desc";
$result = mysql_query($queryNodes) or die(mysql_error());
$allNodes=array();
$test=array();
while($row=mysql_fetch_array($result)){
	$test[]=getNode($row['source']);	
	/*print_r($test);
	echo "<BR>";*/

}
$array1=array(2,6,8,10,11);
$array2=array(6,8,9);
$array3=array(4,6,7);
$array4=array(1,3,5);
$array5=array(12,13,14);
$set=array($array1,$array2,$array3,$array4,$array5);

for($q=0;$q < 1; $q++){
	$newSet=arrayCompare($test);	
	$set[]=$newSet;
	print_r($newSet);
	echo "<BR>";
	$q++;
}
	
print_r($newSet);
function arrayCompare($set){
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
				$bestIndex=array($i,$k);
				$bestCount=$count;
			}
		}
	}
	return $bestSet;
}


function saturationNodes($set,$number){

}

function getNode($node){
	$targets=array();
	$filter="WHERE source=".$node;
	$nodeSelect="SELECT target from edge ".$filter;
	$result=mysql_query($nodeSelect);
	while($row=mysql_fetch_array($result)){	
		$targets[]=$row['target'];
		//echo $row['target']." FROM NODE: ".$node."<BR>";
	}
	return $targets;
}
//if there are papers, insert an author record	
$End = getTime(); 
echo "Time taken = ".number_format(($End - $Start),2)." secs";

?>