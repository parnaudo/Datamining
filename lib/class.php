<?php

	class publishingInfo{
		function __construct($atomId){
					$this->atomId=$atomId;
		}
		function getAuthorCount(){
				$coAuthorCount=0;
				$sql="select paper from coAuthorInstance c INNER JOIN authors a on coAuthor=a.id where atomId=".$this->atomId;
				$sqlResult=mysql_query($sql);
				while($sqlRow=mysql_fetch_array($sqlResult)){
	
					$coAuthorSelect= "SELECT count(id) as count from coAuthorInstance where paper=".$sqlRow['paper'];
					$coAuthorResult=mysql_query($coAuthorSelect);
					while($coAuthorRow=mysql_fetch_array($coAuthorResult)){
						$coAuthorCount=$coAuthorCount+($coAuthorRow['count']-1);
					}
				}	
				//$updateQuery="UPDATE node SET numCoauthors='".$coAuthorCount."' WHERE atomId=".$atomId;
				//echo $updateQuery."<BR>";
				return $coAuthorCount;
			
		
		}		
		function getPubCount($type=null){
				if($type!==null){
				//first position of author
					$filter=" AND authorPosition=1";
				}
				else{
				//just get all pubs written
					$filter="";
				}
				$sql="select count(atomId) as count from coAuthorInstance c INNER JOIN authors a on coAuthor=a.id where atomId=".$this->atomId.$filter;
				$sqlResult=mysql_query($sql);
				$sqlRow=mysql_fetch_array($sqlResult);
				$count=$sqlRow['count'];
				return $count;
			
		}
		function getAuthorInstances(){
			$paperArray=array();
			$sql="SELECT paper from coAuthorInstance c INNER JOIN authors a on c.coAuthor=a.id where a.atomId=".$this->atomId;
			$sqlResult=mysql_query($sql);
			while($sqlRow=mysql_fetch_array($sqlResult)){	
				//echo $sqlRow['paper'];
				$paperArray[]=$sqlRow['paper'];
			}			
			return $paperArray;
		}
		function getYear($paperId){
			$sql="SELECT pubDate from papers where id=".$paperId." AND pubDate!=''" ;
			$sqlResult=mysql_query($sql);
			$sqlRow=mysql_fetch_array($sqlResult);
			$date=$sqlRow['pubDate'];
			$test=date('Y', strtotime($date));
			echo "TEST $test <BR>";
			if($test!==FALSE){
				$year=$test;
			}
			else{
				echo "DATE $date from paper $paperId HAS NO 19 or 20 <BR>";
				$year=FALSE;
				//echo "NO YEAR FOUND";
			}
			return intval($year);
		
		}
	}
	Class networkAnalysis{

	    function __construct($x,$y,$z){
	    	$this->node=$x;
	    	$this->table=$y;
	    	$this->threshold=$z; 
	    	$this->currentTarget=0;
	    }
	    
		function reach(){
			$nodeArray=array();
			$firstTargets=$this->getTargets();
			var_dump($firstTargets);
			$reach=0;
			$origin=$this->node;
			//Get first degree weights and add them
			for($i=0;$i < sizeof($firstTargets['weight']);$i++){
				$test=in_array($firstTargets['id'][$i],$nodeArray);
				if($test===FALSE){
					$nodeArray[]=$firstTargets['id'][$i];
				}
				$reach=$reach+($firstTargets['weight'][$i]*.5);
			}
			
			//Get second degree weights that don't equal the origin and add them	
				for($i=0; $i < sizeof($firstTargets['id']);$i++){				
					$this->node=$firstTargets['id'][$i];
					$secondTargets=$this->getTargets();
					for($j=0;$j < sizeof($secondTargets['weight']);$j++){
						if(in_array($secondTargets['id'][$j],$nodeArray)===FALSE){
							$nodeArray[]=$secondTargets['id'][$j];
						}
						if($secondTargets['id'][$j]==$origin){
							//don't count scores that head back to origin
						}
						else{
							$reach=$reach+($secondTargets['weight'][$j]*.25);	
						}
					}
				}

				
			
			echo "REACH: $reach";
			//print_r($nodeArray);
			return $reach;	
							
				
		}
		function getTargets(){
			$targets=array();
			$weights=array();
			$returnArray=array();
			$select="SELECT target,weight from ".$this->table." where source=".$this->node." AND weight > ".$this->threshold;
			echo $select."<BR>";
			$result=mysql_query($select);
			while($row=mysql_fetch_array($result)){
				$targets[]=$row['target'];
				$weights[]=$row['weight'];
			}
			$returnArray['id']=$targets;
			$returnArray['weight']=$weights;
			return $returnArray;	
		}
	}
	Class textManipulate{
//basically this is strpos for more than one occurence	
		function findOccurences($string, $find) {
			if (strpos(strtolower($string), strtolower($find)) !== FALSE) {
				$pos = -1;
				for ($i=0; $i<substr_count(strtolower($string), strtolower($find)); $i++) {
					$pos = strpos(strtolower($string), strtolower($find), $pos+1);
					$positionarray[] = $pos;
				}
				
				return $positionarray;
			}
			else {
				return FALSE;
			}
	
		}			
		
		function separateOccurences($occurenceArray,$string){
//after finding the amounts of delimiters, separate values and return an array of the separated values
			$start=0;
			$piecesArray=array();
			$length=strlen($string);
			if(is_array($occurenceArray)){
				foreach($occurenceArray as $key=>$value){
					$end=$value-$start;
					$piece=substr($string,$start,$end);
					$piece=trim(str_replace(';','',$piece));
					$pieceArray[]=$piece;
					$start=$value;
					
				}
				$lastpiece=trim(str_replace(';','',substr($string,$value,$length)));
				$pieceArray[]=$lastpiece;

			}
			else{
				$pieceArray=array($string);
			
			}
			return $pieceArray;
		}
		function parseRecords($array){
//separate dates from the string value for comparison
			$dateArray=array();
			$stringArray=array();
			$returnArray=array(
				'date'=>'',
				'name'=>'',
				'type'=>'',			
			);
			foreach($array as $key=>$value){
				$end=strlen($value);
				$cutoff=strpos($value,':');	
				$date=substr($value,0,$cutoff);
				$string=str_replace(':','',substr($value,$cutoff,$end));
				//echo $date." VALUE ".$string. "<BR>";
				$dateArray[]=$date;
				$stringArray[]=$string;		
			}

			$type='';
			$name='';
			$nameArray=array();
			$typeArray=array();
			$degrees=array('ba','bs','md','phd','bd','ms','ma','scb','sc','mph','ab','aa');
			foreach($stringArray as $value){
				$value=strtolower($value);
				//echo $value."<BR>";
				$cutoff=strpos($value,',');	
				$length=strlen($value);
				$type=trim(substr($value,0,$cutoff));
				
				$name=trim(str_replace(',','',substr($value,$cutoff,$length)));
//search for different types of medical degrees 				
				foreach($degrees as $value){
					if(strpos($type,$value)!==FALSE){
						$degreeFlag=1;
						$nameArray[]=mysql_escape_string($name);
						$typeArray[]=$type;
						echo "HAS DEGREE: ".$name."<BR>";
						echo "TYPE: ".$type."<BR>";	
						break;
					}
				}

				if(sizeof($nameArray)==0){
					if(stripos($value,'undergrad')!==FALSE){
						$typeArray[]='bs';
					}
					else{
						$nameArray[]=mysql_escape_string(strtolower($name));
						$typeArray[]='unknown';					
					}
				}		
			}
			$returnArray['date']=$dateArray;
			$returnArray['name']=$nameArray;
			$returnArray['type']=$typeArray;	
			var_dump($returnArray);		
			return $returnArray;
		}
		
		function separateDegrees($array){
//takes array of string values that still have
			$type='';
			$name='';
			$nameArray=array();
			$typeArray=array();
			$degrees=array('ba','bs','md','phd','bd','ms','ma','scb','sc','mph');
			foreach($array as $value){
				$value=strtolower($value);
				//echo $value."<BR>";
				$cutoff=strpos($value,',');	
				$length=strlen($value);
				$type=trim(substr($value,0,$cutoff));
				$name=trim(str_replace(',','',substr($value,$cutoff,$length)));
				//echo $test;	
				foreach($degrees as $value){
					if(strpos($type,$value)!==FALSE){
						$degreeFlag=1;
						$nameArray[]=$name;
						$typeArray[]=$type;
						echo "HAS DEGREE: ".$name."<BR>";
						echo "TYPE: ".$type."<BR>";	
						break;
					}

				}		
			}
			$returnArray=array(
			'name'=>$nameArray,
			'type'=>$typeArray
			);
			return $returnArray;
		
		}
	}
	Class dataMiner{
	
	//Input is an array of terms for search, including pubmed keywords ex: array("Kurtzke JF [AUTHOR]","MULTIPLE SCLEROSIS [MESH FIELDS]"), $count is 1 if a count is desired, otherwise array of UIDs will be returned
			function eSearch($input,$countFlag){
				$query='';
				$query=implode(" AND ", $input);
				print "<br>Searching for: $query\n";
	 		 	$params = array(
	    			'db' => 'pubmed',
	   				'retmode' => 'xml',
	    			'retmax' => 500,
	    			'usehistory' => 'y',
					'tool' => 'SCUcitationminer',
					'email' => 'parnaudo@scu.edu',
	    			'term' => $query,
	//also can add MeSH terms here for more granularity
	    		);
	 			$url = 'http://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?' . http_build_query($params);
				echo $url;
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
					 $paperInfo = array(
					 			'papers'=>$papers,
					 			'count'=>$count,
					 			);
					return $paperInfo;		
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
						if(strpos($attributeName,"ESSN")===0){
							$ISSN = str_replace('-',"",$item[0]);	
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
					'ISSN'=>$ISSN,
				
				);
				return $paperInfo;
			}
			function eFetch($uid){
				$sumParams = array(
	   		 		'db' => 'pubmed',
					'tool' => 'SCUcitationminer',
					'email' => 'parnaudo@scu.edu',
					'retmode' => 'xml',
	    			'id' => $uid,
	   		 	);
	   		 	
	   		 	 $url= "http://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?". http_build_query($sumParams,'','&'); 
	   		 	 $url=str_replace('%5B0%5D','',$url);
				 echo $url;
				$xml = simplexml_load_file($url);
				$bookTest= $xml->xpath('/PubmedArticleSet/PubmedBookArticle/BookDocument');
				$bookTitle=$bookTest[0]->Book->BookTitle;
				if($bookTitle!=''){
					echo $bookTitle;
					return;
				}
				else{
	  	  		$result = $xml->xpath('/PubmedArticleSet/PubmedArticle/MedlineCitation');
					//pull whatever you want from the XML, these two are not available from eSummary
	  	  		 	$date=$result[0]->Article->Journal->JournalIssue->PubDate->Day." ".$result[0]->Article->Journal->JournalIssue->PubDate->Month." ".$result[0]->Article->Journal->JournalIssue->PubDate->Year;
					if( $result[0]->Article->AuthorList->Author !=NULL){
						$authorCount= $result[0]->Article->AuthorList->Author->count();
					}
					else {
						$authorCount=0;	
					}
					$authors=$result[0]->Article->AuthorList->Author;
					$lastAuthor=$authors[$authorCount]->LastName." ".$authors[$authorCount]->Initials;
					
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
						'lastAuthor'=>$lastAuthor,
						'pubType'=>$result[0]->Article->PublicationTypeList,
						'authorCount'=>$authorCount,
								
				);
			}
							
				return $paperInfo;
  	  		
		}	
	
		/*	function npi(){
				$specialty='';
						
				$specialtyArray=array();
	 			$query = "Neurology"; //your query terms
 	 			print "Searching for: $query\n";
  				$params = array(
							'first_name' => '',
    						'last_name' => '',
    						'zip' => '',
							'org_name' => '',
    						'state' => '',
							'city_name' => '',
							'taxonomy' => 'code_179',
    						'is_person' => 'true',
							'is_address' => 'false',
							'format' => 'json',
    							);
  //NPI API URL
  				$url = 'http://docnpi.com/api/index.php?' . http_build_query($params);
				$homepage = file_get_contents($url);
				$json = json_decode($homepage);
				$jsonArray = (array) $json;
 //parse JSON
 				$count=0;
				foreach($jsonArray as $value){
					
					$fname=$value->first_name;
					$lname=$value->last_name;
					$address=$value->address;
					$city=$value->city;
					$state=$value->state;
					$zip=$value->zip;
					$id=$value->npi;
					foreach($value->tax_array as $specialty){
							$specialtyArray[]=$specialty;
					}

				}
			} */	
	
	}

