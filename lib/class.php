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
		function eFetch($uid){
			$sumParams = array(
   		 		'db' => 'pubmed',
				'tool' => 'SCUcitationminer',
				'email' => 'parnaudo@scu.edu',
				'retmode' => 'xml',
    			'id' => $uid,
   		 	);
   		 	
   		 	 $url= "http://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?". http_build_query($sumParams,'','&')."<BR>"; 
			$xml = simplexml_load_file($url);
  	  		$result = $xml->xpath('/PubmedArticleSet/PubmedArticle/MedlineCitation');
				//pull whatever you want from the XML, these two are not available from eSummary
  	  		 	$paperInfo=array(
				'address'=> $result[0]->Article->Affiliation,
				'abstract'=> $result[0]->Article->Abstract->AbstractText,
			
				);
			return $paperInfo;
  	  		
		}	
	
		function npi(){
		
		
		}
	
	}
	
/*	foreach($arrayTest as $info){
		
		if(is_array($info)==1){
		  	foreach($info as $authors){
		  		echo $authors['name']. " ".$authors['position']."<BR>";
		  	}
		}
		else{
		echo $info;
		}
	
	}
*/

class mysql {
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