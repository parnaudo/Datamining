<?php 
include("lib/init.php");
$Start = getTime(); 
$dataminer=new dataMiner;
$paperInfo=$dataminer->eFetch(21290704);
echo "TESTING";
?>