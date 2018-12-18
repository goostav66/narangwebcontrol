<?php session_start(); ?>
<?php
include_once $_SERVER[DOCUMENT_ROOT].'/m/data/DB_connect.php';

	//$connect = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	
	/* check connection */
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}

	if(isset($_POST['fcm_message']) && !empty($_POST['fcm_message'])){
		$action = $_POST['fcm_message'];
		//echo "success<br/>";
		requstNotification($connect);
	}else{
		//echo "fail";
	}
	
	function ViewList($connect){
		
		$qry = "SELECT r.hpno, r.`credate`, t2.url, t2.dst_position, t2.shop_name, t2.agent_name, t2.branch_name, t2.cnt,			t2.sum_call, t2.branch_name, t2.agent_name, t2.shop_name
				FROM registertag r
				LEFT OUTER JOIN (
				SELECT t1.*, COUNT(*) AS cnt, SUM(t1.add_call) AS sum_call
				FROM(
					SELECT DISTINCT c.*,
					s.`shop_name`, s.`position`, s.`shop_tel`, a.`agent_name`, b.`branch_name`

					FROM calldrivertb c
					LEFT JOIN shop s ON c.url = s.url
					LEFT JOIN agent a ON a.idx = s.`agent_idx`
					LEFT JOIN branch b ON a.`br_url` = b.`url`

					WHERE c.url IS NOT NULL AND c.`state` = 'S'

					UNION

					SELECT DISTINCT c.*,
					s.`shop_name`, s.`position`, s.`shop_tel`, a.`agent_name`, b.`branch_name`

					FROM calldrivertb c
					LEFT OUTER JOIN (
						SELECT * FROM registertag r 
						LEFT OUTER JOIN serialcode sc ON r.`tagid` = sc.`serialNum`
						GROUP BY sc.`serialNum` HAVING COUNT(*) >= 1
					) AS tag
					ON c.`hpno` = tag.hpno
					LEFT JOIN shop s ON s.`idx` = tag.`shopSeq`
					LEFT JOIN agent a ON a.idx = s.`agent_idx`
					LEFT JOIN branch b ON a.`br_url` = b.`url`

					WHERE c.url IS NULL AND c.`state` = 'S'
				) AS t1

				GROUP BY t1.`hpno` 
				ORDER BY t1.credate DESC
				)AS t2 ON r.`hpno` = t2.hpno

				GROUP BY r.`hpno` 
				ORDER BY dst_position IS NULL, r.`credate`DESC";
		
		/* Select queries return a resultset */
		if ($result = mysqli_query($connect, $qry)) {
			//printf("Select returned %d rows.\n", mysqli_num_rows($result));
		}else{
			 //echo("Error description: " . mysqli_error($connect));
		}
		
		return $result;
	}

	function msgList($connect){
		$qry = "SELECT r.hpno, r.`credate`, t2.dst_position, t2.shop_name, t2.agent_name, t2.branch_name, t2.cnt, 
		t2.branch_name, t2.agent_name, t2.shop_name, t2.sum_call, t.token
				FROM registertag r
				LEFT OUTER JOIN (
				SELECT t1.*, COUNT(*) AS cnt, SUM(t1.add_call) AS sum_call
				FROM(
					SELECT DISTINCT c.*,
					s.`shop_name`, s.`position`, s.`shop_tel`, a.`agent_name`, b.`branch_name`

					FROM calldrivertb c
					LEFT JOIN shop s ON c.url = s.url
					LEFT JOIN agent a ON a.idx = s.`agent_idx`
					LEFT JOIN branch b ON a.`br_url` = b.`url`

					WHERE c.url IS NOT NULL AND c.`state` = 'S'

					UNION

					SELECT DISTINCT c.*,
					s.`shop_name`, s.`position`, s.`shop_tel`, a.`agent_name`, b.`branch_name`

					FROM calldrivertb c
					LEFT OUTER JOIN (
						SELECT * FROM registertag r 
						LEFT OUTER JOIN serialcode sc ON r.`tagid` = sc.`serialNum`
						GROUP BY sc.`serialNum` HAVING COUNT(*) >= 1
					) AS tag
					ON c.`hpno` = tag.hpno
					LEFT JOIN shop s ON s.`idx` = tag.`shopSeq`
					LEFT JOIN agent a ON a.idx = s.`agent_idx`
					LEFT JOIN branch b ON a.`br_url` = b.`url`

					WHERE c.url IS NULL AND c.`state` = 'S'
				) AS t1

				GROUP BY t1.`hpno` 
				ORDER BY t1.credate DESC
				)AS t2 ON r.`hpno` = t2.hpno
				JOIN (
				SELECT hpno, token FROM fcmuser
				GROUP BY `hpno` 
				ORDER BY `credate` DESC
				)AS t ON t.hpno = r.`hpno`
				GROUP BY r.`hpno` 
				ORDER BY dst_position IS NULL, r.`credate`DESC";

		/* Select queries return a resultset */
		if ($result = mysqli_query($connect, $qry)) {
			//printf("Select returned %d rows.\n", mysqli_num_rows($result));
		}else{
			 //echo("Error description: " . mysqli_error($connect));
		}
		
		return $result;
	}

	function requstNotification($connect){
		
		// 메세지 발송 페이지에서 발송상대와 메세지 가져오기
	
		//print_r($_POST['s_idx']).'<br/>';
		//print_r($_POST['idx']).'<br/>';
		//echo "배열크기 : ".count($_POST['idx']).'<br/>';

		$idx = $_POST['idx']; // 선택된 토큰의 idx
		$isArray;

		if(count($idx) > 1){ //idx 배열일 때, 하나일 때
			
			$isArray = 0;

			array_walk($idx , 'intval');
			$idx_sql = implode(',', $idx); //sql문에 사용할 idx 문구
			
			//echo "check : array".'<br/>';
			print_r($idx).'<br/>';
			//echo $idx_sql.'<br/>';

		}else if(count($idx) == 1){
			$isArray = 1;
			//echo "check : one".'<br/>';
			//echo "idx : ";
			//print_r($idx[0]).'<br/>';
		}

		//데이터베이스에 접속해서 토큰들을 가져와서 FCM에 발신요청
		$tokens = array();

		if($isArray == 0){ 
			$sql = "Select token From `fcmuser`  Where hpno IN ({$idx_sql})";
		}else{
			$sql = "Select token From `fcmuser`  Where hpno IN ('$idx[0]')";	
		}			
		
		$result = mysqli_query($connect,$sql);

		while($row = mysqli_fetch_assoc($result)){
			$tokens[] = $row['token'];
		}

		//echo "token : ".'<br/>';
		//print_r($tokens);
		
		$myMessage = $_POST['message']; //폼에서 입력한 메세지를 받음
		
		if ($myMessage == ""){
			$myMessage = "N대다 알림.";
		}

		$message = array("message" => $myMessage);
		$message_status = send_notification($tokens, $message);
		echo $message_status;
	}

	function send_notification ($tokens, $message)
	{
		$url = 'https://fcm.googleapis.com/fcm/send';
		$fields = array(
			 'registration_ids' => $tokens,
			 'data' => $message
			);

		$headers = array(
			'Authorization:key =' . GOOGLE_API_KEY,
			'Content-Type: application/json'
			);

	   $ch = curl_init();
       curl_setopt($ch, CURLOPT_URL, $url);
       curl_setopt($ch, CURLOPT_POST, true);
       curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);  
       curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
       curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
       $result = curl_exec($ch);           
       if ($result === FALSE) {
           die('Curl failed: ' . curl_error($ch));
       }
       curl_close($ch);
       return $result;
	}
?>
