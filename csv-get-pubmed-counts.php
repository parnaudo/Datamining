<?php
/*
This script accepts a csv file with authors names and then queries the pubmed esearch utility to get a count for how many publications they have written in a subject. Good for a quick reference of the most academically active in a set of doctors.

*/
include("lib/init.php");	
$Start = getTime(); 
$row = 1;
//Open input CSV format should be first column ID, then First name, Middle name and Last name in order
if (($handle = fopen("PrePubMedRheumatologists-3_May_2012.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $queryString='';

			
		$num = count($data);
    	
		echo "<p> $num fields in line $row: <br /></p>\n";
        $row++;
		//since name data is in columns 2-4, we skip the first one and put the rest in a variable to send to the pubmed query
        for ($c=1; $c < $num; $c++) {
			switch($c){
			case 1:
				$first=$data[$c];
			break;
			case 2:
				$middle=$data[$c];
			break;
			case 3:
				$last=$data[$c];
			break;
			}
           // echo $data[$c] . "<br />\n";
			//$queryString=authorPubmedTransform($first,$middle,$last);
			$queryString=$first. " ". $middle . " ".$last;
		}
		//NPI identifier is separate, if statement was hanging things up so went with easier method
		$queryNPI='';
		$queryNPI=$data[0];
		//$query = $queryString.'[Author] AND (endocrinology OR diabetes)[MESH FIELDS]';
		$query = $queryString.'[Full Author Name] AND Rheumatoid Arthritis [MESH FIELDS] '; // for example AND (endocrinology OR diabetes) AND ("2001"[Date - Publication] : "3000"[Date - Publication])
		echo "trying: ". $query;
		//Lets see if there are any hits
		pubmed_fetch($query, $queryNPI);
		
    }
    fclose($handle);
$End = getTime(); 
echo "Time taken = ".number_format(($End - $Start),2)." secs";
}

function pubmed_fetch($query, $queryNPI){
	
  print "Searching for: $query\n";
  $params = array(
    'db' => 'pubmed',
    'retmode' => 'xml',
    'retmax' => 1,
    'usehistory' => 'y',
	'tool' => 'SCUcitationminer',
	'email' => 'parnaudo@scu.edu',
    'term' => $query,
    );
  
  $url = 'http://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?' . http_build_query($params);
  

   //retrieve XML
  $xml = simplexml_load_file($url);
  
  pubmed_errors($xml);
  $xml ->Count;
  if (!$count = (int) $xml->Count)
  echo "None Found<br>";
   // exit();
  print "$count items found\n";
  $translated = (string) $xml->QueryTranslation;
  printf("Translated query: %s\n\n", $translated);
  $params = array(
    'db' => 'pubmed',
    'retmode' => 'xml',
    'query_key' => (string) $xml->QueryKey,
    'WebEnv' => (string) $xml->WebEnv,
    'retmax' => $count,
    );
    
  $url = 'http://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?' . http_build_query($params);
  echo $url;
  $file = sprintf('%s-%s.xml', preg_replace('/\W/', '_', $translated), date('YmdHis'));
  system(sprintf("wget --output-document=%s %s", escapeshellarg($file), escapeshellarg($url)));

//Write NPI, query term and count to output CSV
  $fp = fopen('PrePubMedRheumatologists-3_May_2012-results.csv', 'a+');
	$list=array(
		array($queryNPI,$query,$count)
		);
	foreach ($list as $fields) {
    fputcsv($fp, $fields);
}

fclose($fp);
}
function pubmed_errors($xml){
  print "\033[31m" ; // red
  
  if ($xml->ErrorList){
    if ($xml->ErrorList->PhraseNotFound)
      printf("Phrase not found: %s\n", (string) $xml->ErrorList->PhraseNotFound);
    if ($xml->ErrorList->FieldNotFound)
      printf("Field not found: %s\n", (string) $xml->ErrorList->FieldNotFound);
  }
  
  if ($xml->WarningList){
    print (string) $xml->WarningList->OutputMessage . "\n"; 
    if ($xml->WarningList->QuotedPhraseNotFound)
      printf("Quoted phrase not found: %s\n", (string) $xml->WarningList->QuotedPhraseNotFound); 
    if ($xml->WarningList->PhraseIgnored)
      printf("Phrase ignored: %s\n", (string) $xml->WarningList->PhraseIgnored);
  }
  
  print "\033[00m"; // default
}

?>