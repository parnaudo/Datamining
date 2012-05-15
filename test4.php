<?php 
include("lib/init.php");
$Start = getTime(); 
$dataminer=new dataMiner;
$queryDoctors = "select id, phone from `update`";
//$queryDoctors = "select * from neurologist where paperCount>10 and paperCountFullAuthor>6 order by paperCount Desc";
$result = mysql_query($queryDoctors) or die(mysql_error());
while($row=mysql_fetch_array($result)){
	$updateQuery="UPDATE neurologist set phone='".$row['phone']."' where id=".$row['id'];
	echo $updateQuery;
	mysql_query($updateQuery);
}
	
?>