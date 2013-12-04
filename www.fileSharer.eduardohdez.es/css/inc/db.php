<?php

	$dbhost = "localhost:3306";
	$dbuser = "*****";
	$dbpass = "****";

	$db = mysql_connect($dbhost,$dbuser,$dbpass) or die("Cannot connect to DB");
	$database = '*****';
	
	mysql_select_db($database);
?>
