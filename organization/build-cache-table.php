<?php 
include_once("../lib/init.php");
$Start = getTime(); 
$table="edgeCache";
clearTable($table);

$dataminer=new dataMiner;
/*$query="SELECT distinct target from edge where class=1";
$result = mysql_query($query) or die(mysql_error());
$sources=array();
$updateCount=0;
//Transform organizational edges (which are physician->organization) into physician->physician edges
while($row=mysql_fetch_array($result)){
	$count=0;
	echo $row['target']." IS THE TARGET<BR>";
	$getSources="SELECT source,weight,direction from edge where class=1 and target=".$row['target'];
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
					$valueArray=array(
						'source'=>$sources[$i],
						'target'=>$sources[$k],
						'weight'=>8,
						'direction'=>'Directed',
						'certainty'=>1
					);
					insertQuery($valueArray,'edgeCache');				
				}
			}
		}
		}
	
}*/
//Get the rest of the edges and add them up.
$query="select * from edge ";
$result = mysql_query($query) or die(mysql_error());
$sources=array();
while($row=mysql_fetch_array($result)){
	$test=edgeExists($row['source'],$row['target'],'edgeCache','');
	if($test>0){
		$updateQuery="UPDATE ".$table." set weight=(weight+".$row['weight']."), certainty=(certainty+1) where source=".$row['source']." AND target=".$row['target'];
		echo $updateQuery;
		mysql_query($updateQuery);
		$updateCount++;
	}
	else{
		$valueArray=array(
			'source'=>$row['source'],
			'target'=>$row['target'],
			'weight'=>$row['weight'],
			'direction'=>'Directed',
			'certainty'=>1
		);
		insertQuery($valueArray,'edgeCache');
	
	}
}

$edgeTypes="SELECT DISTINCT class FROM edge";
$edgeCount=getRowCount($edgeTypes);
//create certainty measure
certainty($edgeCount);

$End = getTime(); 
echo "Time taken = ".number_format(($End - $Start),2)." secs with $updateCount updates";

?>