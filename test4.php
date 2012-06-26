<?php


include("lib/init.php");
$Start = getTime(); 

//clearTable('edgeCache');
$table="schizo";
$dataminer=new dataMiner;
$query="SELECT zipcode,atomId from ".$table;
$result = mysql_query($query) or die(mysql_error());
while($row=mysql_fetch_array($result)){
	$name=substr($row['zipcode'],0,5);
	if(strlen($name) < 5){
		$name=str_pad($name,5,0,STR_PAD_LEFT); 
	}
	$selectCoords="SELECT latitude,longitude from zipcodes where zipCodeValue=".$name." LIMIT 1";
	$resultCoords = mysql_query($selectCoords) or die(mysql_error());
	if(mysql_num_rows($resultCoords) < 1){
		echo "NO MATCH<BR>";
	}
	else{
		while($rowCoords=mysql_fetch_array($resultCoords)){
			//echo $rowCoords['latitude']." ".$rowCoords['longitude']. "<BR>";
			$updateQuery="UPDATE ".$table." SET longitude='".$rowCoords['longitude']."', latitude='".$rowCoords['latitude']."' where atomId=".$row['atomId'];
			echo $updateQuery."<BR>";
			mysql_query($updateQuery);
		}
	}
	
//	mysql_query($updateQuery);
}
?>