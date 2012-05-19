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

$atomQuery="select atomId from authors where atomId!=0 group by atomId having count(id) > 1 order by atomId ASC";
$result=mysql_query($atomQuery);
while($row=mysql_fetch_array($result)){
	$authorQuery="SELECT * from authors where atomId=".$row['atomId'];
	$authorResult=mysql_query($authorQuery);
	$lengthTest=0;
	$keepId=0;
	$deleteId=0;
	$testArray=array();
	$nameArray=array();
	while($authorRow=mysql_fetch_array($authorResult)){
		$test=strlen($authorRow['name']);
		$testArray[$authorRow['id']]=$test;
		$nameArray[$authorRow['id']]=$authorRow['name'];

	}
	$maxIndex = array_search(max($testArray), $testArray);
	if(hasDuplicates($testArray)==TRUE){
		echo "DUPLICATES";
		print_r($nameArray);
		foreach($testArray as $key=>$value){
			$updateAuthor="UPDATE authors set duplicateFlag=1 where id=".$key;
			echo $updateAuthor;
			mysql_query($updateAuthor);
		}
	}
	else{
		foreach($nameArray as $key=>$value){
			if($key!==$maxIndex){
				$updateInstance="UPDATE coAuthorInstance SET coAuthor=".$maxIndex.", query='".$nameArray[$maxIndex]."' where coAuthor=".$key;
				$deleteAuthor="DELETE FROM authors where id=".$key ;
				mysql_query($updateInstance);
				mysql_query($deleteAuthor);
			}
		}
	}

}

$End = getTime(); 
echo "Time taken = ".number_format(($End - $Start),2)." secs";
?>