<?php
$time = "17 February 2007";
$CorrectTime = date('Y-m-d',strtotime($time));

//echo $test;
echo $time."<br>";
echo "time: ". $CorrectTime."<br>";
echo strtotime("17 February 2007");

?>