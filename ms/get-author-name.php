<?php 
include("../lib/init.php");
$Start = getTime(); 
$dataminer=new dataMiner;
$queryDoctors = "select id,authorName,firstName from topneurologistsnetworkmeasures ";

//$queryDoctors = "select * from neurologist where paperCount>10 and paperCountFullAuthor>6 order by paperCount Desc";
$result = mysql_query($queryDoctors) or die(mysql_error());
while($row=mysql_fetch_array($result)){
	$firstName='';
	$middleName='';
	$lastName='';
		$authorPos=stripos($row['authorName']," ");
		$firstNamePos=stripos($row['firstName']," ");
		if($firstNamePos===false){
			$firstName=$row['firstName'];
			$middleName='';	
		}
		else{
		  $middleName=substr($row['firstName'],$firstNamePos);
		  $firstName=substr($row['firstName'],0,$firstNamePos);
		}
		  $lastName=substr($row['authorName'],0,$authorPos);
		$updateQuery="UPDATE topneurologistsnetworkmeasures set firstName='".mysql_escape_string($firstName)."',lastName='".mysql_escape_string($lastName)."',middleName='".mysql_escape_string($middleName)."' where Id='".$row['id']."'";
		echo $updateQuery;
		mysql_query($updateQuery) or die ("Error in query: $query. ".mysql_error());
	}
//if there are papers, insert an author record	
$End = getTime(); 
echo "Time taken = ".number_format(($End - $Start),2)." secs";

?>