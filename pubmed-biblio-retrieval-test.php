<?php

/*
This script accepts as an input a list of authors names and queries the pubmed database for all the papers attributed to the author. It then parses all the authors listed on the paper, marks the first, second and last author and records them to a sepaarate table. 

Written by Paul Arnaudo 2/17/12 
*/
include("lib/init.php");	

$Start = getTime(); 
//remove old data
clearAuthorTables();
$authorID=1;

//query to get doctor set, can really be from anywhere
$queryDoctors = "SELECT atomId, firstName,middleName, lastName from tempdoc where lastName!='' AND atomId=375193";
echo $queryDoctors;
$result = mysql_query($queryDoctors) or die(mysql_error());
while($row=mysql_fetch_array($result)){
  $query='';

  $query = $row['firstName']." ".$row['middleName']." ".$row['lastName']; //your query term

//query to get doctor set, can really be from anywhere (specialty like '%cardio%' or specialty like '%CD%' )
$queryDoctors = "SELECT atomId, firstName,middleName, lastName from tempdoc where lastName!='' AND (specialty like '%cardio%' or specialty like '%CD%' ) ";

$result = mysql_query($queryDoctors) or die(mysql_error());
while($row=mysql_fetch_array($result)){
  $query='';
  $middle=substr($row['middleName'],0,1);
  echo $row['middleName']. " MIDDLE IS ".$middle;
 $query = "(".$row['firstName']." ".$row['middleName']." ".$row['lastName']. "[Full Author Name] OR ".$row['firstName']." ".$middle." ".$row['lastName']."[FULL AUTHOR NAME])"; //your query term

  print "<br>Searching for: $query\n";
  $params = array(
    'db' => 'pubmed',
    'retmode' => 'xml',
    'retmax' => 200,
    'usehistory' => 'y',
	'tool' => 'SCUcitationminer',
	'email' => 'parnaudo@scu.edu',

    'term' => $query.  " AND cardio",

    );
  
  $url = 'http://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?' . http_build_query($params);
  

   //Retrieve the pubmed UIDs to then retrieve summaries for
  $xml = simplexml_load_file($url);

//  echo $xml ->Count;
  $translated = (string) $xml->QueryTranslation;
  printf("Translated query: %s\n\n", $translated);
 //use each UID to query the esummary API and return all information on each article
  foreach($xml->IdList->Id as $uid){
	  
	  $attributeName='';

		$sumParams = array(
   		 'db' => 'pubmed',
		'tool' => 'SCUcitationminer',
		'email' => 'parnaudo@scu.edu',
    	'id' => $uid,
   		 );
	  $bibliourl= "http://eutils.ncbi.nlm.nih.gov/entrez/eutils/esummary.fcgi?". http_build_query($sumParams,'','&'); 
  		
	  $bibliourl=str_replace('%5B0%5D','',$bibliourl);
	  $biblioxml = simplexml_load_file($bibliourl);
  	  foreach( $biblioxml->children() as $docsum){
		 //XML that describes the articles 
		foreach($docsum->children() as $item){
			
			$attributeName = $item->attributes();

			if(strpos($attributeName,"FullJournalName")===0){
				$journal = $item[0];	
			}
			if(strpos($attributeName,"Title")===0){
				$title= $item[0];	
			}
				if(strpos($attributeName,"PubDate")===0){
				$pubdate= $item[0];	
			}
			
			if(strpos($attributeName,"AuthorList")===0){
				$lastAuthor=$item->count();
				
				$countAuthors = 1;
				//parse authors and insert them into DB
			 	foreach($item->children() as $author){
					$targetPhysician=$row['atomId'];
					$physicianQuery='';
					$authorIdentifier='';
					if(stripos($author,$row['lastName'])===0){
						$authorIdentifier=$row['atomId'];
						$physicianQuery=$query;
					}	
					if($countAuthors===$lastAuthor){
						$countAuthors='500';
					}
					$testQuery= "SELECT id FROM authors WHERE name LIKE '%".$author."%'";
					
					$resultAuthor=mysql_query($testQuery);
					$rows = mysql_num_rows($resultAuthor);
					if($rows > 0){
						$authorRow=mysql_fetch_array($resultAuthor);
						$author=$authorRow['id'];
						echo $author." already in authors<br>";
					}
					else {
						$insertAuthorQuery="INSERT INTO authors(id,name, atomId) VALUES ('".$authorID."','".$author."','".$authorIdentifier."')";
						$author=$authorID;
						$authorID++;
						mysql_query($insertAuthorQuery);
						
					}
					
						$insertInstanceQuery = "INSERT INTO coAuthorInstance (coAuthor, paper, coAuthorPosition, authorAtomId,query) VALUES ('".$author."','".$uid."','".$countAuthors."','".$targetPhysician."','".$physicianQuery."')";				
						mysql_query($insertInstanceQuery);
						
						$countAuthors++; 
				}
				
			}
			

	
		}
		$insertJournalQuery = "INSERT INTO papers (id, title, journal, numAuthors, pubDate) VALUES ('".$uid."','".$title."','".$journal."','".$lastAuthor."','".$pubdate."')";
		echo $insertJournalQuery."<BR>";
  		mysql_query($insertJournalQuery);
	
	}
  }
}
$End = getTime(); 
echo "Time taken = ".number_format(($End - $Start),2)." secs";
?>