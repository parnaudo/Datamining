<?php 
include("../lib/init.php");
$Start = getTime(); 
$table='orgTemp';
$dataminer=new dataMiner;

clearTable($table);
//Right now this is limited to organizations that have more than one record from the set, can change whenever though.
$queryDoctors = "select distinct institution from ocre where institution!='' order by institution ";
$avgSQL="select avg(weight) as avg from edge";
$avgResult=mysql_query($avgSQL);
$row=mysql_fetch_array($avgResult);
$baseWeight=$row['avg']*2;
$result = mysql_query($queryDoctors) or die(mysql_error());
while($row=mysql_fetch_array($result)){

	$getPhysicians="SELECT distinct atomId from ocre where institution='".$row['institution']. "'";
//find org info and add an entity to the organization table
	//echo $getPhysicians."<BR>";
	//getOrgInfo($row['srcIsotopeId'],$row['dstAtomId'],$table);
	$physicianResult=mysql_query($getPhysicians);
	$nodes=array();
	while($physicianRow=mysql_fetch_array($physicianResult)){
		$nodes[]=$physicianRow['atomId'];
		

		//$source=$physicianRow['srcAtomId'];
		//$target=$row['dstAtomId'];
		/*$valueArray=array(
			'source'=>$source,
			'target'=>$target,
			'weight'=>'8.0',
			'class'=>'1'
		);*/
		//insertQuery($valueArray,'edge');
	}
	for($i=0;$i < sizeof($nodes);$i++){
		for($k=0;$k <sizeof($nodes);$k++){
			if($i!==$k){
				$valueArray=array(
						'source'=>$nodes[$i],
						'target'=>$nodes[$k],
						'weight'=>$baseWeight,
						'direction'=>'Directed',
						'class'=>1
					);
				insertQuery($valueArray,'edge');			
			}
		}
	}

	var_dump($nodes);
}

$End = getTime(); 
echo "Time taken = ".number_format(($End - $Start),2)." secs with rows: ".$count;
?>