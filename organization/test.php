<?php
include("../lib/init.php");
include("get-author-edges.php");
echo "AUTHOR COMPLETE<BR>";
include("get-orgs-address.php");
echo "ORGS COMPLETE<BR>";
include("get-edu-edges.php");
echo "EDU COMPLETE<BR>";
include("build-cache-table.php");
echo "ALL DONE";
?>