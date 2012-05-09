<?php 
include("lib/init.php");
$Start = getTime(); 
$dataminer=new dataMiner;
$queryDoctors = "select * from relationship";
$result = mysql_query($queryDoctors) or die(mysql_error());
while($row=mysql_fetch_array($result)){
	$id=$row['authorAtom'];
	$source=relationshipToEdge($id);
	$id=$row['coAuthor'];
	$target=relationshipToEdge($id);
	if(empty($target)){
		echo "EMPTY ID IS: ".$id."<BR>";
	}
	$insertQuery="INSERT INTO edge (source, target, direction, weight, class) VALUES (".$source.",".$target.",'Directed',".$row['relationship'].",2";
	echo $insertQuery."<BR>";
}

	
?>