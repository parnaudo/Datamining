<?php

/*
This script accepts as an input a list of authors names and queries the pubmed database for all the papers attributed to the author. It then parses all the authors listed on the paper, marks the first, second and last author and records them to a sepaarate table. 

Written by Paul Arnaudo 2/17/12 
*/
include("lib/init.php");	
$Start = getTime(); 
//remove old data
clearAuthorTables();
//query to get doctor set, can really be from anywhere
if (($handle = fopen("exampledataTest.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $queryString='';

		$num = count($data);
    	
		echo "<p> $num fields in line $row: <br /></p>\n";
        $row++;
		//since name data is 3 columns, we skip the first one and put the rest in a variable to send to the pubmed query
        for ($c=1; $c < $num; $c++) {
	
           // echo $data[$c] . "<br />\n";
			$queryString=$queryString." ".$data[$c];
			
		}
		echo $queryString; //your query term
  print "<br>Searching for: $query\n";
  $params = array(
    'db' => 'pubmed',
    'retmode' => 'xml',
    'retmax' => 100,
    'usehistory' => 'y',
	'tool' => 'SCUcitationminer',
	'email' => 'parnaudo@scu.edu',
    'term' => $query.  " [Full Author Name]",
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
					$targetPhysician='';
					$physicianQuery='';
					if(stripos($author,$data[4])===0){
						$targetPhysician=$data[0];
						$physicianQuery=$query;
					}
				
					if($countAuthors===1 || $countAuthors===2){	
						
						$insertAuthorQuery = "INSERT INTO authors (author, paper, importantAuthor, atomId,query) VALUES ('".$author."','".$uid."','".$countAuthors."','".$targetPhysician."','".$physicianQuery."')";
					}
					elseif($countAuthors===$lastAuthor){
						$insertAuthorQuery = "INSERT INTO authors (author, paper, importantAuthor,atomId,query) VALUES ('".$author."','".$uid."','3','".$targetPhysician."','".$physicianQuery."')";
					}
					else{
						$insertAuthorQuery = "INSERT INTO authors (author, paper,atomId,query) VALUES ('".$author."','".$uid."','".$targetPhysician."','".$physicianQuery."')";	
					}
					
					mysql_query($insertAuthorQuery);
					$countAuthors++; 
				}
				
			}
			

	
		}
		$insertJournalQuery = "INSERT INTO papers (id, title, journal, numAuthors, pubDate) VALUES ('".$uid."','".$title."','".$journal."','".$lastAuthor."','".$pubdate."')";
	
  		mysql_query($insertJournalQuery);
	
	}
  }
}
}
$End = getTime(); 
echo "Time taken = ".number_format(($End - $Start),2)." secs";
?>