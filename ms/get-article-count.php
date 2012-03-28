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

$queryDoctors = "SELECT * FROM `neurologist`";


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

    'term' => $query.  " AND (MULTIPLE SCLEROSIS [MESH FIELDS] OR MULTIPLE SCLEROSIS [Journal] OR MULTIPLE SCLEROSIS [Title])",
//also can add MeSH terms here for more granularity
    );
  
  $url = 'http://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?' . http_build_query($params);
 
   //Retrieve the pubmed UIDs to then retrieve summaries for
  $xml = simplexml_load_file($url);
  $count= (int) $xml->Count;
  $updateQuery="UPDATE neurologist SET paperCount=".$count." WHERE id=".$row['id'];
  mysql_query($updateQuery);

}
$End = getTime(); 
echo "Time taken = ".number_format(($End - $Start),2)." secs";
?>