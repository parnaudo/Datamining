<?php 
$middleInitial="Laird JR";
$noMiddleInitial="Laird J";
$test=strpos($middleInitial," ");
$testString=substr($middleInitial,0,strlen($middleInitial)-1);
echo $testString;
echo $test;
echo strlen($middleInitial);
echo strlen($noMiddleInitial);
?>