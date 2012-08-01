<?php


include("../lib/init.php");
$Start = getTime(); 

$dataminer=new dataMiner;

/*
This script accepts as an input a list of authors names and queries the pubmed database for all the papers attributed to the author. It then parses all the authors listed on the paper, marks the first, second and last author and records them to a sepaarate table. 

Written by Paul Arnaudo 3/10/12 
*/

$Start = getTime(); 
//remove old data from tables
clearAuthorTables();

$max="SELECT MAX(id) as max FROM authors";
$maxResult=mysql_query($max);
$maxRow=mysql_fetch_array($maxResult);
if($maxRow['max']==NULL){
	$authorID=1;
}
else{
	$authorID=$maxRow['max']+1;
}
$filter="(thrombolytic [mesh terms] OR Fibrinolytic [mesh terms] OR Ischemic Attack [mesh terms] or EMERGENCY MEDICINE[mesh terms] OR STROKE[mesh terms] or Brain ischemia [mesh terms])";
//query to get doctor set, can really be from anywhere, I'm pulling from a temporary doctor table that has first, last and middle 
//$queryDoctors = "select * from neurologist where id IN (1760442420)";
  	$count=0;
  	$query=array();	
	
	if(!empty($filter)){
		$query[]=$filter;
	}
	
	$uids=$dataminer->eSearch($query,0);
	$web=$uids['webEnv'];
	$key=$uids['queryKey'];
	$count=$uids['count'];
	echo "WEB: $web KEY: $key COUNT: $count <BR> ";
	$retmax=500;
/*	for($retstart=0; $retstart < $count; $retstart+=$retmax){
		$efetch_url = $base ."efetch.fcgi?db=pubmed&WebEnv=$web";
        $efetch_url .= "&query_key=$key&retstart=$retstart";
        $efetch_url .= "&retmax=$retmax&rettype=fasta&retmode=text";
        $efetch_out = get($efetch_url);
        print OUT "$efetch_out";
	
	
	}
*/	
//if there are papers, insert an author record	
	foreach($uids['papers'] as $key=>$paperID){
		$paperInfo=$dataminer->eFetch($paperID);
		$paperFlag=0;
		$countPaperQuery=0;
		$pieces=array();
		foreach($paperInfo['abstract']->AbstractText as $abstractText){
			 $pieces[]=$abstractText[0];	
		}

		$abstract=implode(" ",$pieces);
		unset($pieces);
		foreach($paperInfo['pubType']->PublicationType as $typeText){
			$pieces[]=$typeText[0];	
		}
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
		insertQuery($valueArray,'papers');
		$countPaperQuery++;
		$paperInfo=$dataminer->eFetch($paperID);
		$countAuthors=0;
		  foreach($paperInfo['authors'] as $field){
		 
			  foreach($field->children() as $authors){

				  $authorMatch=0;
				  $dupeTest=0;
				  $countAuthors++;
				  $atomId=0;
				  $physicianQuery='';
				  $coAuthor=$authorID;
				  $pubmedName=$authors->LastName." ".$authors->Initials;
				  
				  $lastName=$authors->LastName;
				  $foreName=$authors->ForeName;

				  if(!empty($lastName)){
				  	$coAuthor="$lastName, $foreName"; 
				  
				  }
				  else{
				  	$coAuthor=$authors->CollectiveName;
				  
				  }
				  $coAuthorNames[]=$coAuthor;
				  //In case you use full author name, this check won't work unless you do this
				  //$testQuery= 'SELECT id,atomId FROM authors WHERE name LIKE "%'.$pubmedName.'%"';	  
				  //This is used for Aaron's current project, creates more duplicates but good for being deduped later
				  $testQuery= 'SELECT id,atomId FROM authors WHERE lastName LIKE "'.$lastName.'" AND foreName LIKE "'.$foreName.'"';
				  $resultAuthor=mysql_query($testQuery);
				  $dupeTest = mysql_num_rows($resultAuthor);
				  //checks to see if author has already been inputted
					if($paperInfo['authorCount']==$countAuthors){
						$countAuthors=500;
					}
				  if($dupeTest > 0 ){
						  $authorRow=mysql_fetch_array($resultAuthor);
						  $coAuthor=$authorRow['id'];
				  }
				  else{
				  	if(!empty($lastName)){
				  //if the author doesn't exist, create a new one
				  		$authorArray=array(
				  			'id'=>$authorID,
				  			'name'=>mysql_escape_string($pubmedName),
				  			'atomId'=>$atomId,
				  			'lastName'=>mysql_escape_string($lastName),
				  			'foreName'=>mysql_escape_string($foreName)
				  		);
						insertQuery($authorArray,'authors');
					  	$authorID++;
					  	}
				  }					
					$instanceArray=array(
						'coAuthor'=>$coAuthor,
						'paper'=>$paperID,
						'coAuthorPosition'=>$countAuthors,
					);
					
					insertQuery($instanceArray,'coAuthorInstance')	;
				  	
			  }
			  $lastAuthor=end($coAuthorNames);
			  var_dump($coAuthorNames);
			  $coAuthorString=implode("; ",$coAuthorNames);
				$updatePaperQuery="UPDATE papers SET authors='".mysql_escape_string($coAuthorString)."', lastAuthor='".mysql_escape_string($lastAuthor)."' WHERE articleId=".$paperID;
				mysql_query($updatePaperQuery);
				echo $updatePaperQuery;	  
				unset($coAuthorNames);
		  }
		
		
		//print_r($paperInfo);
		//echo $paperIDs." WITH KEY: ".$key."<BR>";
	
	}


//get all author positions for our target authors and update
//updateAuthorPosition();
//Look for atomIds being used more than once, then flag for manual curation
//deduplicateAuthors();

$End = getTime(); 
echo "Time taken = ".number_format(($End - $Start),2)." secs";
?>