class mysql{
	var $con;
	function __construct($db=array()) {
		$default = array(
			'host' => 'localhost',
			'user' => 'root',
			'pass' => '',
			'db' => 'test'
		);
		$db = array_merge($default,$db);
		$this->con=mysql_connect($db['host'],$db['user'],$db['pass'],true) or die ('Error connecting to MySQL');
		mysql_select_db($db['db'],$this->con) or die('Database '.$db['db'].' does not exist!');
	}
	function __destruct() {
		mysql_close($this->con);
	}
	function query($s='',$rows=false,$organize=true) {
		if (!$q=mysql_query($s,$this->con)) return false;
		if ($rows!==false) $rows = intval($rows);
		$rez=array(); $count=0;
		$type = $organize ? MYSQL_NUM : MYSQL_ASSOC;
		while (($rows===false || $count<$rows) && $line=mysql_fetch_array($q,$type)) {
			if ($organize) {
				foreach ($line as $field_id => $value) {
					$table = mysql_field_table($q, $field_id);
					if ($table==='') $table=0;
					$field = mysql_field_name($q,$field_id);
					$rez[$count][$table][$field]=$value;
				}
			} else {
				$rez[$count] = $line;
			}
			++$count;
		}
		if (!mysql_free_result($q)) return false;
		return $rez;
	}
	function execute($s='') {
		
		if (mysql_query($s,$this->con)) return true;
		return false;
	}
	function select($options) {
		$default = array (
			'table' => '',
			'fields' => '*',
			'condition' => '1',
			'order' => '1',
			'limit' => 50
		);
		$options = array_merge($default,$options);
		$sql = "SELECT {$options['fields']} FROM {$options['table']} WHERE {$options['condition']} ORDER BY {$options['order']} LIMIT {$options['limit']}";
		return $this->query($sql);
	}
	function row($options) {
		$default = array (
			'table' => '',
			'fields' => '*',
			'condition' => '1',
			'order' => '1'
		);
		$options = array_merge($default,$options);
		$sql = "SELECT {$options['fields']} FROM {$options['table']} WHERE {$options['condition']} ORDER BY {$options['order']}";
		$result = $this->query($sql,1,false);
		if (empty($result[0])) return false;
		return $result[0];
	}
	function get($table=null,$field=null,$conditions='1') {
		if ($table===null || $field===null) return false;
		$result=$this->row(array(
			'table' => $table,
			'condition' => $conditions,
			'fields' => $field
		));
		if (empty($result[$field])) return false;
		return $result[$field];
	}
	function update($table=null,$array_of_values=array(),$conditions='FALSE') {
		if ($table===null || empty($array_of_values)) return false;
		$what_to_set = array();
		foreach ($array_of_values as $field => $value) {
			if (is_array($value) && !empty($value[0])) $what_to_set[]="`$field`='{$value[0]}'";
			else $what_to_set []= "`$field`='".mysql_real_escape_string($value,$this->con)."'";
		}
		$what_to_set_string = implode(',',$what_to_set);
		print "UPDATE $table SET $what_to_set_string WHERE $conditions";
		return $this->execute("UPDATE $table SET $what_to_set_string WHERE $conditions");
	}
	function insert($table=null,$array_of_values=array()) {
		if ($table===null || empty($array_of_values) || !is_array($array_of_values)) return false;
		$fields=array(); $values=array();
		foreach ($array_of_values as $id => $value) {
			$fields[]=$id;
			if (is_array($value) && !empty($value[0])) $values[]=$value[0];
			else $values[]="'".mysql_real_escape_string($value,$this->con)."'";
		}
		$s = "INSERT INTO $table (".implode(',',$fields).') VALUES ('.implode(',',$values).')';
		if (mysql_query($s,$this->con)) return mysql_insert_id($this->con);
		return false;
	}
	function delete($table=null,$conditions='FALSE') {
		if ($table===null) return false;
		return $this->execute("DELETE FROM $table WHERE $conditions");
	}
}

?>