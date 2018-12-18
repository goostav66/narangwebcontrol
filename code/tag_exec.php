<?php session_start(); ?>
<?php
include_once $_SERVER[DOCUMENT_ROOT].'/m/data/DB_connect.php';
	/*
	define("TAG_DB_HOST", "103.60.124.17");
	define("TAG_DB_USER", "hanji");
	define("TAG_DB_PASSWORD", "youshcall1004");
	define("TAG_DB_NAME", "jnfc");

	$connect_tag = mysqli_connect(TAG_DB_HOST, TAG_DB_USER, TAG_DB_PASSWORD, TAG_DB_NAME) or die('Could not connect to server.' );
	$charset = 'utf-8';

	if (!$connect_tag){
		//echo " <br/> please check config file ! <br/>";
		die(mysql_error());
	}else {
		//echo "success!";

		//utf-8 설정
		mysqli_set_charset($connect_tag,"utf8");
		mysqli_query($connect_tag,"set session character_set_connection=utf8;");
		mysqli_query($connect_tag,"set session character_set_results=utf8;");
		mysqli_query($connect_tag,"set session character_set_client=utf8;");
	}
*/
	/* check connection */
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}


	
	function ViewList($connect, $auth, $id){

		if($auth == 'manager'){

			$qry = "SELECT DISTINCT sc.*, stb.`idx`, stb.`shop_name`, stb.id, stb.agent_name, tag.hpno, tag.credate
					FROM (
						SELECT b.url, b.`branch_name`, a.`br_url`, a.`agent_name`, a.`id`, s.`idx`, s.`shop_name`, s.`agent_idx`,
							s.`shop_tel`
						FROM branch b
						JOIN agent a ON b.url = a.`br_url`
						JOIN shop s ON a.idx = s.`agent_idx`
					) AS stb
					JOIN serialcode sc ON stb.idx = sc.`shopSeq`
					LEFT OUTER JOIN ( SELECT hpno, tagid, credate FROM registertag ORDER BY credate DESC) AS tag ON tag.tagid = sc.`serialNum`

					GROUP BY sc.`serialNum`
					ORDER BY tag.hpno IS NULL, tag.credate DESC, sc.`insertDate` DESC";
		}
		else if($auth == 'branch'){
			$qry = "SELECT DISTINCT sc.*, stb.`idx`, stb.`shop_name`, stb.id, stb.agent_name, tag.hpno, tag.credate
					FROM (
						SELECT b.url, b.`branch_name`, a.`br_url`, a.`agent_name`, a.`id`, s.`idx`, s.`shop_name`, s.`agent_idx`,
							s.`shop_tel`
						FROM branch b
						JOIN agent a ON b.url = a.`br_url`
						JOIN shop s ON a.idx = s.`agent_idx` WHERE b.`id` = '$id'
					) AS stb
					JOIN serialcode sc ON stb.idx = sc.`shopSeq`
					LEFT OUTER JOIN ( SELECT hpno, tagid, credate FROM registertag ORDER BY credate DESC) AS tag ON tag.tagid = sc.`serialNum`

					GROUP BY sc.`serialNum`
					ORDER BY tag.hpno IS NULL, tag.credate DESC, sc.`insertDate` DESC";
		
		}
		else if($auth == 'agent'){
			$qry = "SELECT DISTINCT sc.*, stb.`idx`, stb.`shop_name`, stb.id, stb.agent_name, tag.hpno, tag.credate
					FROM (
						SELECT b.url, b.`branch_name`, a.`br_url`, a.`agent_name`, a.`id`, s.`idx`, s.`shop_name`, s.`agent_idx`,
							s.`shop_tel`
						FROM branch b
						JOIN agent a ON b.url = a.`br_url`
						JOIN shop s ON a.idx = s.`agent_idx` WHERE a.`id` = '$id'
					) AS stb
					JOIN serialcode sc ON stb.idx = sc.`shopSeq`
					LEFT OUTER JOIN ( SELECT hpno, tagid, credate FROM registertag ORDER BY credate DESC) AS tag ON tag.tagid = sc.`serialNum`

					GROUP BY sc.`serialNum`
					ORDER BY tag.hpno IS NULL, tag.credate DESC, sc.`insertDate` DESC";
		
		}
		/* Select queries return a resultset */
		if ($result = mysqli_query($connect, $qry)) {
			//printf("Select returned %d rows.\n", mysqli_num_rows($result));
		}else{
			 echo("Error description ViewList: " . mysqli_error($connect));
		}

		return $result;
	}


?>