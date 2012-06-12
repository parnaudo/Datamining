<?php 
include("lib/init.php");
$start_date = '2009';

$end_date = '2012';

$date_from_user = '2007';

//$test=check_in_range($start_date, $end_date, $date_from_user);
//var_dump( $test);

processDate('2009','md');
$test=processDate('1979-Present','phd');	
var_dump($test);

?>