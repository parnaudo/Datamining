<?php

/*
This script creates an address rank based on the number of instances they have in the data set

Written by Paul Arnaudo 3/29/12 
*/
include("lib/init.php");	

$percentiles=array(
				90=>13,
				80=>7,
				70=>4,
				60=>3,
				50=>2,
			);

quintile(3,$percentiles);
function quintile($value, $array){
	

	if($value >= $array['90']){
		echo "90";
	}
	elseif($value <=$array['90'] && $value >=$array['80']){
		echo "80";
	}
	elseif($value <=$array['80'] && $value >=$array['70']){
		echo 70;
	}	
	elseif($value <=$array['70'] && $value >=$array['60']){
		echo 60;
	}
	else{
		echo "NEITHER";	
	}


}
?>