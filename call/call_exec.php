<?php session_start(); ?>
<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/m/data/DB_connect_old.php';

	/* check connection */
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}

	//호출 등록 코드
	if(isset($_POST['call_rgst']) && !empty($_POST['call_rgst'])){
		$call_rgst = $_POST['call_rgst'];
		if($call_rgst == '1') registerCall($connect);
	}else{
		//echo "fail";
	}

	// 지사 선택 후 대리점 선택하는 로직
	if(isset($_POST['branch_url']) && !empty($_POST['branch_url'])){
		$branch_url = $_POST['branch_url'];
		$id = $_POST['agent_id'];
		
		getAgent($connect,$branch_url, $id);
	}

	// 대리점 선택 후 가맹점 선택하는 로직
	if(isset($_POST['agent_idx']) && !empty($_POST['agent_idx'])){
		$agent_idx = $_POST['agent_idx'];
		//echo "success";
		getShop($connect,$agent_idx);
	}

	// 검색
	if(isset($_POST['search_type']) && !empty($_POST['search_type']) ){
		$search_type = $_POST['search_type'];
		$search_text = $_POST['search_text'];
		$id = $_POST['search_id'];
		$auth = $_POST['search_auth'];
		//echo "success";
		searchCall($connect, $search_type, $search_text, $auth, $id);
	}else{
		//echo "fail";
	}
	
	//-----------------------------------------------------------호출관리 페이지에서 새로운 데이터 유무 체크
	if(isset($_POST['call_refresh']) && !empty($_POST['call_refresh'])){
		$call_refresh = $_POST['call_refresh'];
		$old_list_num = $_POST['old_list_num'];
		
		$record_num = mysqli_query($connect,"SELECT count(*) AS `count` FROM calldrivertb;");
		$record_row = mysqli_fetch_assoc($record_num);
		$record_count = $record_row['count'];

		//echo $old_list_num.",".$record_count;

		if($old_list_num < $record_count){
			
			/*
			// 호출 시 사용자에게 푸쉬 메세지 전송
			$qry = "SELECT hpno FROM `calldrivertb` ORDER BY idx DESC LIMIT 1";

			if ($result = mysqli_query($connect, $qry)) {
				
				$value = mysqli_fetch_object($result);

				$hpno = $value->hpno;
				
				setPushInfo($connect, $hpno);
			}
			
			setPushInfo($connect, $hpno);
			*/
			// 호출관리 페이지로 data 전송
			echo "y";	
		}

	}
	//-----------------------------------------------------------접수대기 idx 체크
	if(isset($_POST['call_O_check']) && !empty($_POST['call_O_check'])){
		$qry = "SELECT idx FROM calldrivertb WHERE state = 'O' AND credate >= CAST(CURRENT_TIMESTAMP AS DATE)";
			
		/* Select queries return a resultset */
		if ($result = mysqli_query($connect, $qry)) {
				while ($row = mysqli_fetch_assoc($result)) {
				$o_idx = $o_idx.$row['idx']."/";
			}
		}
	
		echo $o_idx;
	}
	//-----------------------------------------------------------접수대기 idx -> 고객취소 체크
	if(isset($_POST['call_cancle_idx']) && !empty($_POST['call_cancle_idx'])){
		$idx_arr = split("/", $_POST['call_cancle_idx']);
		$count_cancel = 0;
		
		for($i = 0; $i < (sizeof($idx_arr)-1); $i++){
			$qry = "SELECT * FROM calldrivertb WHERE idx = '$idx_arr[$i]' AND state = 'C'";
			if ($result = mysqli_query($connect, $qry)) {
				if (mysqli_num_rows($result) > 0) {
					$count_cancel++;
				}
			}
		}

		if($count_cancel == 0) echo "n";
		else if($count_cancel > 0) echo "y";
	}
	
	function getBranch($connect, $auth, $id){
		
		if($auth == 'manager' || $auth == 'center'){
			$qry = "SELECT  `branch_name`, `url`
					FROM  `branch`  ";
		}

		/* Select queries return a resultset */
		if ($result = mysqli_query($connect, $qry)) {
			//printf("Select returned %d rows.\n", mysqli_num_rows($result));
		}else{
			echo("Error description: " . mysqli_error($connect));
		}
		
		return $result;
	}

	function getAgent($connect, $branch_url, $id){

		$qry = "SELECT  `agent_name`, `idx`
				FROM  `agent` 
				WHERE  `br_url` =  '$branch_url'
				AND `id` IS NOT NULL AND `id` != ''";	

		/* Select queries return a resultset */
		if ($result = mysqli_query($connect, $qry)) {
			//printf("Select returned %d rows.\n", mysqli_num_rows($result));
		}else{
			 echo("Error description: " . mysqli_error($connect));
		}

		$num_rows = mysqli_num_rows($result);
		$ag_name = "";
		$chk_rows = 0;

		while ($row = mysqli_fetch_assoc($result)) {
			$chk_rows++;
			$ag_name = $ag_name.$row['agent_name']."/".$row['idx'];

			if($chk_rows != $num_rows){
				$ag_name = $ag_name.",";
			}
			
		}

		echo $ag_name;
	}

	function getShop($connect, $agent_idx){
		$qry = "SELECT  `shop_name`, `url`
				FROM  `shop` 
				WHERE  `agent_idx` =  '$agent_idx'";
		

		/* Select queries return a resultset */
		if ($result = mysqli_query($connect, $qry)) {
			//printf("Select returned %d rows.\n", mysqli_num_rows($result));
		}else{
			 echo("Error description: " . mysqli_error($connect));
		}

		$num_rows = mysqli_num_rows($result);
		$shop_name = "";
		$chk_rows = 0;

		while ($row = mysqli_fetch_assoc($result)) {
			$chk_rows++;
			$shop_name = $shop_name.$row['shop_name'].":/:".$row['url'];

			if($chk_rows != $num_rows){
				$shop_name = $shop_name.",";
			}
			
		}

		echo $shop_name;
	}

	// 호출 항목 변경 코드
	if(isset($_POST['change_idx']) && !empty($_POST['change_idx'])){
		$idx = $_POST['change_idx'];
		$item_name = $_POST['item_name'];
		$value = $_POST['value'];
		
		editCallItem($connect, $item_name, $value, $idx);
	}
	
	
	function ViewList($connect){

		$qry = "SELECT c.`idx`, c.`hpno`, c.`credate`, c.`current_position`, c.`dst_position`, 
						c.`price`, c.`mid_pass`,	c.`add_call`, c.`state`,
						r.`tagid`, r.`shop_idx`,s.`shop_name`, s.`shop_tel`,
						a.`agent_name`, a.`branch_name`

				FROM calldrivertb c

				LEFT OUTER JOIN registertag r ON c.hpno = r.hpno
				LEFT OUTER JOIN shop s ON s.`idx` = r.`shop_idx`
				LEFT OUTER JOIN agent a ON a.idx = s.`agent_idx`

				ORDER BY  c.credate DESC";
		
		/* Select queries return a resultset */
		if ($result = mysqli_query($connect, $qry)) {
			//printf("Select returned %d rows.\n", mysqli_num_rows($result));
		}else{
			 echo("Error description ViewList: " . mysqli_error($connect));
		}

		return $result;
	}
	
	// FOR PAGENATION
	function _ViewList($connect, $start_from, $num_rec_per_page, $auth, $id){

		if($auth == 'manager' or $auth == 'center'){

			$qry = "SELECT DISTINCT c.*,
					s.`shop_name`, s.`shop_addr`, s.`shop_tel`, a.`agent_name`, b.`branch_name`

					FROM calldrivertb c
					LEFT JOIN shop s ON c.url = s.url
					LEFT JOIN agent a ON a.idx = s.`agent_idx`
					LEFT JOIN branch b ON a.`br_url` = b.`url`

					WHERE c.url IS NOT NULL
					
					UNION

					SELECT DISTINCT c.*,
					s.`shop_name`, s.`shop_addr`, s.`shop_tel`, a.`agent_name`, b.`branch_name`

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

					WHERE c.url IS NULL

					GROUP BY c.`idx` HAVING COUNT(*) >= 1
					ORDER BY `credate` DESC
					LIMIT $start_from, $num_rec_per_page";
		}

		else if ($auth == 'branch'){

			$qry = "SELECT DISTINCT c.*,
					s.`shop_name`, s.`shop_addr`, s.`shop_tel`, a.`agent_name`, b.`branch_name`

					FROM calldrivertb c

					LEFT JOIN shop s ON c.url = s.url
					LEFT OUTER JOIN agent a ON a.idx = s.`agent_idx`
					LEFT OUTER JOIN branch b ON a.`br_url` = b.`url`

					WHERE b.id = '$id' AND c.url IS NOT NULL

					UNION

					SELECT DISTINCT c.*,
					s.`shop_name`, s.`shop_addr`, s.`shop_tel`, a.`agent_name`, b.`branch_name`

					FROM calldrivertb c

					LEFT OUTER JOIN (
						SELECT * FROM registertag r 
						LEFT OUTER JOIN serialcode sc ON r.`tagid` = sc.`serialNum`
						GROUP BY sc.`serialNum` HAVING COUNT(*) >= 1
					) AS tag
					ON c.`hpno` = tag.hpno
									
					LEFT OUTER JOIN shop s ON s.`idx` = tag.`shopSeq`
					LEFT OUTER JOIN agent a ON a.idx = s.`agent_idx`
					LEFT OUTER JOIN branch b ON a.`br_url` = b.`url`

					WHERE b.id = '$id' AND c.url IS NULL

					GROUP BY c.`idx` HAVING COUNT(*) >= 1
					ORDER BY  credate DESC
					LIMIT $start_from, $num_rec_per_page";
		
		}
		else if ($auth == 'agent'){

			$qry = "SELECT DISTINCT c.*,
					s.`shop_name`, s.`shop_addr`, s.`shop_tel`, a.`agent_name`, b.`branch_name`

					FROM calldrivertb c

					LEFT JOIN shop s ON c.url = s.url
					LEFT OUTER JOIN agent a ON a.idx = s.`agent_idx`
					LEFT OUTER JOIN branch b ON a.`br_url` = b.`url`

					WHERE a.id = '$id' AND c.url IS NOT NULL

					UNION

					SELECT DISTINCT c.*,
					s.`shop_name`, s.`shop_addr`, s.`shop_tel`, a.`agent_name`, b.`branch_name`

					FROM calldrivertb c

					LEFT OUTER JOIN (
						SELECT * FROM registertag r 
						LEFT OUTER JOIN serialcode sc ON r.`tagid` = sc.`serialNum`
						GROUP BY sc.`serialNum` HAVING COUNT(*) >= 1
					) AS tag
					ON c.`hpno` = tag.hpno
									
					LEFT OUTER JOIN shop s ON s.`idx` = tag.`shopSeq`
					LEFT OUTER JOIN agent a ON a.idx = s.`agent_idx`
					LEFT OUTER JOIN branch b ON a.`br_url` = b.`url`

					WHERE a.id = '$id' AND c.url IS NULL

					GROUP BY c.`idx` HAVING COUNT(*) >= 1
					ORDER BY  credate DESC
					LIMIT $start_from, $num_rec_per_page";
		
		}
		
		/* Select queries return a resultset */
		if ($result = mysqli_query($connect, $qry)) {
			//printf("Select returned %d rows.\n", mysqli_num_rows($result));
		}else{
			 echo("Error description _ViewList: " . mysqli_error($connect));
		}

		$chk_list_num = mysqli_num_rows($result);

		return $result;
	}

	function _ViewListCount($connect,  $auth, $id){

		if($auth == 'manager' or $auth == 'center'){

			/*$qry = "SELECT DISTINCT c.*,
					s.`shop_name`, s.`shop_addr`, s.`shop_tel`, a.`agent_name`, b.`branch_name`

					FROM calldrivertb c
					LEFT JOIN shop s ON c.url = s.url
					LEFT JOIN agent a ON a.idx = s.`agent_idx`
					LEFT JOIN branch b ON a.`br_url` = b.`url`

					WHERE c.url IS NOT NULL
					
					UNION

					SELECT DISTINCT c.*,
					s.`shop_name`, s.`shop_addr`, s.`shop_tel`, a.`agent_name`, b.`branch_name`

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

					WHERE c.url IS NULL

					GROUP BY c.`idx` HAVING COUNT(*) >= 1
					ORDER BY `credate` DESC";*/
			$qry = "SELECT idx FROM calldrivertb";
				
			if($result = mysqli_query($connect, $qry)){
				$count = mysqli_num_rows($result);
			}else{
				echo("Error description _ViewListCount: " . mysqli_error($connect));
			}
			return $count;
		}

		else if ($auth == 'branch'){

			/* $qry = "SELECT DISTINCT c.*,
					s.`shop_name`, s.`shop_addr`, s.`shop_tel`, a.`agent_name`, b.`branch_name`

					FROM calldrivertb c

					LEFT JOIN shop s ON c.url = s.url
					LEFT OUTER JOIN agent a ON a.idx = s.`agent_idx`
					LEFT OUTER JOIN branch b ON a.`br_url` = b.`url`

					WHERE b.id = '$id' AND c.url IS NOT NULL

					UNION

					SELECT DISTINCT c.*,
					s.`shop_name`, s.`shop_addr`, s.`shop_tel`, a.`agent_name`, b.`branch_name`

					FROM calldrivertb c

					LEFT OUTER JOIN (
						SELECT * FROM registertag r 
						LEFT OUTER JOIN serialcode sc ON r.`tagid` = sc.`serialNum`
						GROUP BY sc.`serialNum` HAVING COUNT(*) >= 1
					) AS tag
					ON c.`hpno` = tag.hpno
									
					LEFT OUTER JOIN shop s ON s.`idx` = tag.`shopSeq`
					LEFT OUTER JOIN agent a ON a.idx = s.`agent_idx`
					LEFT OUTER JOIN branch b ON a.`br_url` = b.`url`

					WHERE b.id = '$id' AND c.url IS NULL

					GROUP BY c.`idx` HAVING COUNT(*) >= 1
					ORDER BY credate DESC"; */
				$qry = "SELECT url FROM branch WHERE id = '$id' ";
				$count = 0;
				if($result = mysqli_query($connect, $qry)){
					$br_url = "";
					if($row = mysqli_fetch_assoc($result)){
						$br_url = $row['url'];
						
						$qry2 = "SELECT idx FROM calldrivertb WHERE url LIKE CONCAT ('$br_url', '%') ";
						if($result = mysqli_query($connect, $qry2)){
							$count = mysqli_num_rows($result);
						} else{
							 echo("Error description _ViewListCount: " . mysqli_error($connect));
						}
					}
				}
				return $count;
		
		}
		else if ($auth == 'agent'){
/*
			$qry = "SELECT DISTINCT c.*,
					s.`shop_name`, s.`shop_addr`, s.`shop_tel`, a.`agent_name`, b.`branch_name`

					FROM calldrivertb c

					LEFT JOIN shop s ON c.url = s.url
					LEFT OUTER JOIN agent a ON a.idx = s.`agent_idx`
					LEFT OUTER JOIN branch b ON a.`br_url` = b.`url`

					WHERE a.id = '$id' AND c.url IS NOT NULL

					UNION

					SELECT DISTINCT c.*,
					s.`shop_name`, s.`shop_addr`, s.`shop_tel`, a.`agent_name`, b.`branch_name`

					FROM calldrivertb c

					LEFT OUTER JOIN (
						SELECT * FROM registertag r 
						LEFT OUTER JOIN serialcode sc ON r.`tagid` = sc.`serialNum`
						GROUP BY sc.`serialNum` HAVING COUNT(*) >= 1
					) AS tag
					ON c.`hpno` = tag.hpno
									
					LEFT OUTER JOIN shop s ON s.`idx` = tag.`shopSeq`
					LEFT OUTER JOIN agent a ON a.idx = s.`agent_idx`
					LEFT OUTER JOIN branch b ON a.`br_url` = b.`url`

					WHERE a.id = '$id' AND c.url IS NULL

					GROUP BY c.`idx` HAVING COUNT(*) >= 1
					ORDER BY credate DESC"; */
			$qry = "SELECT br_url, url FROM agent WHERE id = '$id'";
			$count = 0;
			
			if ($result = mysqli_query($connect, $qry)) {
						$br_url = "";
						$url = "";
						
						if ($row = mysqli_fetch_assoc($result)) {
							$br_url = $row['br_url'];
							$url = $row['url'];
							
							$qry2 = "SELECT idx FROM calldrivertb WHERE url LIKE CONCAT ('$br_url', '$url', '%') ";
							
							if($result = mysqli_query($connect, $qry2)){
								$count = mysqli_num_rows($result);
							}
						} else{
							 echo("Error description _ViewListCount: " . mysqli_error($connect));
						}
			}
			return $count;
			
		}
		
		/* Select queries return a resultset */
		if ($result = mysqli_query($connect, $qry)) {
			//printf("Select returned %d rows.\n", mysqli_num_rows($result));
		}else{
			 echo("Error description _ViewListCount: " . mysqli_error($connect));
		}

		$total_records = mysqli_num_rows($result);  //count number of records
		

		return $total_records;
	}

	function registerCall($connect){

		$hpno = $_POST['hpno'];
		$current_position = $_POST['current_position'];
		$dst_position = $_POST['dst_position'];
		$mis_pass = $_POST['mis_pass'];
		$add_call = $_POST['add_call'];
		$price = $_POST['price'];
		$state = $_POST['state'];
		$shop_url = $_POST['shop'];
		$credate = date('Y-m-d H:i:s');

		$qry = "INSERT INTO  `jnfc`.`calldrivertb` (
			`idx` ,
			`hpno`,
			`current_position`,
			`dst_position`,
			`Latitude`,
			`Longitude` ,
			`mid_pass`,
			`add_call`,
			`price`,
			`state`,
			`credate`,
			`url`
			)
			VALUES (
			NULL , '$hpno', '$current_position', '$dst_position', '', '', '$mis_pass', '$add_call', '$price',
			'$state', '$credate', '$shop_url'
			); ";

		if ($connect->query($qry) === TRUE) {
			echo "success";
			/*
			$qry2 = "SELECT DISTINCT c.*, tag.tagid, tag.serialNum, tag.shopSeq, tag.serialText,
					s.`shop_name`, s.`shop_tel`, a.`agent_name`, b.`branch_name`

				FROM calldrivertb c

				LEFT OUTER JOIN (
					SELECT * FROM registertag r 
					LEFT OUTER JOIN serialcode sc ON r.`tagid` = sc.`serialNum`
					GROUP BY sc.`serialNum` HAVING COUNT(*) >= 1
				) AS tag
				ON c.`hpno` = tag.hpno
				
				LEFT OUTER JOIN shop s ON s.`idx` = tag.`shopSeq`
				LEFT OUTER JOIN agent a ON a.idx = s.`agent_idx`
				LEFT OUTER JOIN branch b ON a.`br_url` = b.`url`
				
				WHERE c.`hpno` = '$hpno' AND c.`credate` = '$credate'";
			
			$data = array();

			if($result = mysqli_query($connect, $qry2)){
				//printf("Select returned %d rows.\n", mysqli_num_rows($result));
				while($row = mysqli_fetch_assoc($result)){
					$t = new stdClass();
					$t->idx = $row['idx'];
					$t->shop_name = $row['shop_name'];
					$t->shop_tel = $row['shop_tel'];
					$t->hpno = $row['hpno'];
					$t->credate = $row['credate'];
					$t->current_position = $row['current_position'];
					$t->dst_position = $row['dst_position'];
					$t->mid_pass = $row['mid_pass'];
					$t->add_call = $row['add_call'];
					$t->price = $row['price'];
					$t->state = $row['state'];
					$t->price = $row['branch_name'];
					$t->agent_name = $row['agent_name'];
					$data[] = $t;
					unset($t);
				}

			} else {
				echo("Error description: " . mysqli_error($connect));
				$data = array( 0 => 'empty');
			}

			$json_data = json_encode((array) $data);
			print_r($json_data);		
			*/
		} else {
			echo "Error rgstcall: " . $qry . "<br>" . $connect->error;
		}
			
	}

	function editCallItem($connect, $item_name, $value, $idx){
		
		$qry = "UPDATE calldrivertb
				SET `$item_name` = '$value'
				WHERE `idx` = '$idx'";

		/* Select queries return a resultset */
		if (mysqli_query($connect, $qry)) {
			
			//printf ("Updated records: %d\n", mysql_affected_rows());
			echo "success";

		}else{
			echo("Error description: " . mysqli_error($connect));
		}
		
	}
	
	//호출 등록후 새로운 리스트아이템 추가를 위한 코드
	function newList($connect){

		$qry = "SELECT 
				c.`idx`, c.`hpno`, c.`credate`, c.`current_position`, c.`dst_position`, c.`price`, c.`mid_pass`,	c.`add_call`, c.`state`,
				r.`tagid`, r.`shop_idx`, 
				s.`shop_name`, s.`shop_tel`,
				a.`agent_name`, a.`branch_name`

			FROM calldrivertb c

				JOIN registertag r ON c.hpno = r.hpno
				JOIN shop s ON s.`idx` = r.`shop_idx`
				JOIN agent a ON a.idx = s.`agent_idx`

			ORDER BY  c.credate DESC";
		
		/* Select queries return a resultset */
		if ($result = mysqli_query($connect, $qry)) {
			//printf("Select returned %d rows.\n", mysqli_num_rows($result));
		}else{
			echo("Error description: " . mysqli_error($connect));
		}
		
		return $result;
	}
	
	// 호출정보 검색
	function searchCall($connect, $search_type, $search_text, $auth, $id){
		
		if($auth == 'manager' || $auth == 'center' ){
			$condition = "";
		}
		else if($auth == 'branch'){
			$condition = "AND br_id = '$id'";
		}
		else if($auth == 'agent'){
			$condition = "AND ag_id = '$id'";
		}

		$qry = "SELECT tb.* FROM (
				SELECT DISTINCT c.*,
				s.`shop_name`, s.`shop_addr`, s.`shop_tel`, a.`agent_name`, b.`branch_name`, a.`id`AS ag_id, b.`id`AS br_id
				FROM calldrivertb c
				LEFT JOIN shop s ON c.url = s.url
				LEFT JOIN agent a ON a.idx = s.`agent_idx`
				LEFT JOIN branch b ON a.`br_url` = b.`url`

				WHERE c.url IS NOT NULL

				UNION

				SELECT DISTINCT c.*,
				s.`shop_name`, s.`shop_addr`, s.`shop_tel`, a.`agent_name`, b.`branch_name`, a.`id`AS ag_id, b.`id`AS br_id

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

				WHERE c.url IS NULL

				GROUP BY c.`idx` HAVING COUNT(*) >= 1
				ORDER BY `credate`
				)AS tb
				WHERE $search_type LIKE '%$search_text%' $condition";
		
		
		/* Select queries return a resultset */
		if ($result = mysqli_query($connect, $qry)) {
			//printf("Select returned %d rows.\n", mysqli_num_rows($result));
			$num_rows = mysqli_num_rows($result);
		}else{
			 echo("Error description searchShop: " . mysqli_error($connect));
		}

		if ($num_rows == 0) echo "0";
		else {
			$data = array();

			while($row = mysqli_fetch_assoc($result)){
				$t = new stdClass();
				$t->idx = $row['idx'];
				$t->url = $row['url'];
				$t->hpno = $row['hpno'];
				$t->credate = $row['credate'];
				$t->shop_name = $row['shop_name'];
				$t->branch_name = $row['branch_name'];
				$t->agent_name = $row['agent_name'];
				$t->shop_tel = $row['shop_tel'];
				$t->current_position = $row['current_position'];
				$t->dst_position = $row['dst_position'];
				$t->position = $row['shop_addr'];
				$t->mid_pass = $row['mid_pass'];
				$t->add_call = $row['add_call'];
				$t->price = $row['price'];
				$t->state = $row['state'];
				$data[] = $t;
				unset($t);
			}

			$json_data = json_encode((array) $data);
			print_r($json_data);
		}
		
	}

	function setPushInfo($connect, $hpno) {
		
		$sql = "SELECT token FROM `fcmuser` WHERE hpno = '$hpno'";	
		
		$result = mysqli_query($connect,$sql);

		while($row = mysqli_fetch_assoc($result)){
			$tokens[] = $row['token'];
		}
		
		$myMessage = "기사 배정 중이오니 잠시만 기다려주세요.";
		

		$message = array("message" => $myMessage);
		$message_status = send_notification($tokens, $message);
		//echo $message_status;
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
