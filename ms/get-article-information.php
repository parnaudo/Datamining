<?php

/*
This script accepts as an input a list of authors names and queries the pubmed database for all the papers attributed to the author. It then parses all the authors listed on the paper, marks the first, second and last author and records them to a sepaarate table. 

Written by Paul Arnaudo 3/10/12 
*/
include("../lib/init.php");	

$Start = getTime(); 
//remove old data from tables
clearAuthorTables();
$authorID=1;




//query to get doctor set, can really be from anywhere, I'm pulling from a temporary doctor table that has first, last and middle 
$queryDoctors = "select * from neurologist where paperCount>10 and paperCountFullAuthor>6 order by paperCount Desc";

$result = mysql_query($queryDoctors) or die(mysql_error());
while($row=mysql_fetch_array($result)){
  $query='';
  $count=0;
  

$query=authorPubmedTransform($row['firstName'],$row['middleName'],$row['lastName']); //your query term, searches for both middle name and middle initial



  print "<br>Searching for: $query\n";
  $params = array(
    'db' => 'pubmed',
    'retmode' => 'xml',
    'retmax' => 200,
    'usehistory' => 'y',
	'tool' => 'SCUcitationminer',
	'email' => 'parnaudo@scu.edu',

    'term' => $query.  " AND MULTIPLE SCLEROSIS [MESH FIELDS]",
//also can add MeSH terms here for more granularity
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
					$authorMatch=0;
					$targetPhysician=$row['id'];
					$physicianQuery='';
					$authorIdentifier='';
					//identifies author from my data set
					if(stripos($author,$row['lastName'])===0){
						$authorIdentifier=$row['id'];
						$authorMatch=1;
						$physicianQuery=$query;
						
					}	
					if($countAuthors===$lastAuthor){
						$countAuthors='500';
					}
					$testQuery= "SELECT id FROM authors WHERE name LIKE '%".$author."%'";
					//echo $testQuery;
					$resultAuthor=mysql_query($testQuery);
					$rows = mysql_num_rows($resultAuthor);
					//checks to see if author has already been inputted
					if($rows > 0 ){
						$authorRow=mysql_fetch_array($resultAuthor);
						$author=$authorRow['id'];
						//echo $author." already in authors<br>";
					}
					else {
						if($authorMatch > 0){
						$insertAuthorQuery="INSERT INTO authors(id,name, atomId) VALUES ('".$authorIdentifier."','".$author."','".$authorIdentifier."')";
						$author=$authorID;						
						echo $insertAuthorQuery;
						}
						else{
						$insertAuthorQuery="INSERT INTO authors(id,name, atomId) VALUES ('".$authorID."','".$author."','".$authorIdentifier."')";
						$author=$authorID;
						}
						$authorID++;
						mysql_query($insertAuthorQuery);
						
					}
					//check to see if paper is already in paper table
					 $paperQuery="SELECT id FROM papers where id=".$uid;
					 $resultPaper=mysql_query($paperQuery);
					 $paperFlag = mysql_num_rows($resultPaper);
	 				if($paperFlag > 0 && $authorMatch > 0){
							$paperUpdateQuery="  ";
							$updateAuthorQuery="UPDATE authors set id='".$authorIdentifier."', atomId='".$authorIdentifier."' WHERE id='".$author."'";
							echo $updateAuthorQuery."<BR>";
							$updateQuery="UPDATE coAuthorInstance SET query='".$query."' WHERE paper=".$uid." AND coAuthor='".$author."'"; 
							mysql_query($updateAuthorQuery);
					  		mysql_query($updateQuery);
					  		$cleanUpQuery="UPDATE coAuthorInstance SET coAuthor='".$authorIdentifier."' WHERE coAuthor='".$author."'";
					  		mysql_query($cleanUpQuery);
	 					 }
					elseif($paperFlag > 0){
							//instance already created, no need 	
						
					}
	 				 else{
						//create new instance for doctor & paper
						$insertInstanceQuery = "INSERT INTO coAuthorInstance (coAuthor, paper, coAuthorPosition, authorAtomId,query) VALUES ('".$author."','".$uid."','".$countAuthors."','".$targetPhysician."','".$physicianQuery."')";				
						mysql_query($insertInstanceQuery);
						
						$countAuthors++; 
					}
				}
				
			}
			

	
		}
		//insert into paper 
		
		$insertJournalQuery = "INSERT INTO papers (id, title, journal, numAuthors, pubDate) VALUES ('".$uid."','".$title."','".$journal."','".$lastAuthor."','".$pubdate."')";
		//echo $insertJournalQuery."<BR>";
  		mysql_query($insertJournalQuery);
	  
	}
  }
}
//function for my purposes of identifying author positions to compare with other authors
updateAuthorPosition();
$End = getTime(); 
echo "Time taken = ".number_format(($End - $Start),2)." secs";
?>