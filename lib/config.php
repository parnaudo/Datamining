<?php

$CFG->dbname = "zephyr";
	$CFG->dbuser = "root";

	$CFG->dbpass = 'rubberbabybuggybumpers';

	$CFG->dbtype = 'mysql';
	$CFG->dbhost = 'localhost';
	mysql_connect($CFG->dbhost,$CFG->dbuser,$CFG->dbpass) or die(mysql_error());
	mysql_select_db($CFG->dbname);

	

?>