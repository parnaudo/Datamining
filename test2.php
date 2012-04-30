<?php 
include("lib/init.php");
$Start = getTime(); 
$dataminer=new dataMiner;
$queryDoctors = "select * from topneurologistsnetworkmeasures where location IS NULL";
echo $queryDoctors;
//$queryDoctors = "select * from neurologist where paperCount>10 and paperCountFullAuthor>6 order by paperCount Desc";
$result = mysql_query($queryDoctors) or die(mysql_error());
while($row=mysql_fetch_array($result)){
	$break=0;
  	$author='';
  	$query=array();
	$count=0;
	$filter="[AUTHOR]";
	$author=$row['authorName'].$filter; //your query term, searches for both middle name and middle initials	
	$query[]=$author;
	echo $author."<BR>";
	$uids=$dataminer->eSearch($query,0);
		foreach($uids['papers'] as $key=>$paperID){
		
		echo "TRYING PAPER: ".$paperID."<BR>";
		  
		  $paperInfo=$dataminer->eFetch($paperID);
		  if(strlen($paperInfo['affiliation'])<1){
			echo "NO STRING<BR>";		  

		  }
		  else{
		  foreach($paperInfo['authors'] as $field){
				$authorPosition=0;
			  foreach($field->children() as $authors){
				  
				  $authorPosition++;
				  $pubmedName=$authors->LastName." ".$authors->Initials;
				  $lastName=$authors->LastName;
				  $foreName=$authors->ForeName;
				  echo $pubmedName. " ". $authorPosition;
				  if(stripos($author,$pubmedName)===0){
						echo $authorPosition."<BR>";
					 if($authorPosition==1){
					 	$updateQuery="UPDATE topneurologistsnetworkmeasures set location='".mysql_escape_string($paperInfo['affiliation'])."' where Id='".$row['Id']."'";
						echo $updateQuery;
						mysql_query($updateQuery) or die ("Error in query: $query. ".mysql_error());
						$break=1;
					 }
											 
					}
				  else{
				  	break;
				  
				  }
					if($break==1){
						echo "BREAKING";
						break 3;	
					}	
		/*		  
			 if($paperInfo['affiliation']!=''){
			 	$updateQuery="UPDATE topneurologistsnetworkmeasures set location='".$paperInfo['affiliation']."' where atomId='".$row['atomId']."'";
			 echo $updateQuery."<BR>";
		 	}*/
			}
		 }
	  }
	}
//if there are papers, insert an author record	
$End = getTime(); 
echo "Time taken = ".number_format(($End - $Start),2)." secs";
}
?>