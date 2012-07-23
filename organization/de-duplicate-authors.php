<?php
/*
This script takes all the atomIds that have more than one author record attached to them, and reconciles the names if there are only two records with the same name minus first initial. Will set a duplicate flag if it cannot reconcile.
EXAMPLE:
Arnaudo P
Arnaudo PF will reconcile to Arnaudo PF
Arnaudo PG
Arnaudo PF will NOT reconcile
*/

include("../lib/init.php");
$Start = getTime(); 

$dataminer=new dataMiner;
deduplicateAuthors();
$End = getTime(); 
echo "Time taken = ".number_format(($End - $Start),2)." secs";
?>