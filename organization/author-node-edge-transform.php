<?php


include("../lib/init.php");
$Start = getTime(); 

$dataminer=new dataMiner;
function authorEdgeTransform(){
	$getRelationshipNodes="SELECT coAuthor, authorAtom, type, relationship,paperCount from relationship ";
	$result=mysql_query($getRelationshipNodes);
	while($row=mysql_fetch_array($result)){
		$query="SELECT distinct node.id from node INNER JOIN authors a ON a.atomId=node.infoId where a.id=".$row['coAuthor'];

		$sourceResult=mysql_query($query);
		$sourceRow=mysql_fetch_array($sourceResult);
		$sourceAtom=$sourceRow['id'];	
		$query="SELECT distinct node.id from node INNER JOIN authors a ON a.atomId=node.infoId where a.id=".$row['authorAtom'];
		$targetResult=mysql_query($query);
		$targetRow=mysql_fetch_array($targetResult);
		$targetAtom=$targetRow['id'];	
		$insertQuery="INSERT INTO edge (source,target,weight,direction,class) VALUES('".$sourceAtom."','".$targetAtom."','".$row['relationship']."','".$row['type']."','1')";

		mysql_query($insertQuery);
	}
}

authorEdgeTransform();
$End = getTime(); 
echo "Time taken = ".number_format(($End - $Start),2)." secs";
?>