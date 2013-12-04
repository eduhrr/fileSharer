<?php

	//introduce user and password
	$dbhost = "localhost:3306";
	$dbuser = "*****";
	$dbpass = "****";

	$db = mysql_connect($dbhost,$dbuser,$dbpass) or die("Cannot connect to DB");
	$database = 'itondacloud';
	
	mysql_select_db($database);
?>