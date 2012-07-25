<?php
include("lib/init.php");
$Start = getTime(); 
//remove old data from tables
$table="CIUpubinstances";
clearTable($table);
$dataminer=new dataMiner;
$authorID=1;
$filter="(URTICARIA [MESH FIELDS] OR URTICARIA [Title] OR URTICARIA [Journal])";
//query to get doctor set, can really be from anywhere, I'm pulling from a temporary doctor table that has first, last and middle 
//$queryDoctors = "select * from neurologist where id IN (1760442420)";
$queryDoctors = "select * from ciupubcount";
$result = mysql_query($queryDoctors) or die(mysql_error());
while($row=mysql_fetch_array($result)){
  	$query=array();
	$count=0;
	$author=authorPubmedTransform($row['firstName'],$row['middleName'],$row['lastName']); //your query term, searches for both middle name and middle initials	
	
//	echo $author."<BR>";
	$query[]=$author;
	if(!empty($filter)){
		$query[]=$filter;
	}
	$uids=$dataminer->eSearch($query,0);
//if there are papers, insert an author record	
	$count=$uids['count'];
	foreach($uids['papers'] as $key=>$value){
		$paperInfo=$dataminer->eFetch($value[0]);
		//first get coauthors
		foreach($paperInfo['authors'] as $field){
			$coAuthorNames=array();
			foreach($field->children() as $authors){
				 $pubmedName=$authors->LastName." ".$authors->Initials;
				 $lastName=$authors->LastName;
				 $foreName=$authors->ForeName;
				 $coAuthorNames[]="$lastName, $foreName";
			}
			$coAuthorString=implode("; ",$coAuthorNames);
		}
		$test=$paperInfo['abstract'];
		//next abstract Info
		$pieces=array();
		foreach($paperInfo['abstract']->AbstractText as $abstractText){
			 $pieces[]=$abstractText[0];	
		}

		$abstract=implode(" ",$pieces);
		unset($pieces);
		foreach($paperInfo['pubType']->PublicationType as $typeText){
			$pieces[]=$typeText[0];	
		}
		$pubTypes=implode("; ",$pieces);
		unset($pieces);
		$url='http://www.ncbi.nlm.nih.gov/pubmed/'.$value[0];
		//Get the rest of the info
		$valueArray=array(
			'atomId'=>$row['atomId'],
			'affiliation'=> $paperInfo['affiliation'],
			'abstract'=> $abstract,
			'ISSN'=>$paperInfo['ISSN'],
			'journal'=>$paperInfo['journal'],
			'journalCountry'=>$paperInfo['journalCountry'],
			'volume'=>$paperInfo['volume'],
			'issue'=>$paperInfo['issue'],
			'pages'=>$paperInfo['pages'],
			'articleId'=>$value[0],
			'pubDate'=>$paperInfo['pubDate'],
			'language'=>$paperInfo['language'],
			'title'=>$paperInfo['title'],
			'authors'=>$coAuthorString,
			'lastAuthor'=>$paperInfo['lastAuthor'],
			'pubType'=>$pubTypes,
			'authorCount'=>$paperInfo['authorCount'],
			'url'=>$url					
		);
		insertQuery($valueArray,$table);		
	}
		
}
echo "ALL DONE";
?>