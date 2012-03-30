<?php


	Class dataMiner{
	
//Input is an array of terms for search, including pubmed keywords ex: array("Kurtzke JF [AUTHOR]","MULTIPLE SCLEROSIS [MESH FIELDS]"), $count is 1 if a count is desired, otherwise array of UIDs will be returned
		function eSearch($input,$countFlag){
			$query='';
			$query=implode(" AND ", $input);
			print "<br>Searching for: $query\n";
 		 	$params = array(
    			'db' => 'pubmed',
   				'retmode' => 'xml',
    			'retmax' => 200,
    			'usehistory' => 'y',
				'tool' => 'SCUcitationminer',
				'email' => 'parnaudo@scu.edu',
    			'term' => $query,
//also can add MeSH terms here for more granularity
    		);
 			$url = 'http://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?' . http_build_query($params);
//Retrieve the pubmed UIDs to then retrieve summaries for
  			$xml = simplexml_load_file($url);
			$count= (int) $xml->Count;
//if set, just return count			
			if($countFlag==1){
				return $count;	
			}
//otherwise return set of UIDs that were returned from the query
			else{
				$papers=array();
				foreach($xml->IdList->Id as $uid){
				 	$papers[]=$uid;
				 }
				return $papers;
	
			}
		}
		
//accepts a UID and returns a multi-dimensional array with all the desired paper fields along with all coauthors. More fields can be added from pubmed if required.			
		function eSummary($uid){
			
			$sumParams = array(
   		 		'db' => 'pubmed',
				'tool' => 'SCUcitationminer',
				'email' => 'parnaudo@scu.edu',
    			'id' => $uid,
   		 	);	
   		 	 $url= "http://eutils.ncbi.nlm.nih.gov/entrez/eutils/esummary.fcgi?". http_build_query($sumParams,'','&'); 
  		     
	
			 $xml = simplexml_load_file($url);
  	  		foreach( $xml->children() as $docsum){
  	  		
 //XML that describes the articles 
				foreach($docsum->children() as $item){	
					$attributeName = $item->attributes();
//add more fields from the XML if desired					
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
//get position and name of each author, stick it in array
			 			foreach($item->children() as $author){
							if($countAuthors===$lastAuthor){
								$countAuthors='500';
							}
							
							$authorRecord=array(
								'name'=>$author,
								'position'=>$countAuthors,
							);
							$authors[]=$authorRecord;
							$countAuthors++;
						}
					}
				}
			}
			
			$paperInfo=array(
				'journal'=> $journal,
				'title'=> $title,
				'pubDate'=> $pubdate,
				'lastAuthor'=> $lastAuthor,
				'coAuthors'=> $authors,
			
			);
			return $paperInfo;
		}
		function eFetch(){
		
		
		}
	
		function npi(){
		
		
		}
	
	}
	
	$array=array("Waxman SG [AUTHOR]","MULTIPLE SCLEROSIS [MESH FIELDS]");
	$test=new dataMiner();
	
	$arrayTest=$test->eSummary("4314823");
	foreach($arrayTest as $info){
		
		if(is_array($info)==1){
		  	foreach($info as $authors){
		  		echo $authors['name']. " ".$authors['position']."<BR>";
		  	}
		}
		else{
		echo $info;
		}
	
	}

?>