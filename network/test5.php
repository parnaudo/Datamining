<?php
header('Content-Type: text/html; charset=utf-8');
include("../lib/init.php");
mysql_query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
$alterQuery="alter table papers add column testPubDate varchar (255) DEFAULT 0";
//mysql_query($alterQuery);
$getPapers="SELECT * FROM papersRA where pubDate!=testPubDate  ";
$result=mysql_query($getPapers);
while($row=mysql_fetch_array($result)){
		$date='';
        $data=new dataMiner;
        $coAuthorString='';
        $paperInfo=$data->eFetch($row['Id']);
        $count=0;
 		$authorSize=sizeof($paperInfo['authors']);
	/*	foreach($paperInfo['authors'] as $field){
				foreach($field->children() as $authors){
					$collectiveFlag=0;
					if($authors->CollectiveName==FALSE){
						 $lastName=$authors->LastName;
						 $foreName=$authors->ForeName;
						 $coAuthorNames[]="$lastName, $foreName";
					 }
					 else{
						 $lastName=$authors->CollectiveName;
					 	 $coAuthorNames[]=$lastName;
					 	$collectiveFlag=1;
					 }
					if($count==$authorSize){
						if($collectiveFlag==0){
							$lastAuthor="$lastName, $foreName";
						}
						else{
							$lastAuthor=$lastName;
						}
					}
					else{
						$count++;
					}
				}
				
			
		
		}
		foreach($paperInfo['abstract']->AbstractText as $abstractText){
							 $pieces[]=$abstractText[0];	
		}
		$abstract=implode(" ",$pieces);
		unset($pieces);
		
		$coAuthorString=implode("; ",$coAuthorNames);
		*/
//		echo "<BR>PUBDATE IS :". $paperInfo['pubDate'];	
		
		if(empty($row['pubDate'])==TRUE){

			$date=$paperInfo['pubDate'];
		}
		else{
			$date=date('m/d/Y',strtotime($row['pubDate']));
		}
		$updatePaper='UPDATE YOUR MOM papersRA set pubDate="'.$date.'" WHERE FUCKYOU='.$row['Id'];
			
		echo $updatePaper;
		//mysql_query($updatePapers);
		unset($coAuthorNames);
/*
		foreach($paperInfo['abstract']->AbstractText as $abstractText){
			 $pieces[]=$abstractText[0];	
		}

		$abstract=implode(" ",$pieces);
		unset($pieces);
		foreach($paperInfo['pubType']->PublicationType as $typeText){
			$pieces[]=$typeText[0];	
		}
                                unset($pieces);
		foreach($paperInfo['meshTerms']->MeshHeading as $meshText){
				$pieces[]=$meshText[0]->DescriptorName;	
		}
			$meshTerms=implode('; ',$pieces);
		$pubTypes=implode("; ",$pieces);
		unset($pieces);
		$url='http://www.ncbi.nlm.nih.gov/pubmed/'.$paperID;
		$valueArray=array(
			'affiliation'=> $paperInfo['affiliation'],
			'abstract'=> $abstract,
			'ISSN'=>$paperInfo['ISSN'],
			'journal'=>$paperInfo['journal'],
			'journalCountry'=>$paperInfo['journalCountry'],
			'volume'=>$paperInfo['volume'],
			'issue'=>$paperInfo['issue'],
			'pages'=>$paperInfo['pages'],
			'articleId'=>$paperID,
			'pubDate'=>$paperInfo['pubDate'],
			'language'=>$paperInfo['language'],
			'title'=>$paperInfo['title'],
			'pubType'=>$pubTypes,
			'authorCount'=>$paperInfo['authorCount'],
			'url'=>$url,
			'meshTerms'=>$meshTerms					
		);
                                var_dump($valueArray);
		//insertQuery($valueArray,'papers');
    	*/    
}

?>
