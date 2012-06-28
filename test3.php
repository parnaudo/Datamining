
<?php 
include("lib/init.php");
$test="select name,particles.value,matters.matterId, particles.atomId FROM particles inner join matters on matters.matterId=particles.matterId where atomId IN (select atomId from nodepruned where institution='' ) and matters.matterId=672 and value!='' order by name";
$result=mysql_query($test);
while($row=mysql_fetch_array($result)){
	$institution=ucwords(strtolower($row['value']));
	$updateQuery="UPDATE nodepruned set institution='".$institution."' where atomId=".$row['atomId'];
	mysql_query($updateQuery);
	echo $updateQuery."<BR>";

}
/*
$edgeTypes="SELECT DISTINCT class FROM edge";
$edgeCount=getRowCount($edgeTypes);
certainty($edgeCount);
*/
?>