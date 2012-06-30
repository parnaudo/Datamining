<?php 
include("lib/init.php");
	Class networkAnalysis{

		public $node;
		public $table;
		public $threshold;
	    function __construct($x,$y,$z){
	    	$this->node=$x;
	    	$this->table=$y;
	    	$this->threshold=$z; 
	    }
	    
		function reach(){
			$nodeArray=array();
			$firstTargets=$this->getTargets();
			$reach=0;
			$origin=$this->node;
			for($i=0;$i < sizeof($firstTargets);$i++){
				$test=in_array($firstTargets['id'][$i],$nodeArray);
				if(in_array($firstTargets['id'][$i],$nodeArray)===FALSE){
					$nodeArray[]=$firstTargets['id'][$i];
				}
				$reach=$reach+($firstTargets['weight'][$i]*.5);

	
				$this->node=$firstTargets['id'][$i];
				$secondTargets=$this->getTargets();
				for($j=0;$j < sizeof($secondTargets);$j++){
					if(in_array($secondTargets['id'][$j],$nodeArray)===FALSE){
						$nodeArray[]=$secondTargets['id'][$j];
					}
					if($secondTargets['id'][$j]==$origin){
						
					}
					else{
						$reach=$reach+($secondTargets['weight'][$j]*.25);	
					}
				}
				
			}
			//print_r($nodeArray);
			return $reach;

						
				
							
				
		}
		function getTargets(){
			$targets=array();
			$weights=array();
			$returnArray=array();
			$select="SELECT target,weight from ".$this->table." where source=".$this->node." AND weight > ".$this->threshold;
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
	$node=1712046;
	$threshold=7.99;
	$table='edgeCache';
	$select="SELECT atomId FROM nodepruned";
	$result=mysql_query($select);
	while($row=mysql_fetch_array($result)){
			$test=new networkAnalysis($row['atomId'],$table,$threshold);
			$testTargets=$test->reach();
			$updateQuery="UPDATE nodepruned SET reach='".$testTargets."' WHERE atomId=".$row['atomId'];
			echo $updateQuery;
			mysql_query($updateQuery);
	}
	$test=new networkAnalysis($node,$table,$threshold);
	$testTargets=$test->reach();
	var_dump($testTargets);
?>