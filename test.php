<?php


include("lib/init.php");
$Start = getTime(); 

$dataminer=new dataMiner;

/*
This script accepts as an input a list of authors names and queries the pubmed database for all the papers attributed to the author. It then parses all the authors listed on the paper, marks the first, second and last author and records them to a sepaarate table. 

Written by Paul Arnaudo 3/10/12 
*/

$Start = getTime(); 
//remove old data from tables
clearAuthorTables();
$authorID=1;
$filter="(MULTIPLE SCLEROSIS [MESH FIELDS] OR MULTIPLE SCLEROSIS [Title] OR MULTIPLE SCLEROSIS [Journal])";
//query to get doctor set, can really be from anywhere, I'm pulling from a temporary doctor table that has first, last and middle 
$queryDoctors = "select * from neurologist where id IN (1760442420)";
//$queryDoctors = "select * from neurologist where paperCount>10 and paperCountFullAuthor>6 order by paperCount Desc";
$result = mysql_query($queryDoctors) or die(mysql_error());
while($row=mysql_fetch_array($result)){
  	$query=array();
	$count=0;
	$author=authorPubmedTransform($row['firstName'],$row['middleName'],$row['lastName']); //your query term, searches for both middle name and middle initials	
	echo $author."<BR>";
	$query[]=$author;
	$query[]=$filter;
	$uids=$dataminer->eSearch($query,0);
//if there are papers, insert an author record	

	foreach($uids['papers'] as $key=>$paperID){
		$paperFlag=0;
		$paperQuery="SELECT id FROM papers where id=".$paperID;
		$resultPaper=mysql_query($paperQuery);
		$paperFlag = mysql_num_rows($resultPaper);	

		  $paperInfo=$dataminer->eFetch($paperID);
		  $countAuthors=0;
		  foreach($paperInfo['authors'] as $field){
			 
			  foreach($field->children() as $authors){
				  $authorMatch=0;
				  $countAuthors++;
				  $atomId=0;
				  $physicianQuery='';
				  $coAuthor=$authorID;
				  $pubmedName=$authors->LastName." ".$authors->Initials;
				  $lastName=$authors->LastName;
				  $foreName=$authors->ForeName;
				  if(stripos($author,$pubmedName)===0){
				 	 $physicianQuery=$author;	
					 $atomId=$row['id'];
					 $authorMatch=1;
				  }				  
				  $testQuery= "SELECT id,atomId FROM authors WHERE name LIKE '%".$pubmedName."%'";
				  echo $testQuery;
				  $resultAuthor=mysql_query($testQuery);
				  $rows = mysql_num_rows($resultAuthor);
				  //checks to see if author has already been inputted
					if($paperInfo['authorCount']==$countAuthors){
						$countAuthors=500;
					}
				  if($rows > 0 ){
						  $authorRow=mysql_fetch_array($resultAuthor);
						  $coAuthor=$authorRow['id'];
						  $atomId=$row['id'];
						  //echo $author." already in authors<br>";
				  }
				  else{
					  $insertAuthor="INSERT INTO authors(id,name, atomId,lastName,foreName) VALUES ('".$authorID."','".mysql_escape_string($pubmedName)."','".$atomId."','".mysql_escape_string($lastName)."','".mysql_escape_string($foreName)."')";
					  echo $insertAuthor."<BR>";
					  mysql_query($insertAuthor)  or die ("Error in query: $query. ".mysql_error());
					  $authorID++;
				  }
				  if($paperFlag < 1 && $authorMatch==1){	
					$paperQuery="INSERT INTO papers (id, title, journal, numAuthors, pubDate,ISSN,affiliation) VALUES ('".$paperID."','".mysql_escape_string($paperInfo['title'])."','".mysql_escape_string($paperInfo['journal'])."','".$paperInfo['authorCount']."','".$paperInfo['pubDate']."','".$paperInfo['ISSN']."','".mysql_escape_string($paperInfo['affiliation'])."')";
					echo $paperQuery."<BR>";
					mysql_query($paperQuery)  or die ("Error in query: $query. ".mysql_error());
				  	$insertCoAuthorInstance = "INSERT INTO coAuthorInstance (coAuthor, paper, coAuthorPosition, authorAtomId,query) VALUES ('".$coAuthor."','".$paperID."','".$countAuthors."','".$row['id']."','".$physicianQuery."')";
				  	echo $insertCoAuthorInstance."<BR>";
				  	mysql_query($insertCoAuthorInstance)  or die ("Error in query: $query. ".mysql_error());		
				  	
				  }
				  elseif($paperFlag < 1){

				  	$insertCoAuthorInstance = "INSERT INTO coAuthorInstance (coAuthor, paper, coAuthorPosition, authorAtomId,query) VALUES ('".$coAuthor."','".$paperID."','".$countAuthors."','".$row['id']."','".$physicianQuery."')";
				  	echo $insertCoAuthorInstance;
				  	mysql_query($insertCoAuthorInstance)  or die ("Error in query: $query. ".mysql_error());					
				  }
				  else{
					  if($authorMatch==1){
						$updateAuthorQuery="UPDATE authors set  atomId='".$atomId."' WHERE id='".$coAuthor."'";
						echo $updateAuthorQuery;
						mysql_query($updateAuthorQuery)  or die ("Error in query: $query. ".mysql_error());  
						$updateQuery="UPDATE coAuthorInstance SET query='".$author."' WHERE paper=".$paperID." AND coAuthor='".$coAuthor."'"; 
						mysql_query($updateQuery)  or die ("Error in query: $query. ".mysql_error());  
					  	
					  }
				  }

			  }
		  }
		
		
		//print_r($paperInfo);
		//echo $paperIDs." WITH KEY: ".$key."<BR>";
	
	}

}

updateAuthorPosition();
$End = getTime(); 
echo "Time taken = ".number_format(($End - $Start),2)." secs";
?>