<?php

	include_once $_SERVER['DOCUMENT_ROOT'].'/m/data/config_old.php';

	$connect = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Could not connect to server.' );
	$charset = 'utf-8';

	if (!$connect){
		//echo " <br/> please check config file ! <br/>";
		die(mysql_error());
	}else {
		//echo "success!";

		//utf-8 설정
		mysqli_set_charset($connect,"utf8");
		mysqli_query($connect,"set session character_set_connection=utf8;");
		mysqli_query($connect,"set session character_set_results=utf8;");
		mysqli_query($connect,"set session character_set_client=utf8;");
	}

	//mysqli_close($connect);

?>