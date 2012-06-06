<?php


include("lib/init.php");
$Start = getTime(); 

$dataminer=new dataMiner;
$table="orgTemp";
$atomQuery="Select p.atomId,value,m.name,p.isotopeId,p.tableId,isFixed,m.matterId,s.address from matters m
INNER JOIN particles p on p.matterId=m.matterId
INNER JOIN schizo s on s.atomId=p.atomId
where value!='' and isFixed=1 and m.matterId=617 
ORDER BY m.name";
$result=mysql_query($atomQuery);
$count='';
$stringreplace=array("-","&","(",")","|","/",",",".",";","=","#",":","'","+","?");
while($row=mysql_fetch_array($result)){
	$address=trim($row['address']);
	$isotopeId=$row['isotopeId'];
	$atomId=$row['atomId'];
	$position=$row['value'];
	$testQuery="select value,name,m.matterId from particles p INNER JOIN matters m on p.matterId=m.matterId where isotopeId=".$isotopeId." AND m.matterId=159 AND value!=''";
	$testResult=mysql_query($testQuery);
	while($testRow=mysql_fetch_array($testResult)){
		$value=trim($testRow['value']);	
	if(stripos($address,$value)!==FALSE||stripos($value,$address)!==FALSE){
		echo "POSITION ".$position;
		echo " MATCH HERE ";
		$count++;
			
	}
		echo "ORIGINAL ADDRESS: ".$address." TEST ADDRESS: ".$value." ".$atomId."<BR>";
	}
}
$End = getTime(); 
echo "Time taken = ".number_format(($End - $Start),2)." secs with matches ".$count;
?>