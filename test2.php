<?php 
include("lib/init.php");
$dataminer=new dataMiner;
$queryDoctors = "select * from topneurologistsnetworkmeasures where atomId IN (1699766238)";
echo $queryDoctors;
//$queryDoctors = "select * from neurologist where paperCount>10 and paperCountFullAuthor>6 order by paperCount Desc";
$result = mysql_query($queryDoctors) or die(mysql_error());
while($row=mysql_fetch_array($result)){
  	$query=array();
	$count=0;
	$filer="[AUTHOR]";
	$author=$row['authorName']; //your query term, searches for both middle name and middle initials	
	echo $author."<BR>";
	$query[]=$author;
	$query[]=$filter;
	$uids=$dataminer->eSearch($query,0);
		foreach($uids['papers'] as $key=>$paperID){
		  $paperInfo=$dataminer->eFetch($paperID);
		  foreach($paperInfo['authors'] as $field){
				$authorPosition=0;
			  foreach($field->children() as $authors){

				  $authorPosition++;
				  $pubmedName=$authors->LastName." ".$authors->Initials;
				  $lastName=$authors->LastName;
				  $foreName=$authors->ForeName;
				  if(stripos($author,$pubmedName)===0){
	
					 if($authorPosition==1){
					 	$updateQuery="UPDATE topneurologistsnetworkmeasures set location='".mysql_escape_string($paperInfo['affiliation'])."' where atomId='".$row['atomId']."'";
						$updateQuery=$updateQuery;
						mysql_query($updateQuery) or die ("Error in query: $query. ".mysql_error());
						exit;	  	
					 }
					}		
		/*		  
			 if($paperInfo['affiliation']!=''){
			 	$updateQuery="UPDATE topneurologistsnetworkmeasures set location='".$paperInfo['affiliation']."' where atomId='".$row['atomId']."'";
			 echo $updateQuery."<BR>";
		 	}*/
			}
		 }
	}
//if there are papers, insert an author record	

}
?>