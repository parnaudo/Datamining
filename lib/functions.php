<?php

function alterNodeTable($table){
	$alterQuery="alter table $table add column numPublications int(5) DEFAULT 0";
	echo $alterQuery;
	mysql_query($alterQuery);
	$alterQuery="alter table $table add column numPublicationsFirstAuthor int(5) DEFAULT 0";
	mysql_query($alterQuery);
	$alterQuery="alter table $table add column numCoauthors int(5) DEFAULT 0";
	mysql_query($alterQuery);
	$alterQuery="alter table $table add column reach float DEFAULT 0";
	mysql_query($alterQuery);
	$alterQuery="alter table $table add column SCImagoProminenceScore float DEFAULT 0";
	mysql_query($alterQuery);
}	
function authorPubmedTransform($first,$middle,$last){
	 $middle=substr($middle,0,1);
 	 $first=substr($first,0,1);	
 	 $query = ''.ucfirst(strtolower($last))." ".$first.$middle. "[Author]";
 	 return $query;
}
function certainty($count){
	$selectQuery="SELECT certainty,id FROM edgeCache";
	$result=mysql_query($selectQuery);
	while($row=mysql_fetch_array($result)){
		$certainty=$row['certainty']/$count;
		$update="UPDATE edgeCache SET certainty=".$certainty." WHERE id=".$row['id'];
		mysql_query($update);			
	}
}
function checkDateRange($startDate, $endDate, $dateFromUser){
  echo "$startDate, $endDate, $dateFromUser<BR>";
  $start_ts = strtotime($startDate);
  $end_ts = strtotime($endDate);
  $user_ts = strtotime($dateFromUser);
  return (($user_ts >= $start_ts) && ($user_ts <= $end_ts));
}
function cleanDate($date){
		
		$length = strlen($date); 
		$characters = 4; 
		$start = $length - $characters; 
		$year = substr($date , $start ,$characters); 
		
		return(double) $year;	
	
}	
function cleanTable($table){
	$columnNames = array();
	$getColumns="select column_name from information_schema.columns where table_name='docinstance'";
	$result= mysql_query($getColumns);
	while($row = mysql_fetch_array($result)){
		if($row['column_name']!=="id" && $row['column_name']!=="atomId" && $row['column_name']!=="isotopeId"){
		$columnNames[]=$row['column_name'];
		}
	}
	
	$filter="";
	foreach($columnNames as $key){
		if($key =="type"){
		$filter=$filter;
		}
		else{
		$filter=$filter." AND (".$key."='' OR ".$key." IS NULL)";
		}
	}
	$deleteQuery="DELETE FROM ".$table." WHERE ".trim($filter," AND");
	
	mysql_query($deleteQuery);
}
function clearAuthorTables(){
	$authors="DELETE FROM authors";
	$papers="DELETE FROM papers";
	$instance="DELETE FROM coAuthorInstance";
	mysql_query($authors);
	mysql_query($papers);
	mysql_query($instance);
	
	
}
function clearTable($insertTable){
		mysql_query("DELETE FROM ".$insertTable."");
} 

function createMETable($insertTable){
	
	$createTable="CREATE TABLE `".$insertTable."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menumber` int(11) DEFAULT NULL,
  `universityname` varchar(50) DEFAULT NULL,
  
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4279 DEFAULT CHARSET=latin1;
";
	mysql_query($createTable)or die(mysql_error());
}
function createTable($insertTable){
	
	$createTable="CREATE TABLE ".$insertTable."(Id INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT, atomId INT(11))";
	mysql_query($createTable)or die(mysql_error());
	if($debug) echo "Database doesn't exist!";
	if($debug) echo "Database created";	
	
}

