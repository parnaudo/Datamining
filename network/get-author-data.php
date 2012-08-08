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
//clearAuthorTables();

$max="SELECT MAX(id) as max FROM authors";
$maxResult=mysql_query($max);
$maxRow=mysql_fetch_array($maxResult);
if($maxRow['max']==NULL){
	$authorID=1;
}
else{
	$authorID=$maxRow['max']+1;
}
$filter="(Multiple Sclerosis [MESH FIELDS] OR Multiple Sclerosis [Title] OR Multiple Sclerosis [Journal])";
//query to get doctor set, can really be from anywhere, I'm pulling from a temporary doctor table that has first, last and middle 
//$queryDoctors = "select * from largecounts where atomId IN (3814943)";
$queryDoctors = "select * from ocreNew where atomId NOT IN (select distinct atomId from authors)";
$result = mysql_query($queryDoctors) or die(mysql_error());
while($row=mysql_fetch_array($result)){
  	$count=0;
  	$query=array();	
	//$author= $row['firstName']." ".$row['lastName']." [FULL AUTHOR NAME]";
	//see whether to use author or full author name
	if($row['paperCount']==$row['truePaperCount']){

		$author=authorPubmedTransform($row['firstName'],$row['middleName'],$row['lastName']);

	}

	else{

		$author=  $row['firstName']." ".$row['middleName']." ".$row['lastName']." [FULL AUTHOR NAME]";

	};
	$query[]=$author;
	if(!empty($filter)){
		$query[]=$filter;
	}
	$uids=$dataminer->eSearch($query,0);
//if there are papers, insert an author record	
	foreach($uids['papers'] as $key=>$test){
		$paperFlag=0;
		$countPaperQuery=0;
		$paperID=intval($test);
		$paperQuery="SELECT id FROM papers where id=".$paperID;	
		$resultPaper=mysql_query($paperQuery);
		$paperFlag = mysql_num_rows($resultPaper);	
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
				  $coAuthorNames[]="$lastName, $foreName";
				  //In case you use full author name, this check won't work unless you do this
				  $author=authorPubmedTransform($row['firstName'],$row['middleName'],$row['lastName']);
				  $author=str_replace('[Author]',"",$author);
				  echo "$author VS $pubmedName<BR>";
				  
				  //This coauthor is the author that we originally were looking for
				  if(stripos($author,$pubmedName)===0 || stripos($pubmedName, $author)===0){
				 	 $physicianQuery=$author;	
					 $atomId=$row['atomId'];
					 $authorMatch=1;
				  }	
				  $testQuery= 'SELECT id,atomId FROM authors WHERE name LIKE "%'.$pubmedName.'%"';	  
				  echo $testQuery;
				  //This is used for Aaron's current project, creates more duplicates but good for being deduped later
				  //$testQuery= 'SELECT id,atomId FROM authors WHERE lastName LIKE "'.$lastName.'" AND foreName LIKE "'.$foreName.'"';
				  $resultAuthor=mysql_query($testQuery);
				  $dupeTest = mysql_num_rows($resultAuthor);
				  //checks to see if author has already been inputted
					if($paperInfo['authorCount']==$countAuthors){
						$countAuthors=500;
					}
				  if($dupeTest > 0 ){
						  $authorRow=mysql_fetch_array($resultAuthor);
						  $coAuthor=$authorRow['id'];
						  $atomId=$row['atomId'];
						  
						  //echo $author." already in authors<br>";
				  }
				  else{
				  //if the author doesn't exist, create a new one
				  		$authorArray=array(
				  			'id'=>$authorID,
				  			'name'=>$pubmedName,
				  			'atomId'=>$atomId,
				  			'lastName'=>$lastName,
				  			'foreName'=>$foreName
				  		);
						insertQuery($authorArray,'authors');
					  	$authorID++;
				  }
				  //Paper doesn't exist, but the author already does and is part of our set 
				  if($paperFlag < 1 && $authorMatch==1){
					if($countPaperQuery < 1){	
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
					 }
					//make sure this author has an atomId so that he can be correctly identified when we create edges
					
					$updateAuthorQuery="UPDATE authors set  atomId='".$atomId."' WHERE id='".$coAuthor."'";
					 echo $updateAuthorQuery."<BR>";
					mysql_query($updateAuthorQuery)  or die ("Error in query: $query. ".mysql_error());  		
					$instanceArray=array(
						'coAuthor'=>$coAuthor,
						'paper'=>$paperID,
						'coAuthorPosition'=>$countAuthors,
						'authorAtomId'=>$row['atomId'],
						'query'=>mysql_real_escape_string($author)
					);
					
					insertQuery($instanceArray,'coAuthorInstance')	;
				  	
				  }
				  //paper doesn't exist, but author & paper have already been created so just create the author-paper instance
				  elseif($paperFlag < 1){

					$instanceArray=array(
						'coAuthor'=>$coAuthor,
						'paper'=>$paperID,
						'coAuthorPosition'=>$countAuthors,
						'authorAtomId'=>$row['atomId'],
						'query'=>mysql_real_escape_string($physicianQuery)
					);
					
					insertQuery($instanceArray,'coAuthorInstance');										

				  }
				  
				  else{
				  //author and papers already exist, so need to update records
					  if($authorMatch==1){
						$updateAuthorQuery="UPDATE authors set  atomId='".$atomId."' WHERE id='".$coAuthor."'";			echo $updateAuthorQuery."<BR>";
						mysql_query($updateAuthorQuery)  or die ("Error in query: $query. ".mysql_error());  
						$updateQuery="UPDATE coAuthorInstance SET query='".mysql_real_escape_string($author)."' WHERE paper=".$paperID." AND coAuthor='".$coAuthor."'"; 
						mysql_query($updateQuery)  or die ("Error in query: $query. ".mysql_error());  
					  	
					  }
				  }

			  }
			  $lastAuthor=end($coAuthorNames);
			  $coAuthorString=implode("; ",$coAuthorNames);
				$updatePaperQuery="UPDATE papers SET authors='".$coAuthorString."', lastAuthor='".$lastAuthor."' WHERE articleId=".$paperID;
				mysql_query($updatePaperQuery);
				echo $updatePaperQuery;	  
				unset($coAuthorNames);
		  }
		
		
		//print_r($paperInfo);
		//echo $paperIDs." WITH KEY: ".$key."<BR>";
	
	}

}
//get all author positions for our target authors and update
updateAuthorPosition();
//Look for atomIds being used more than once, then flag for manual curation
deduplicateAuthors();
$End = getTime(); 
echo "Time taken = ".number_format(($End - $Start),2)." secs";
?>
