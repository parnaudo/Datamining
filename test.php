<?php

/*
This script creates an address rank based on the number of instances they have in the data set

Written by Paul Arnaudo 3/29/12 
*/
include("lib/init.php");	
$connection = array(
	'host' => 'localhost',
	'user' => 'root',
	'pass' => 'root',
	'db' => 'zephyr'
);
echo "TESTING";
$mysql = new mysql($connection);
$test=$mysql->query("SELECT * FROM papers");
var_dump($test);

}					
?>