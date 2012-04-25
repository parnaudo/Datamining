<?php


include("lib/init.php");
//updatePercentiles("topneurologistsnetworkmeasures","paperCount","PaperCountPercentile");
//updatePercentiles("topneurologistsnetworkmeasures","ClosenessCentrality","ClosenessPercentile");
//updatePercentiles("topneurologistsnetworkmeasures","BetweennessCentrality","BetweennessPercentile");
//updatePercentiles("topneurologistsnetworkmeasures","SCImagoProminenceScore","SCImagoProminenceScorePercentile");
//updatePercentiles("topneurologistsnetworkmeasures","ClinicalTrialsCount","ClinicalTrialsPercentile");	
$dataminer=new dataMiner;

/*
This script accepts as an input a list of authors names and queries the pubmed database for all the papers attributed to the author. It then parses all the authors listed on the paper, marks the first, second and last author and records them to a sepaarate table. 

Written by Paul Arnaudo 3/10/12 
*/

$Start = getTime(); 
//remove old data from tables
clearAuthorTables();
$authorID=1;



$filter="MULTIPLE SCLEROSIS [MESH FIELDS]";
//query to get doctor set, can really be from anywhere, I'm pulling from a temporary doctor table that has first, last and middle 
$queryDoctors = "select * from neurologist where id=1467430686";
//$queryDoctors = "select * from neurologist where id=1003013616";
$result = mysql_query($queryDoctors) or die(mysql_error());
while($row=mysql_fetch_array($result)){
  	$query=array();
	$count=0;
	$author=authorPubmedTransform($row['firstName'],$row['middleName'],$row['lastName']); //your query term, searches for both middle name and middle initials	
	$query[]=$author;
	$query[]=$filter;
	$uids=$dataminer->eSearch($query,0);
//if there are papers, insert an author record	
	if($uids['count']!==0){
		$author=str_ireplace("[AUTHOR]","",$author);
		$insertAuthor="INSERT INTO authors(id,name, atomId,lastName,foreName) VALUES ('".$authorID."','".$author."','".$row['id']."','".$row['lastName']."','".$row['firstName']." ".$row['middleName']."')";
		mysql_query($insertAuthor);
		$authorID++;
	}
	foreach($uids['papers'] as $key=>$paperID){
		$paperQuery="SELECT id FROM papers where id=".$uid;
		$resultPaper=mysql_query($paperQuery);
		$paperFlag = mysql_num_rows($resultPaper);	
			
		$paperInfo=$dataminer->eFetch($paperID);
		$paperQuery="INSERT INTO papers (id, title, journal, numAuthors, pubDate,ISSN,affiliation) VALUES ('".$paperID."','".$paperInfo['title']."','".$paperInfo['journal']."','".$paperInfo['authorCount']."','".$paperInfo['pubDate']."','".$paperInfo['ISSN']."','".$paperInfo['affiliation']."')";
		
		mysql_query($paperQuery);
		foreach($paperInfo['authors'] as $field){
			foreach($field->children() as $authors){
				$pubmedName=$authors->LastName." ".$authors->Initials;
				$lastName=$authors->LastName;
				$foreName=$authors->ForeName;
				$testQuery= "SELECT id FROM authors WHERE name LIKE '%".$pubmedName."%'";
				$resultAuthor=mysql_query($testQuery);
				$rows = mysql_num_rows($resultAuthor);
				//checks to see if author has already been inputted
				if($rows > 0 ){
						$authorRow=mysql_fetch_array($resultAuthor);
						$author=$authorRow['id'];
						//echo $author." already in authors<br>";
				}
				else{
					
					$insertAuthor="INSERT INTO authors(id,name, atomId,lastName,foreName) VALUES ('".$authorID."','".$pubmedName."','0','".$lastName."','".$foreName."')";
					mysql_query($insertAuthor);
					$authorID++;
				
				}
				//LEFT OFF HERE!!!!!!!!!!!!!!!!!!!!!!!!!!!!
				$insertCoAuthorInstance = "INSERT INTO coAuthorInstance (coAuthor, paper, coAuthorPosition, authorAtomId,query) VALUES ('".$author."','".$uid."','".$countAuthors."','".$targetPhysician."','".$physicianQuery."')";
				echo $insertCoAuthorInstance;

			}
		}
		//print_r($paperInfo);
		//echo $paperIDs." WITH KEY: ".$key."<BR>";
	
	}

}
?>