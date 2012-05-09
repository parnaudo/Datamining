<?php 
include("../lib/init.php");
$Start = getTime(); 
$table='node';
$dataminer=new dataMiner;
$nodeQuery= "SELECT id from node order by id desc limit 1";
$nodeResult=mysql_query($nodeQuery);
$nodeRow=mysql_fetch_array($nodeResult);
$nodeCount=$nodeRow['id'];
$queryDoctors = "select node.id,Hospital from node INNER JOIN individual ON node.infoId=individual.id where Specialty='Cardiologist' and class=2";
//$queryDoctors = "select * from neurologist where paperCount>10 and paperCountFullAuthor>6 order by paperCount Desc";
$result = mysql_query($queryDoctors) or die(mysql_error());
while($row=mysql_fetch_array($result)){
	$nodeCount++;
	if(!empty($row['Hospital'])){
		$testQuery="SELECT node.id FROM node INNER JOIN organization ON node.infoId=organization.id WHERE name = '".mysql_escape_string($row['Hospital'])."'";

		$testResult = mysql_query($testQuery);
		$testRow=mysql_fetch_array($testResult);
		//echo $testRow['id']." ".$row['Hospital'];
		$insertEdgeQuery="INSERT INTO edge (source,target,direction) VALUES ('".$testRow['id']."','".$row['id']."','Directed')";
		mysql_query($insertEdgeQuery);
		//mysql_query($insertEdgeQuery);
	}
	else{
		echo "NO HOSPITAL";
	
	}
		//	$updateQuery="UPDATE topneurologistsnetworkmeasures set firstName='".mysql_escape_string($firstName)."',lastName='".mysql_escape_string($lastName)."',middleName='".mysql_escape_string($middleName)."' where Id='".$row['id']."'";
		//echo $updateQuery;
		//mysql_query($updateQuery) or die ("Error in query: $query. ".mysql_error());
}
//if there are papers, insert an author record	
$End = getTime(); 
echo "Time taken = ".number_format(($End - $Start),2)." secs with rows: ".$count;

?>