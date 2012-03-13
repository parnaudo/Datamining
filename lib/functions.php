<?php
	global $debug;
	$debug=1;
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
function clearAuthorTables(){
	$authors="DELETE FROM authors";
	$papers="DELETE FROM papers";
	$instance="DELETE FROM coAuthorInstance";
	mysql_query($authors);
	mysql_query($papers);
	mysql_query($instance);
	
	
}
function createInstanceTable($insertTable){
	
	$createTable="CREATE TABLE `".$insertTable."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `atomId` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `position` varchar(50) DEFAULT NULL,
  `tenurefrom` int(11) DEFAULT NULL,
  `tenureto` int(11) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  `isotopeId` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=142916 DEFAULT CHARSET=latin1";


	mysql_query($createTable)or die(mysql_error());
}
function createAttributesTable($insertTable){
	
	$createTable="CREATE TABLE `".$insertTable."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `atomId` int(11) DEFAULT NULL,
  `firstName` varchar(20) DEFAULT NULL,
  `middleName` varchar(20) DEFAULT NULL,
  `lastName` varchar(20) DEFAULT NULL,
  `zipcode` varchar(20) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `medschool` varchar(50) DEFAULT NULL,
  `medschoolyear` int(11) DEFAULT NULL,
  `internship` varchar(50) DEFAULT NULL,
  `internshipyear` int(11) DEFAULT NULL,
  `residency` varchar(50) DEFAULT NULL,
  `residencyyear` int(11) DEFAULT NULL,
  `fellowship` varchar(50) DEFAULT NULL,
  `menumber` varchar(11) DEFAULT NULL,
  `fellowshipyear` int(11) DEFAULT NULL,
  `specialty` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4279 DEFAULT CHARSET=latin1;
";
	mysql_query($createTable)or die(mysql_error());
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

function deleteTable($insertTable){
		mysql_query("DROP TABLE ".$insertTable."");
		if($debug)echo "Database deleted!";
} 
function clearTable($insertTable){
		mysql_query("DELETE FROM ".$insertTable."");
		if($debug)echo "Database deleted!";
} 
function getTime() 
    { 
    $a = explode (' ',microtime()); 
    return(double) $a[0] + $a[1]; 

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
	
?>