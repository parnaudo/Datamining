<?php
include("lib/init.php");
$Start = getTime(); 

class edgeCreate {
	function education($type){
		$table='edge';
		switch($type){
			case 'md':
				$filter="AND type like '".$type."'";
				$base=2;
				$class=3;
				break;
			case 'residency':
				$filter="AND type like '".$type."'";
				$base=3;
				$class=4;
				break;
			case 'fellowship':
				$filter="AND type like '".$type."'";
				$base=4;
				$class=5;
				break;
			case 'mixed':
				$filter=" AND (type LIKE 'residency' or type LIKE 'fellowship' or type like 'phd%')";
				$base=1;
				$class=7;
				break;
			default:
				$filter=" AND type NOT LIKE 'residency' AND type NOT LIKE 'fellowship' and type NOT LIKE 'md'";
				$base=1;
				$class=6;
				break;
			}
		 
		$atomQuery="select * from education where years!=''$filter";
		$result=mysql_query($atomQuery);
		while($row=mysql_fetch_array($result)){
			$mixedFilter='';
			$source=$row['atomId'];
			$referenceDate=processDate($row['years'],$row['type']);
			var_dump($referenceDate);
			if($class==7){
				$mixedFilter=" AND type!='".$row['type']."'";
			}
			$edgeQuery="SELECT * from education where name like '".mysql_escape_string($row['name'])."' AND atomId!=".$row['atomId']." AND years!=''$filter $mixedFilter";
			$edgeResult=mysql_query($edgeQuery);
			echo $edgeQuery."<BR>";
			$rowTest=mysql_num_rows($edgeResult);
			if($rowTest > 1){
				while($edgeRow=mysql_fetch_array($edgeResult)){
					$existTest=0;
					$valueArray1=array();
					$valueArray2=array();
					$target=$edgeRow['atomId'];
					$testDate=processDate($edgeRow['years'],$edgeRow['type']);	
					$weight=0;			
					for($i=$testDate['start'];$i<=$testDate['end'];$i++){
						if(checkDateRange($referenceDate['start'], $referenceDate['end'], $i)==1){
							if($weight==0){
								$weight=$base;
							}
							else{
								$weight=$weight+($base/2);
							}
						}
					
					}
					$existTest=edgeExists($source,$target,$table,$class);
					if($existTest < 1 && $weight > 0){
						
						$valueArray1=array(
							'source'=>$source,
							'target'=>$target,
							'weight'=>$weight,
							'class'=>$class
						);
					
						$valueArray2=array(
							'source'=>$target,
							'target'=>$source,
							'weight'=>$weight,
							'class'=>$class
						);
						
						insertQuery($valueArray1,$table);
						insertQuery($valueArray2,$table);
					}
					elseif($existTest > 0 && $weight > 0){
						$updateQuery="UPDATE ".$table." set weight=(weight+".$weight.") where source=".$source." AND target=".$target." AND class=".$class;
						echo $updateQuery;
						mysql_query($updateQuery);
						$updateQuery="UPDATE ".$table." set weight=(weight+".$weight.") where source=".$target." AND target=".$source." AND class=".$class;
						echo $updateQuery;
						mysql_query($updateQuery);
		
					
					
					}
					else{
						echo "EDGES EXIST ALREADY!";
					
					
					}
				}			
			}
			
		}
	
	}



}

$type=array('md','residency','fellowship','other','mixed');
$edges=new edgeCreate;
foreach($type as $value){
	$edges->education($value);
}
$dataminer=new dataMiner;
echo "ALL DONE";
?>