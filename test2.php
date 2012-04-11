<?php 
function multidimensional_search($parents, $searched) { 
  if (empty($searched) || empty($parents)) { 
    return false; 
  } 
  
  foreach ($parents as $key => $value) { 
    $exists = true; 
    foreach ($searched as $skey => $svalue) { 
      $exists = ($exists && IsSet($parents[$key][$skey]) && $parents[$key][$skey] == $svalue); 
    } 
    if($exists){ return $key; } 
  } 
  
  return false; 
} 

$parents = array(); 
$parents[] = array(1320883200, 3); 
$parents[] = array(1320883200, 5); 
$parents[] = array(1318204800, 5); 

echo multidimensional_search($parents, array(1318204800, 5)); // 1 
?>