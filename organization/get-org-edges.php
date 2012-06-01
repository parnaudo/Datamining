<?php 
echo "OMG";
include("../lib/init.php");
$Start = getTime(); 
$table='orgTemp';
$dataminer=new dataMiner;

clearTable($table);
$queryDoctors = "select dstAtomId,srcIsotopeId, count(distinct atomBondId) from atomBonds where bondId in (3,7) and srcAtomId IN (select atomId from mixtureAtoms where mixtureId=1176) group by dstAtomId having count(distinct atomBondId) > 1 order by count( distinct atomBondId) desc";
echo $queryDoctors;
//$queryDoctors = "select * from neurologist where paperCount>10 and paperCountFullAuthor>6 order by paperCount Desc";
$result = mysql_query($queryDoctors) or die(mysql_error());
while($row=mysql_fetch_array($result)){
	$nodeCount++;
	$getPhysicians="SELECT distinct srcAtomId from atomBonds where dstAtomId=".$row['dstAtomId']. " AND srcAtomId IN (select atomId from mixtureAtoms where mixtureId=1176)";

	getOrgInfo($row['srcIsotopeId'],$row['dstAtomId'],$table);
	$physicianResult=mysql_query($getPhysicians);
	while($physicianRow=mysql_fetch_array($physicianResult)){
		//echo $physicianRow['srcAtomId']."<BR>";
		$source=$physicianRow['srcAtomId'];
		$target=$row['dstAtomId'];
		$valueArray=array(
			'source'=>$source,
			'target'=>$target,
			'weight'=>'8.0',
			'class'=>'1'
		);
		insertEdge($valueArray);
	}
}
		//	$updateQuery="UPDATE topneurologistsnetworkmeasures set firstName='".mysql_escape_string($firstName)."',lastName='".mysql_escape_string($lastName)."',middleName='".mysql_escape_string($middleName)."' where Id='".$row['id']."'";
		//echo $updateQuery;
		//mysql_query($updateQuery) or die ("Error in query: $query. ".mysql_error());
		//if there are papers, insert an author record	
$End = getTime(); 
echo "Time taken = ".number_format(($End - $Start),2)." secs with rows: ".$count;

?>