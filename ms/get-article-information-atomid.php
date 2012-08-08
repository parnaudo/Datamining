<?php 
include("../lib/init.php");	
$dataCounts=array();
$dataBetweenness=array();
$dataCloseness=array();
//$query="select percentile, count,a.id,n.firstName,n.middleName,n.lastName, OfficialFullName from trial INNER JOIN neurologist as n ON (trial.firstName=n.FirstName AND n.lastName=trial.LastName ) INNER JOIN authors as a on a.atomId=n.id order by count desc";
$query="select firstName, middleName,lastName, authors.id from neurologist INNER JOIN authors on authors.atomId=neurologist.id";
$result = mysql_query($query);

while($row=mysql_fetch_array($result)){
		  	//	$updateQuery="UPDATE topneurologistsnetworkmeasures SET firstName='".$row['firstName']."',middleName='".$row['middleName']."',lastName='".$row['lastName']."', ClinicalTrialsCount='".$row['count']."', ClinicalTrialspercentile='".$row['percentile']."' WHERE id=".$row['id'];
		  		$updateQuery="UPDATE topneurologistsnetworkmeasures SET firstName='".$row['firstName']."',middleName='".$row['middleName']."',lastName='".$row['lastName']."' WHERE id=".$row['id'];
		  		echo $updateQuery."<BR>";
				mysql_query($updateQuery);

		
}

?> 