<?php 
include("lib/init.php");
class attributeUpdate{
	function getPubCount($atomId){
			$coAuthorCount=0;
			$sql="select paper from coAuthorInstance c INNER JOIN authors a on coAuthor=a.id where atomId=".$atomId;
			$sqlResult=mysql_query($sql);
			while($sqlRow=mysql_fetch_array($sqlResult)){

				$coAuthorSelect= "SELECT count(id) as count from coAuthorInstance where paper=".$sqlRow['paper'];
				$coAuthorResult=mysql_query($coAuthorSelect);
				while($coAuthorRow=mysql_fetch_array($coAuthorResult)){
					$coAuthorCount=$coAuthorCount+($coAuthorRow['count']-1);
				}
			}	
			$updateQuery="UPDATE node SET numCoauthors='".$coAuthorCount."' WHERE atomId=".$atomId;
			echo $updateQuery."<BR>";
		
	
	}		
	function 
	
	
}

	$node=1712046;
	$threshold=7.99;
	$table='edgeCache';
	$select="SELECT atomId FROM node";
	$result=mysql_query($select);
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
			$updateQuery="UPDATE node SET numCoauthors='".$coAuthorCount."' WHERE atomId=".$row['atomId'];
			echo $updateQuery."<BR>";
			mysql_query($updateQuery);
			$sql="select count(atomId) as count from coAuthorInstance c INNER JOIN authors a on coAuthor=a.id where atomId=".$row['atomId'];
			$sqlResult=mysql_query($sql);
			while($sqlRow=mysql_fetch_array($sqlResult)){
				$updateQuery="UPDATE node SET numPublications='".$sqlRow['count']."' WHERE atomId=".$row['atomId'];
				echo $updateQuery."<BR>";
				mysql_query($updateQuery);
			}

			$sql="select count(atomId) as count from coAuthorInstance c INNER JOIN authors a on coAuthor=a.id where atomId=".$row['atomId']." and authorPosition=1";
			$sqlResult=mysql_query($sql);
			while($sqlRow=mysql_fetch_array($sqlResult)){
				$updateQuery="UPDATE node SET numPublicationsFirstAuthor='".$sqlRow['count']."' WHERE atomId=".$row['atomId'];
				echo $updateQuery."<BR>";
				mysql_query($updateQuery);
			}
	}
?>