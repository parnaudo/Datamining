<?php

/*
This script accepts as an input a list of authors names and queries the pubmed database for all the papers attributed to the author. It then parses all the authors listed on the paper, marks the first, second and last author and records them to a sepaarate table. 

Written by Paul Arnaudo 2/17/12 
*/
include("lib/init.php");	

$Start = getTime(); 
updateAuthorPosition();
//remove old data
//clearAuthorTables();

$End = getTime(); 
echo "Time taken = ".number_format(($End - $Start),2)." seconds";

?>