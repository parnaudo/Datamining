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
$queryDoctors = "SELECT atomId, firstName,middleName, lastName from tempdoc where lastName!='' AND atomId=372548 ";

$result = mysql_query($queryDoctors) or die(mysql_error());
while($row=mysql_fetch_array($result)){
  $query='';
  $middle=substr($row['middleName'],0,1);
  echo $row['middleName']. " MIDDLE IS ".$middle;
}
?>
>>>>>>> 3d48cf7924670f3e2097750153247b890b7838c3
