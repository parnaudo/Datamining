<?php 
include("lib/init.php");
function
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
		insertEdge($valueArray,'edgeCache');
	
	}
}


?>