function deduplicateAuthors(){
	$atomQuery="select atomId from authors where atomId!=0 group by atomId having count(id) > 1 order by atomId ASC";
	$result=mysql_query($atomQuery);
	while($row=mysql_fetch_array($result)){
		$authorQuery="SELECT * from authors where atomId=".$row['atomId'];
		$authorResult=mysql_query($authorQuery);
		$lengthTest=0;
		$keepId=0;
		$deleteId=0;
		$testArray=array();
		$nameArray=array();
		while($authorRow=mysql_fetch_array($authorResult)){
			$test=strlen($authorRow['name']);
			$testArray[$authorRow['id']]=$test;
			$nameArray[$authorRow['id']]=$authorRow['name'];
	
		}
		$maxIndex = array_search(max($testArray), $testArray);
		if(hasDuplicates($testArray)==TRUE){
			echo "DUPLICATES";
			print_r($nameArray);
			foreach($testArray as $key=>$value){
				$updateAuthor="UPDATE authors set duplicateFlag=1 where id=".$key;
				echo $updateAuthor;
				mysql_query($updateAuthor);
			}
		}
		else{
			foreach($nameArray as $key=>$value){
				if($key!==$maxIndex){
					$updateInstance="UPDATE coAuthorInstance SET coAuthor=".$maxIndex.", query='".$nameArray[$maxIndex]."' where coAuthor=".$key;
					$deleteAuthor="DELETE FROM authors where id=".$key ;
					mysql_query($updateInstance);
					mysql_query($deleteAuthor);
				}
			}
		}
	
	}
}

function deleteTable($insertTable){
		mysql_query("DROP TABLE ".$insertTable."");
		if($debug)echo "Database deleted!";
} 
function edgeExists($source,$target,$table,$class){
	if(empty($class)){
		$filter='';
	}
	else{
		$filter=" AND class=".$class;
	}
	$query="SELECT * from ".$table." WHERE  source=".$source." AND target=".$target.$filter;
	echo $query;
	$result=mysql_query($query);
	$existFlag=mysql_num_rows($result);
	return $existFlag;
}
function getAtomId($authorId){
	$query="SELECT atomId FROM authors where id=".$authorId;
	$result=mysql_query($query);
	$row=mysql_fetch_array($result);
	return $row['atomId'];
}

function getMostPopulatedMatters($threshold){
	$matterArray=array();
	
	$particlecount="select matters.matterId as matter, count(particleId) as instanceCount, matters.name FROM matters
INNER JOIN particles ON matters.matterId = particles.matterId
GROUP BY matter
HAVING instanceCount > ".$threshold." 
ORDER BY instanceCount Desc";
$result=mysql_query($particlecount) or die(mysql_error());

	while($matterCount=mysql_fetch_array($result)){
	$matterArray[]=$matterCount['matter'];
	}
	
return $matterArray;
}
function getOrgInfo($isotopeId,$atomId,$table){
	$orgArray=array(
		'isotopeId'=>$isotopeId,
		'atomId'=>$atomId,
		'institution'=>'',
		'city'=>'',
		'state'=>'',
		'zipcode'=>''
	);
	$getOrgValues="SELECT name,value from particles p INNER JOIN matters m ON m.matterId=p.matterId WHERE isotopeId=".$isotopeId;
	echo $getOrgValues;
	$orgResult=mysql_query($getOrgValues);
	while($orgRow=mysql_fetch_array($orgResult)){
		if(!empty($orgRow['value'])){
			if(stripos($orgRow['name'],'institution')!==FALSE && stripos($orgRow['name'],'multi')===FALSE && stripos($orgRow['name'],'NPI')===FALSE && stripos($orgRow['name'],'institution2')===FALSE){
				echo $orgRow['name']. ": ".$orgRow['value']."<BR>";
				$orgArray['institution']=$orgRow['value'];
			}
			if($orgRow['name']==='City'){
				$orgArray['city']=$orgRow['value'];		
				echo $orgRow['name']. ": ".$orgRow['value']."<BR>";		
			}
			if($orgRow['name']==='State'){
				$orgArray['state']=$orgRow['value'];			
				echo $orgRow['name']. ": ".$orgRow['value']."<BR>";		
			}
			if(stripos($orgRow['name'],'zipcode')!==FALSE){
				$orgArray['zipcode']=$orgRow['value'];			
				echo $orgRow['name']. ": ".$orgRow['value']."<BR>";		
			}
		}
	}
	insertQuery($orgArray,$table);

}
function getRowCount($query){
	$queryResult=mysql_query($query);
	$count=mysql_num_rows($queryResult);
	return $count;
}
function getTime() 
    { 
    $a = explode (' ',microtime()); 
    return(double) $a[0] + $a[1]; 

	} 


