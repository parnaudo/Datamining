<?php 
include("lib/init.php");
$sources=array(100,150,200,250,300,350);
		for($i=0;$i < sizeof($sources);$i++){
			for($k=0;$k <sizeof($sources);$k++){
				if($i!==$k){
					$insertEdge="INSERT INTO edgeCache (source,target,direction,weight) VALUES (".$sources[$i].",".$sources[$k].",Undirected,8.0)";
					echo $insertEdge."<BR>";
				
				}
			}
		}

	
?>