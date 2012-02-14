<?php
$correct = "0051975080";
$incorrect = "051975080";
$test = str_pad($incorrect, 10, "0", STR_PAD_LEFT);
//echo $test;
echo $correct."<br>";
echo $incorrect."<br>";
echo $test;

?>