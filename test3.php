<?php 
include("lib/init.php");
$Start = getTime(); 

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

	if($testRows > 1){	
		while($rowSources=mysql_fetch_array($resultSources)){
			$sources[]=$rowSources['source'];
			$count++;
	
		}
		foreach($sources as $key=>$value){
			echo $key. " KEY IS ".$value."<BR>";
			
		
		}
	}

	
}
echo $count;
$End = getTime(); 
echo "Time taken = ".number_format(($End - $Start),2)." secs";

?>