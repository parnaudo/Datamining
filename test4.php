<?php


include("lib/init.php");
$Start = getTime(); 

//clearTable('edgeCache');
$table="bitoPhotos";
$dataminer=new dataMiner;
$query="SELECT * from ".$table;
$result = mysql_query($query) or die(mysql_error());
while($row=mysql_fetch_array($result)){

		$updateQuery="UPDATE bito SET photo='".$row['photo']."' where atomId=".$row['atomId'];
		echo $updateQuery."<BR>"; 
		mysql_query($updateQuery);

		
//	mysql_query($updateQuery);
}
?>