<?php
include("../lib/init.php");

$Start = getTime(); 
 $test=array(19892860,18211966);
 //$data=new DataMiner;
 $paperInfo=eFetch($test);

/*$sql = array(); 
foreach( $data as $row ) {
    $sql[] = '("'.mysql_real_escape_string($row['text']).'", '.$row['category_id'].')';
}
//mysql_query('INSERT INTO table (text, category) VALUES '.implode(',', $sql));
*/
function eFetch($uid){
				print_r( $uid)."<BR>";
				if(is_array($uid)){
					$uid=implode(',',$uid);
				}
				$sumParams = array(
	   		 		'db' => 'pubmed',
					'tool' => 'SCUcitationminer',
					'email' => 'parnaudo@scu.edu',
					'retmode' => 'xml',
	    			'id' => $uid
	   		 	);
	   		 	
	   		 	 $url= "http://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?". http_build_query($sumParams,'','&'); 
	   		 	 $url=str_replace('%5B0%5D','',$url);
				 echo $url;
				$xml = simplexml_load_file($url);
			   	if($xml==NULL){
               		 echo "BAD DATA";
                	 return;
       			 }
       			 $articles=$xml->xpath('/PubmedArticleSet/PubmedArticle');
       			 //var_dump($articles);
				foreach($articles as $article){
					echo "NEW ARTICLE";
				
				}
				
				
				
				$bookTest= $xml->xpath('/PubmedArticleSet/PubmedBookArticle/BookDocument');
				$bookTitle=$bookTest[0]->Book->BookTitle;
				if($bookTitle!=''){
					echo "BOOKERROR".$bookTitle;
					return;
				}

				else{
				//Get date data here
				$dateResult=$xml->xpath('/PubmedArticleSet/PubmedArticle/PubmedData');
				$day=$dateResult[0]->History->PubMedPubDate->Day;
				if(strlen($day)==1){
					$day=str_pad($day,2,'0',STR_PAD_LEFT);
				}
				$month=$dateResult[0]->History->PubMedPubDate->Month;
				if(strlen($month)==1){
					$month=str_pad($month,2,'0',STR_PAD_LEFT);
				}
				$year=$dateResult[0]->History->PubMedPubDate->Year;
				$date=$month."/".$day."/".$year;
	  	  		$result = $xml->xpath('/PubmedArticleSet/PubmedArticle/MedlineCitation');
					//pull whatever you want from the XML, these two are not available from eSummary
	  	  		 	//$date=$result[0]->Article->Journal->JournalIssue->PubDate->Day." ".$result[0]->Article->Journal->JournalIssue->PubDate->Month." ".$result[0]->Article->Journal->JournalIssue->PubDate->Year;
					
					if( $result[0]->Article->AuthorList->Author !=NULL){
						$authorCount= $result[0]->Article->AuthorList->Author->count();
					
					}
					else {
						$authorCount=0;	
					}
					$authors=$result[0]->Article->AuthorList;
					//$lastAuthor=$authors[$authorCount]->LastName." ".$authors[$authorCount]->Initials;
					$meshTerms=$result[0]->MeshHeadingList;
	  	  			$paperInfo=array(
						'affiliation'=> $result[0]->Article->Affiliation,
						'abstract'=> $result[0]->Article->Abstract,
						'ISSN'=>str_replace('-',"",$result[0]->Article->Journal->ISSN),
						'journal'=>$result[0]->Article->Journal->Title,
						'journalCountry'=>$result[0]->MedlineJournalInfo->Country,
						'volume'=>$result[0]->Article->Journal->JournalIssue->Volume,
						'issue'=>$result[0]->Article->Journal->JournalIssue->Issue,
						'pages'=>$result[0]->Article->Pagination->MedlinePgn,
						'articleID'=>$result[0]->PubMedData->ArticleIdList->ArticleId,
						'pubDate'=>$date,
						'language'=>$result[0]->Article->Language,
						'title'=>$result[0]->Article->ArticleTitle,
						'authors'=>$result[0]->Article->AuthorList,
						'pubType'=>$result[0]->Article->PublicationTypeList,
						'authorCount'=>$authorCount,
						'meshTerms'=>$meshTerms,
									
				);
				
				
				
			}
							
				return $paperInfo;
  	  		
		}	
$End = getTime(); 
echo "Time taken = ".number_format(($End - $Start),2)." seconds";
?>
