<?php 
function multidimensional_search($parents, $searched) { 
  if (empty($searched) || empty($parents)) { 
    return false; 
  } 
  
  foreach ($parents as $key => $value) { 
	
    $exists = true; 
    foreach ($searched as $skey => $svalue) { 
      $exists = ($exists && IsSet($parents[$key][$skey]) && $parents[$key][$skey] == $svalue); 
	  echo "key: ". $key ." value: ".$value." skey: ".$skey." svalue: ".$svalue."<br>";
    } 
    if($exists){ return array($key,$skey);  } 
  } 
  
  return false; 
} 

$scoringArray=array( array(NULL,1,2,500,'x'),
					 array(1,NULL,.8,.6,.2),
					 array(2,.8,NULL,.4,.1),
					 array(500,.6,.4,NULL,.1),
					 array('x',.2,.1,.1,NULL)
					);
print_r( multidimensional_search($scoringArray, array(1))); // 1 
print_r( multidimensional_search($scoringArray, array(500)));
echo $scoringArray[3][0];
	
?>