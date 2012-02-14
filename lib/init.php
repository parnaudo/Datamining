<?php 
include("config.php");
include("functions.php");
$tableTest="TestingTable";
deleteTable($tableTest);
createTable($tableTest);

?>