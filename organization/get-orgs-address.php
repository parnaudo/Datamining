<?php 
include("../lib/init.php");
$Start = getTime(); 
$dataminer=new dataMiner;
$queryOrgs = "select address,city,state,zip, count(id) as count from neurologist where paperCount>2 and (middleName!='' OR paperCountFullAuthor > 1) group by address order by count desc ";
$table='organization';
clearTable($table);
$table2='node';
clearTable($table2);
$nodeCount=0;
//$queryDoctors = "select * from neurologist where paperCount>10 and paperCountFullAuthor>6 order by paperCount Desc";
$result = mysql_query($queryOrgs) or die(mysql_error());
while($row=mysql_fetch_array($result)){
	$nodeCount++;
	$insertQuery="INSERT INTO ".$table." (id,address,city,state,zipcode) VALUES ('".$nodeCount."','".mysql_escape_string($row['address'])."','".mysql_escape_string($row['city'])."','".mysql_escape_string($row['state'])."','".mysql_escape_string($row['zipcode'])."')";	
	mysql_query($insertQuery);
	$insertNodeQuery="INSERT INTO ".$table2." (class,infoId) VALUES ('1','".$nodeCount."')";
	mysql_query($insertNodeQuery);
	
		//	$updateQuery="UPDATE topneurologistsnetworkmeasures set firstName='".mysql_escape_string($firstName)."',lastName='".mysql_escape_string($lastName)."',middleName='".mysql_escape_string($middleName)."' where Id='".$row['id']."'";
		//echo $updateQuery;
		//mysql_query($updateQuery) or die ("Error in query: $query. ".mysql_error());
}

$queryDoctors = "select * from neurologist where paperCount>2 and (middleName!='' OR paperCountFullAuthor > 1)";
$resultDoctors = mysql_query($queryDoctors) or die(mysql_error());
while($rowDoctors=mysql_fetch_array($resultDoctors)){
	$nodeCount++;
	$insertNodeQuery="INSERT INTO ".$table2." (class,infoId) VALUES (2,'".$rowDoctors['id']."')";	
	echo $insertNodeQuery."<BR>";
	mysql_query($insertNodeQuery);		
}
//if there are papers, insert an author record	
$End = getTime(); 
echo "Time taken = ".number_format(($End - $Start),2)." secs with rows: ".$count;

?>