<?php 
include("../lib/init.php");

$table="ocreNew";
authorCounts($table);
function authorCounts($table){

	$sql="SELECT atomId from $table where atomId=245243";
	echo $sql;
	$result=mysql_query($sql);
	while($row=mysql_fetch_array($result)){
			$coAuthorCount=0;
			$sql="select paper from coAuthorInstance c INNER JOIN authors a on coAuthor=a.id where atomId=".$row['atomId'];
			$sqlResult=mysql_query($sql);
			while($sqlRow=mysql_fetch_array($sqlResult)){

				$coAuthorSelect= "SELECT count(id) as count from coAuthorInstance where paper=".$sqlRow['paper'];
				$coAuthorResult=mysql_query($coAuthorSelect);
				while($coAuthorRow=mysql_fetch_array($coAuthorResult)){
					$coAuthorCount=$coAuthorCount+($coAuthorRow['count']-1);
				}
			}	
			$updateQuery="UPDATE $table SET numCoauthors='".$coAuthorCount."' WHERE atomId=".$row['atomId'];
			echo $updateQuery."<BR>";
			//mysql_query($updateQuery);
			$sql="select count(atomId) as count from coAuthorInstance c INNER JOIN authors a on coAuthor=a.id where atomId=".$row['atomId'];
			$sqlResult=mysql_query($sql);
			while($sqlRow=mysql_fetch_array($sqlResult)){
				$updateQuery="UPDATE $table SET numPublications='".$sqlRow['count']."' WHERE atomId=".$row['atomId'];
				echo $updateQuery."<BR>";
				//mysql_query($updateQuery);
			}

			$sql="select count(atomId) as count from coAuthorInstance c INNER JOIN authors a on coAuthor=a.id where atomId=".$row['atomId']." and authorPosition=1";
			$sqlResult=mysql_query($sql);
			while($sqlRow=mysql_fetch_array($sqlResult)){
				$updateQuery="UPDATE $table SET numPublicationsFirstAuthor='".$sqlRow['count']."' WHERE atomId=".$row['atomId'];
				echo $updateQuery."<BR>";
				//mysql_query($updateQuery);
			}
	}
}	
?>