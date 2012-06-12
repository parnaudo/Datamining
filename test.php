<?php


include("lib/init.php");
$Start = getTime(); 

$dataminer=new dataMiner;
$table="orgTemp";
$atomQuery="SELECT * from education where years!=''";
$result=mysql_query($atomQuery);
$count='';
$stringreplace=array("-","&","(",")","|","/",",",".",";","=","#",":","'","+","?");
while($row=mysql_fetch_array($result)){
		$source=$row['atomId'];
		$referenceDate=processDate($row['years'],$row['type']);
		echo $row['atomId']." WAS AT SCHOOL:";
		var_dump($referenceDate);		
		$edgeQuery="SELECT * from education where name like '".$row['name']."' AND atomId!=".$row['atomId']." AND years!=''";
		$edgeResult=mysql_query($edgeQuery);
		$rowTest=mysql_num_rows($edgeResult);
		if($rowTest > 1){
			while($edgeRow=mysql_fetch_array($edgeResult)){
				$valueArray1=array();
				$valueArray2=array();
				$target=$edgeRow['atomId'];
				$testDate=processDate($edgeRow['years'],$edgeRow['type']);				
				$weight=0;
				for($i=$testDate['start'];$i<=$testDate['end'];$i++){
					if(checkDateRange($referenceDate['start'], $referenceDate['end'], $i)==1){
						$weight=$weight+2;
						
					}
				
				}
				if($row['type']==$edgeRow['type'] && $weight > 0){
					$weight=$weight*1.5;
				}
			/*	if(($row['type'],'b%') || stripos($edgeRow['type'],'b%'){
					$weight=$weight*1.5;
				}*/
				$existTest=edgeExists($source,$target,'edge');
				if($existTest < 1 && $weight > 0){
					
					$valueArray1=array(
						'source'=>$source,
						'target'=>$target,
						'weight'=>$weight,
						'class'=>'3'
					);
				
					$valueArray2=array(
						'source'=>$target,
						'target'=>$source,
						'weight'=>$weight,
						'class'=>'3'
					);
					
					insertQuery($valueArray1,'edge');
					insertQuery($valueArray2,'edge');
				}
				elseif($existTest > 0 && $weight > 0){
					$updateQuery="UPDATE edgeCache set weight=(weight+".$weight.") where source=".$source." AND target=".$target." AND class=3";
					echo $updateQuery;
					mysql_query($updateQuery);
					$updateQuery="UPDATE edgeCache set weight=(weight+".$weight.") where source=".$target." AND target=".$source." AND class=3";
					mysql_query($updateQuery);

				
				}
				else{
					echo "EDGES EXIST ALREADY!";
				
				
				}
			}
		
		}
}	

$End = getTime(); 
echo "Time taken = ".number_format(($End - $Start),2)." secs with matches ".$count;
?>