function hasDuplicates($array){
 $dupe_array = array();
 foreach($array as $val){
  if(++$dupe_array[$val] > 1){
   return true;
  }
 }
 return false;
}
function insertQuery($valueArray,$table){
/*
USAGE FOR VALUE ARRAY:
$valueArray=array(
	'source'=>'12345',
	'target'=>'67890',
	'weight'=>'1.456',
	'direction'=>'Undirected',
	'class'=>'1'
);
table attributes are key
*/
$variables=array();
foreach($valueArray as $name=>$value){
		$variables[]=$name;
		$values[]=$value;
	}
	$insertQuery='INSERT INTO '.$table.' ('.implode(",",$variables).') VALUES ("'.implode('","',$values).'")';
	echo $insertQuery."<BR>";
	mysql_query($insertQuery);
}

function percentile($data,$percentile){ 
    if( 0 < $percentile && $percentile < 1 ) { 
        $p = $percentile; 
    }else if( 1 < $percentile && $percentile <= 100 ) { 
        $p = $percentile * .01; 
    }else { 
        return ""; 
    } 
    $count = count($data); 
    $allindex = ($count-1)*$p; 
    $intvalindex = intval($allindex);
    $floatval = $allindex - $intvalindex; 
    sort($data); 
    if(!is_float($floatval)){ 
        $result = $data[$intvalindex]; 
    }else { 
        if($count > $intvalindex+1) 
            $result = $floatval*($data[$intvalindex+1] - $data[$intvalindex]) + $data[$intvalindex]; 
        else 
            $result = $data[$intvalindex]; 
    } 
    return $result; 
} 
function multineedleStripos($haystack, $needles, $offset=0) {
    foreach($needles as $needle) {
        $found[$needle] = stripos($haystack, $needle, $offset);
    }
    return $found;
}
function networkProminence($table){
	$physicians = "select distinct paper,coAuthorPosition,authorCount,a.id,a.atomId,SJR from $table n INNER JOIN authors a on a.atomId=n.Id
	INNER JOIN coAuthorInstance c on c.coAuthor=a.id 
	INNER JOIN papers p on p.articleId=c.paper
	LEFT JOIN journal  j ON (j.ISSN=p.ISSN OR j.Title=p.journal) where n.SCImagoProminenceScore='0' ";
	$result=mysql_query($physicians);
	while($row=mysql_fetch_array($result)){
		$updateQuery='';
		$journalRank=.1;
		switch ($row['coAuthorPosition']) {
	    	case 1:
	   //     echo "coauthor 1";
				$position=10;
	        	break;
	   		 case 2:
	  //      echo "coauthor 2";
				$position=8;
	       		 break;
	    	 case 500:
				$position=6;
	   //     echo "coauthor 500";
	       		 break;
	   		 default:
				$position=1;
	    //  	echo "coauthor X";
		}
		
		//echo $row['paper']."  ".$row['coAuthorPosition']."  ".$row['numAuthors']."  ".$row['SJR']."  ".$position;
		if($row['authorCount']==1){
			$numAuthorModifier=1;
		}
		else{
			$numAuthorModifier= round(($row['authorCount']-1)/($row['authorCount']),2);
		}
		if(empty($row['SJR'])){
			$journalRank=.1;
			}
		else{
			$journalRank=$row['SJR'];
			}
			$score=$position*$journalRank*$numAuthorModifier;
			$updateQuery="UPDATE $table SET SCImagoProminenceScore=(SCImagoProminenceScore+".$score.") WHERE Id=".$row['atomId'];
			mysql_query($updateQuery);
			echo $updateQuery;
	}
}

