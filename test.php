<?php

/*
This script builds relationship scores based off of authorship instances mined from pubmed.

Written by Paul Arnaudo 3/19/12 
*/
include("lib/init.php");	
  print "<br>Searching for: $query\n";
  $params = array(
    'db' => 'pubmed',
    'retmode' => 'xml',
    'retmax' => 200,
    'usehistory' => 'y',
	'tool' => 'SCUcitationminer',
	'email' => 'parnaudo@scu.edu',

    'term' =>  "Multiple Sclerosis",
//also can add MeSH terms here for more granularity
    );
  
  $url = 'http://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?' . http_build_query($params);
  echo $url;

   //Retrieve the pubmed UIDs to then retrieve summaries for
  $xml = simplexml_load_file($url);

//  echo $xml ->Count;
  $translated = (string) $xml->QueryTranslation;
  printf("Translated query: %s\n\n", $translated);
?>