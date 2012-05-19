<?php


include("lib/init.php");
$Start = getTime(); 

$dataminer=new dataMiner;

$atomQuery="select distinct m.atomId, p.value,p.isotopeId  from mixtureAtoms m inner join particles p on p.atomId=m.atomId where mixtureId=1176 and matterId=672 and p.value!='' order by value";
$result=mysql_query($atomQuery);
while($row=mysql_fetch_array($result)){
	$isotopeId=$row['isotopeId'];
	$atomId=$row['atomId'];
	$institution=$row['value'];
	$queryTerms=array('address','city','state','zip');
	$address=array(
		'address'=>'',
		'city'=>'',
		'state'=>'',
		'zip'=>'',	
	);	
	foreach($queryTerms as $value){
		$isotopeQuery="SELECT m.matterId,value from particles p INNER JOIN matters m ON p.matterId=m.matterId where isotopeId=".$isotopeId." AND name like '".$value."%'";
		echo $isotopeQuery;
		$addressRecords=mysql_query($isotopeQuery);

		while($addressRow=mysql_fetch_array($addressRecords)){
			if(stripos('state',$value)===0){
				$address['state']=$addressRow['value'];
			} 
			if(stripos('address',$value)===0){
				$address['address']=$addressRow['value'];
			} 
			if(stripos('city',$value)===0){
				$address['city']=$addressRow['value'];
			} 
			if(stripos('zip',$value)===0){
				$address['zip']=$addressRow['value'];
			} 							
		}
	}
	$insertQuery="INSERT INTO formalLeader_copy (isotopeId,atomId,institution,address,city,state,zipcode) VALUES (".$isotopeId.",".$atomId.",'".mysql_escape_string($institution)."','".mysql_escape_string($address['address'])."','".mysql_escape_string($address['city'])."','".$address['state']."','".$address['zip']."')";
mysql_query($insertQuery);
echo $insertQuery;
echo "<BR>";
}


$End = getTime(); 
echo "Time taken = ".number_format(($End - $Start),2)." secs";
?>