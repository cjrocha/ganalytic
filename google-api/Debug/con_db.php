<?php
	error_reporting(0);

  $link = mysql_connect('host',"user","pass");
	if(!$link)
	{
		die('Failed to connect to server: ' . mysql_error());
		print ("Failed to connect to server");
	}
    $db = mysql_select_db('incontinence',$link);
		if(!$db)
	{
		die("Unable to select database");
		print ("Unable to select DB");
	}
?>