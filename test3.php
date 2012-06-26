
<?php 
include("lib/init.php");

$edgeTypes="SELECT DISTINCT class FROM edge";
$edgeCount=getRowCount($edgeTypes);
certainty($edgeCount);

?>