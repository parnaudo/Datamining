<?php 
include("lib/init.php");
$Start = getTime(); 
clearTable('edgeCache');
$table="relationship";
$dataminer=new dataMiner;
$query="SELECT distinct target from edge where class=1";
$result = mysql_query($query) or die(mysql_error());
$sources=array();
while($row=mysql_fetch_array($result)){
	$count=0;
	echo $row['target']." IS THE TARGET<BR>";
	$getSources="SELECT source from edge where class=1 and target=".$row['target'];
	$resultSources = mysql_query($getSources) or die(mysql_error());
	$testRows=mysql_num_rows($resultSources);
	$sources=array();
	if($testRows > 1){	
		while($rowSources=mysql_fetch_array($resultSources)){
			$sources[]=$rowSources['source'];
			$count++;
	
		}
		for($i=0;$i < sizeof($sources);$i++){
			for($k=0;$k <sizeof($sources);$k++){
				if($i!==$k){
					$insertEdge="INSERT INTO edgeCache (source,target,direction,weight) VALUES ('".$sources[$i]."','".$sources[$k]."','Undirected','8.0')";
					echo $insertEdge."<BR>";
					mysql_query($insertEdge);
				
				}
			}
		}
		echo "NEW INSTITUTION<BR>";
	}
	
}
$query="select * from edge where class=2";
$result = mysql_query($query) or die(mysql_error());
$sources=array();
while($row=mysql_fetch_array($result)){
	$test=edgeExists($row['source'],$row['target'],'edgeCache');
	if($test>0){
		$updateQuery="UPDATE edgeCache set weight=(weight+".$row['weight'].") where source=".$row['source']." AND target=".$row['target'];
		mysql_query($updateQuery);
	}
	else{
		$valueArray=array(
			'source'=>$row['source'],
			'target'=>$row['target'],
			'weight'=>$row['weight'],
		);
		insertQuery($valueArray,'edgeCache');
	
	}
}
$query="select * from edge where class=3";
$result = mysql_query($query) or die(mysql_error());
$sources=array();
while($row=mysql_fetch_array($result)){
	$test=edgeExists($row['source'],$row['target'],'edgeCache');
	if($test>0){
		$updateQuery="UPDATE edgeCache set weight=(weight+".$row['weight'].") where source=".$row['source']." AND target=".$row['target'];
		mysql_query($updateQuery);
	}
	else{
		$valueArray=array(
			'source'=>$row['source'],
			'target'=>$row['target'],
			'weight'=>$row['weight'],
		);
		insertQuery($valueArray,'edgeCache');
	
	}
}


$End = getTime(); 
echo "Time taken = ".number_format(($End - $Start),2)." secs";

?>