function processDate($years,$type){
	$change=0;
	$findme='-';
	$length=strlen($years);
	$yearTest=stripos($years,$findme);
	if($yearTest!==FALSE){
	//two dates
		$start=substr($years,0,$yearTest);
		$end=substr($years,$yearTest+1,$length);
		if(stripos($end,'present')!==FALSE){
			$end=date('Y');
		}
		if(strlen($end)==2){
			$prefix=substr($start,0,2);
			$end=$prefix.$end;
		}
		$date=array('start'=>$start,'end'=>$end);
		return $date;	

	}
	else{
		if(stripos($type,'md')!==FALSE){
			$change=3;
		}
		elseif(stripos($type,'fellowship')!==FALSE){
			$change=2;
		}
		elseif(stripos($type,'phd')!==FALSE){
			$change=5;
		}
		elseif(stripos($type,'residency')!==FALSE){
			$change=5;
		}
		else{
			$chage=3;
		}					
		$end=intval($years);
		$start=$end-$change;
		$date=array('start'=>$start,'end'=>$end);
		return $date;
	}


}

function relationshipToEdge($relationshipId){
	$query="select n.id from node n INNER JOIN individual i ON i.id=n.infoId INNER JOIN authors a ON a.atomId=i.id  where a.id=".$relationshipId." and n.class=2;";
	echo $query;
	$result=mysql_query($query);
	$row=mysql_fetch_array($result);
	return $row['id'];
}
function scoringTransform($row, $col){
	switch ($row) {
    case 1:
   //     echo "coauthor 1";
		$row=1;
        break;
    case 2:
  //      echo "coauthor 2";
		$row=2;
        break;
    case 500:
		$row=3;
   //     echo "coauthor 500";
        break;
    default:
		$row=4;
    //   echo "coauthor X";
}
	switch ($col) {
    case 1:
		$col=1;
    //    echo "author 1";
        break;
    case 2:
		$col=2;
     //   echo "author 2";
        break;
    case 500:
		$col=3;	
      //  echo "author 500";
        break;
    default:
		$col=4;
     //  echo "author X";
	}	
	return $coordinates=array($row,$col);
}
function transferAuthorEdges($table){
	$queryNodes = "select authorAtom,coAuthor,relationship from ".$table;
	$result = mysql_query($queryNodes) or die(mysql_error());
	$allNodes=array();
	$test=array();
	while($row=mysql_fetch_array($result)){
		$source=getAtomId($row['authorAtom']);
		$target=getAtomId($row['coAuthor']);
		$weight=$row['relationship'];
		$valueArray=array(
			'source'=>$source,
			'target'=>$target,
			'weight'=>$weight,
			'direction'=>'Directed',
			'class'=>2
		);	
		insertQuery($valueArray,'edge');
	}

}
function updateAuthorPosition(){
//query to get doctor set, can really be from anywhere
//query to get doctor set, can really be from anywhere
	$queryDoctors = "SELECT distinct coAuthorInstance.id, paper, coAuthorPosition, query,name from coAuthorInstance INNER JOIN authors ON authors.id=coAuthorInstance.coAuthor where query!='' OR atomId!=0";
	$result = mysql_query($queryDoctors) or die(mysql_error());
	while($row=mysql_fetch_array($result)){
		$addQuery='';
		if(empty($row['query'])){
			$addQuery=", query='".mysql_escape_string($row['name'])."'";
		
		}
		$updateQuery="UPDATE coAuthorInstance SET authorPosition=".$row['coAuthorPosition'].$addQuery." WHERE paper=".$row['paper']. " AND id=".$row['id'];
		mysql_query($updateQuery);
		}
	
}	



?>
