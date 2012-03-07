<?php

/*
This script accepts as an input a list of authors names and queries the pubmed database for all the papers attributed to the author. It then parses all the authors listed on the paper, marks the first, second and last author and records them to a sepaarate table. 

Written by Paul Arnaudo 2/17/12 
*/
include("lib/init.php");	

$Start = getTime(); 
//remove old data
//clearAuthorTables();

//query to get doctor set, can really be from anywhere
$scoringArray=array( array(NULL,1,2,500,'x'),
					 array(1,NULL,.8,.6,.2),
					 array(2,.8,NULL,.4,.1),
					 array(500,.6,.4,NULL,.1),
					 array('x',.2,.1,.1,NULL)
					);
					var_dump($scoringArray);
					print "-".$scoringArray[1][1].$scoringArray[0][2].$scoringArray[0][3].$scoringArray[0][4]." <br>";
					print $scoringArray[1][0]." <br>" ;
					print $scoringArray[2][0]." <br>";
					print $scoringArray[3][0]." <br>";
					print $scoringArray[4][0]." <br>";	
$query="SELECT coAuthorPosition, authorPosition FROM authors WHERE id=1889231";
$result=mysql_query($query);
while($rowauthor=mysql_fetch_array($result)){
echo "coauthor: ".$rowauthor['coAuthorPosition'] ." Author position:".$rowauthor['authorPosition']."<br>";				
}
for ($row = 0; $row < 5; $row++)
{
    echo "<li><b>The row number $row</b>";
    echo "<ul>";

	
	
    for ($col = 0; $col < 5; $col++)
    {
		
        echo $col.": ".$scoringArray[$row][$col]."<br>";
		while($row===0){
			if(	$scoringArray[$row][$col]===$rowauthor['coAuthorPosition']){
			echo "BINGO";	
			}
			
		}
	}

    echo "</ul>";
    echo "</li>";
}
					
$End = getTime(); 
echo "Time taken = ".number_format(($End - $Start),2)." secs";
?>