<?php 
include("lib/init.php");	
<<<<<<< HEAD
?>
<html>
<head>

<script type="text/javascript">
$(document).ready(function(){
$("p").click(function(){
$(this).hide();
});
});
</script>
</head>

<body>
<p>If you click on me, I will disappear.</p>
</body>

</html>
=======
$author="arnaudo pa";
$noMiddleInitial="Laird J";
$test=strpos($middleInitial," ");
$testQuery= "SELECT id FROM authors WHERE name LIKE '".$author."'";
$result=mysql_query($testQuery);
$rows = mysql_num_rows($result);
if($rows > 0){
	echo "Rows";
}
else {
	echo "none";
}
?>
>>>>>>> 3d48cf7924670f3e2097750153247b890b7838c3
