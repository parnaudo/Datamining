<?php 
include("../lib/init.php");
$Start = getTime(); 
$dataminer=new dataMiner;
$queryDoctors = "select * from nodes where State='CA' and id=376 order by Hospital";

//$queryDoctors = "select * from neurologist where paperCount>10 and paperCountFullAuthor>6 order by paperCount Desc";
$result = mysql_query($queryDoctors) or die(mysql_error());
while($row=mysql_fetch_array($result)){
	
	if(!empty($row['Hospital'])){
		$select="SELECT id from nodes where Hospital='".mysql_escape_string($row['Hospital'])."' and id!=".$row['id'];
		$selectResult=mysql_query($select) or die(mysql_error());
		$count=mysql_num_rows($selectResult);
		while($selectRow=mysql_fetch_array($selectResult)){
			$insertQuery="INSERT INTO edges (source,target) VALUES ('".$row['id']."','".$selectRow['id']."')";
			echo $insertQuery."<BR>";
			//mysql_query($insertQuery);
			
		}
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