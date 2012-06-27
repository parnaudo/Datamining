<?php
include("lib/init.php");
$Start = getTime(); 

$dataminer=new dataMiner;
$testCase="Henry A Nasrallah-poop";
$query="SELECT atomId,name from nodepruned";
$result=mysql_query($query);
while($row=mysql_fetch_array($result)){	
	$str = $row['name'];
	$count=0;
	$chars = preg_split('/ /', $str, -1, PREG_SPLIT_OFFSET_CAPTURE);

	$arraySize=sizeof($chars);
	foreach($chars as $value){
		if($count==0){
			$updateQuery="UPDATE nodepruned SET firstName='".$value[0]."' where atomId=".$row['atomId'];
		}
		elseif($count==1){
			$updateQuery="UPDATE nodepruned SET middleName='".$value[0]."' where atomId=".$row['atomId'];
		}
		else{
			$updateQuery="UPDATE nodepruned SET lastName='".$value[0]."' where atomId=".$row['atomId'];
		}
	mysql_query($updateQuery);
	echo "I LOVE YOU";
	$count++;
	}
	
}
echo "ALL DONE";
?>