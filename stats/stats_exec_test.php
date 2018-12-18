<?php session_start(); ?>
<?php
include_once $_SERVER[DOCUMENT_ROOT].'/m/data/DB_connect.php';

	/* check connection */
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
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

	// 통계 호출 검색
	if(isset($_POST['search_stats']) && !empty($_POST['search_stats']) ){
		$branch_url = $_POST['branch'];
		$agent_idx = $_POST['agent'];
		$shop_idx = $_POST['shop'];
		$date_before = $_POST['date_before_stats'];
		$date_after = $_POST['date_after_stats'];
	
		ViewList_search_date($connect, $branch_url, $agent_idx, $shop_idx, $date_before, $date_after);
	}

	// 기간별 호출 검색
	if(isset($_POST['date_before']) && !empty($_POST['date_before'])){
		$date_before = $_POST['date_before'];
		$date_after = $_POST['date_after'];
		$idx = $_POST['idx'];
	
		ViewList_search($connect, $idx, $date_before, $date_after);
	}

	// 완료 호출 검색
	if(isset($_POST['state_search']) && !empty($_POST['state_search'])){
		$state = $_POST['state_search'];
		$idx = $_POST['shop_idx'];
	
		ViewList_search_state($connect, $idx, $state);
	}

	function getBranch($connect, $auth, $id){
		
		if($auth == 'manager'){
			$qry = "SELECT  `branch_name`, `url`
					FROM  `branch`  ";
		
		}else if($auth == 'agent'){
			$qry = "SELECT branch_name, br_url
					FROM (
						SELECT * FROM agent a WHERE id = '$id'
					)AS id
					JOIN branch b ON id.br_url = b.`url`";
		}else if($auth == 'branch'){
			$qry = "SELECT branch_name, url
					FROM branch
					WHERE id = '$id'";
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

		if($id == ''){ //관리자, 지사
		
			$qry = "SELECT  `agent_name`, `idx`
					FROM  `agent` 
					WHERE  `br_url` =  '$branch_url'
					AND `id` IS NOT NULL AND `id` != ''";
		}else{ //대리점
			$qry = "SELECT  `agent_name`, `idx`
					FROM  `agent` 
					WHERE  `br_url` =  '$branch_url' AND id = '$id'
					AND `id` IS NOT NULL AND `id` != ''";
		}

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
		$qry = "SELECT  `shop_name`, `idx`
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
			$shop_name = $shop_name.$row['shop_name'].":/:".$row['idx'];

			if($chk_rows != $num_rows){
				$shop_name = $shop_name.",";
			}
			
		}

		echo $shop_name;
	}
	
	
	function Count_all($connect, $auth, $id){
		
		if($auth == 'manager'){
			$qry = "SELECT COUNT(*) AS cnt, SUM(tb.add_call)AS add_sum, MAX(tb.credate) AS max_date, MIN(tb.credate) AS min_date
					FROM (
					SELECT credate, add_call FROM calldrivertb
					WHERE state = 'S' GROUP BY idx
					)AS tb";
		}else if($auth == 'branch'){
			$qry = "SELECT COUNT(*) AS cnt, SUM(tb.add_call)AS add_sum, MAX(tb.credate) AS max_date, MIN(tb.credate) AS min_date
					FROM (

						SELECT c.credate, c.add_call FROM calldrivertb c
						LEFT JOIN shop s ON c.url = s.url
						LEFT OUTER JOIN agent a ON a.idx = s.`agent_idx`
						LEFT OUTER JOIN branch b ON a.`br_url` = b.`url`
						
						WHERE state = 'S' AND c.url IS NOT NULL AND b.id = '$id'
						
						UNION

						SELECT DISTINCT c.credate, c.add_call

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

						WHERE state = 'S' AND c.url IS NULL AND b.id = '$id'
						
						GROUP BY c.`idx` HAVING COUNT(*) >= 1
						
					)AS tb";
		}else if($auth == 'agent'){
			$qry = "SELECT COUNT(*) AS cnt, SUM(tb.add_call)AS add_sum, MAX(tb.credate) AS max_date, MIN(tb.credate) AS min_date
					FROM (

						SELECT c.credate, c.add_call FROM calldrivertb c
						LEFT JOIN shop s ON c.url = s.url
						LEFT OUTER JOIN agent a ON a.idx = s.`agent_idx`
						LEFT OUTER JOIN branch b ON a.`br_url` = b.`url`
						
						WHERE state = 'S' AND c.url IS NOT NULL AND a.id = '$id'
						
						UNION

						SELECT DISTINCT c.credate, c.add_call

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

						WHERE state = 'S' AND c.url IS NULL AND a.id = '$id'
						
						GROUP BY c.`idx` HAVING COUNT(*) >= 1
						

					)AS tb";
		}
		
		/* Select queries return a resultset */
		if ($result = mysqli_query($connect, $qry)) {
			//printf("Select returned %d rows.\n", mysqli_num_rows($result));
			
		}else{
			 echo("Error description Count_all: " . mysqli_error($connect));
		}

		return $result; // cnt, max_date, min_date
	}

	function Count_today($connect, $auth, $id, $today, $yesterday){
		if($auth == 'manager'){
			$qry = "SELECT COUNT(*) AS cnt, SUM(tb.add_call)AS add_sum
					FROM (
					SELECT credate, add_call FROM calldrivertb
					WHERE state = 'S' AND credate BETWEEN '$yesterday 00:00:00' AND '$today 23:59:59'
					GROUP BY idx
					)AS tb";
		}else if($auth == 'branch'){
			$qry = "SELECT COUNT(*) AS cnt, SUM(tb.add_call)AS add_sum, MAX(tb.credate) AS max_date, MIN(tb.credate) AS min_date
					FROM (

						SELECT c.credate, c.add_call FROM calldrivertb c
						LEFT JOIN shop s ON c.url = s.url
						LEFT OUTER JOIN agent a ON a.idx = s.`agent_idx`
						LEFT OUTER JOIN branch b ON a.`br_url` = b.`url`
						
						WHERE state = 'S' AND c.url IS NOT NULL AND b.id = '$id' AND c.credate BETWEEN '$yesterday 00:00:00' AND '$today 23:59:59'
						
						UNION

						SELECT DISTINCT c.credate, c.add_call

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

						WHERE state = 'S' AND c.url IS NULL AND b.id = '$id' AND c.credate BETWEEN '$yesterday 00:00:00' AND '$today 23:59:59'
						
						GROUP BY c.`idx` HAVING COUNT(*) >= 1

					)AS tb";
		}else if($auth == 'agent'){
			$qry = "SELECT COUNT(*) AS cnt, SUM(tb.add_call)AS add_sum, MAX(tb.credate) AS max_date, MIN(tb.credate) AS min_date
					FROM (

						SELECT c.credate, c.add_call FROM calldrivertb c
						LEFT JOIN shop s ON c.url = s.url
						LEFT OUTER JOIN agent a ON a.idx = s.`agent_idx`
						LEFT OUTER JOIN branch b ON a.`br_url` = b.`url`
						
						WHERE state = 'S' AND c.url IS NOT NULL AND a.id = '$id' AND c.credate BETWEEN '$yesterday 00:00:00' AND '$today 23:59:59'
						
						UNION

						SELECT DISTINCT c.credate, c.add_call

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

						WHERE state = 'S' AND c.url IS NULL AND a.id = '$id' AND c.credate BETWEEN '$yesterday 00:00:00' AND '$today 23:59:59'
						
						GROUP BY c.`idx` HAVING COUNT(*) >= 1

					)AS tb";
		}
		
		/* Select queries return a resultset */
		if ($result = mysqli_query($connect, $qry)) {
			//printf("Select returned %d rows.\n", mysqli_num_rows($result));
			
		}else{
			 echo("Error description Count_today: " . mysqli_error($connect));
		}

		return $result; // cnt
	}

	function ViewList_today($connect, $auth, $id, $today, $yesterday){
		if($auth == 'manager'){
			$qry = "SELECT tb.agent_name, tb.branch_name, tb.shop_name, SUM(tb.add_call)AS add_sum, COUNT(*)AS cnt, CAST(tb.credate AS DATE)AS mydate
					FROM (

						SELECT DISTINCT c.*,
						s.`shop_name`, s.`position`, s.`shop_tel`, a.`agent_name`, b.`branch_name`

						FROM calldrivertb c
						LEFT JOIN shop s ON c.url = s.url
						LEFT JOIN agent a ON a.idx = s.`agent_idx`
						LEFT JOIN branch b ON a.`br_url` = b.`url`

						WHERE c.url IS NOT NULL AND state = 'S'
							AND c.credate BETWEEN '$yesterday 00:00:00' AND '$today 23:59:59'
						
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

						WHERE c.url IS NULL AND state = 'S'
							AND c.credate BETWEEN '$yesterday 00:00:00' AND '$today 23:59:59'

						GROUP BY c.`idx` HAVING COUNT(*) >= 1
						
					)AS tb
					GROUP BY shop_name
					ORDER BY credate DESC";
		}else if($auth == 'branch'){
			$qry = "SELECT tb.agent_name, tb.branch_name, tb.shop_name, SUM(tb.add_call)AS add_sum, COUNT(*)AS cnt, CAST(tb.credate AS DATE)AS mydate
					FROM (

						SELECT DISTINCT c.*,
						s.`shop_name`, s.`position`, s.`shop_tel`, a.`agent_name`, b.`branch_name`

						FROM calldrivertb c
						LEFT JOIN shop s ON c.url = s.url
						LEFT JOIN agent a ON a.idx = s.`agent_idx`
						LEFT JOIN branch b ON a.`br_url` = b.`url`

						WHERE c.url IS NOT NULL AND state = 'S'
							AND c.credate BETWEEN '$yesterday 00:00:00' AND '$today 23:59:59'  AND b.id = '$id'
						
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

						WHERE c.url IS NULL AND state = 'S'
							AND c.credate BETWEEN '$yesterday 00:00:00' AND '$today 23:59:59' AND b.id = '$id'

						GROUP BY c.`idx` HAVING COUNT(*) >= 1
						
					)AS tb
					GROUP BY shop_name
					ORDER BY tb.`credate` DESC";
		}else if($auth == 'agent'){
			$qry = "SELECT tb.agent_name, tb.branch_name, tb.shop_name, SUM(tb.add_call)AS add_sum, COUNT(*)AS cnt, CAST(tb.credate AS DATE)AS mydate
					FROM (

						SELECT DISTINCT c.*,
						s.`shop_name`, s.`position`, s.`shop_tel`, a.`agent_name`, b.`branch_name`

						FROM calldrivertb c
						LEFT JOIN shop s ON c.url = s.url
						LEFT JOIN agent a ON a.idx = s.`agent_idx`
						LEFT JOIN branch b ON a.`br_url` = b.`url`

						WHERE c.url IS NOT NULL AND state = 'S'
							AND c.credate BETWEEN '$yesterday 00:00:00' AND '$today 23:59:59'  AND a.id = '$id'
						
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

						WHERE c.url IS NULL AND state = 'S'
							AND c.credate BETWEEN '$yesterday 00:00:00' AND '$today 23:59:59' AND a.id = '$id'

						GROUP BY c.`idx` HAVING COUNT(*) >= 1
						
					)AS tb
					GROUP BY shop_name
					ORDER BY tb.`credate` DESC";
		}

		
		/* Select queries return a resultset */
		if ($result = mysqli_query($connect, $qry)) {
			//printf("Select returned %d rows.\n", mysqli_num_rows($result));
			
		}else{
			 echo("Error description ViewList_today: " . mysqli_error($connect));
		}

		return $result; 
	}

	function ViewList($connect, $auth, $id){
		if($auth == 'manager'){
			$qry = "SELECT t1.*
					FROM (
						SELECT tb.idx, tb.agent_name, tb.branch_name, tb.shop_name,  SUM(tb.add_call)AS add_sum, COUNT(*)AS cnt
						FROM (

							SELECT DISTINCT c.add_call, c.credate,
							s.`idx`, s.`shop_name`, s.`position`, s.`shop_tel`, a.`agent_name`, b.`branch_name`

							FROM calldrivertb c
							LEFT JOIN shop s ON c.url = s.url
							LEFT JOIN agent a ON a.idx = s.`agent_idx`
							LEFT JOIN branch b ON a.`br_url` = b.`url`

							WHERE c.url IS NOT NULL AND state = 'S'
							
							UNION

							SELECT DISTINCT c.add_call, c.credate,
							s.`idx`, s.`shop_name`, s.`position`, s.`shop_tel`, a.`agent_name`, b.`branch_name`

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

							WHERE c.url IS NULL AND state = 'S'

							GROUP BY c.`idx` HAVING COUNT(*) >= 1
							ORDER BY `credate` DESC
						)AS tb
						GROUP BY tb.idx

						UNION

						SELECT tb.idx, tb.agent_name, tb.branch_name, tb.shop_name,  '0' AS add_sum, '0' AS cnt
						FROM  (
							SELECT s.idx, s.url,  s.agent_idx, s.`shop_name`, s.`ceo_name`, s.`shop_tel`, s.`shop_phone`,
												s.`rgst_date`, s.`call_num`, a.`agent_name`, a.`id`, b.`branch_name`, '0' AS cnt
							FROM shop s 
							INNER JOIN agent a ON s.`agent_idx` = a.`idx`
							INNER JOIN branch b ON a.`br_url` = b.`url`	
						)AS tb
										
						GROUP BY tb.idx
					)AS t1
					GROUP BY t1.idx
					ORDER BY t1.cnt DESC ";
		}else if($auth == 'branch'){
			$qry = "SELECT t1.*
					FROM (
						SELECT tb.idx, tb.agent_name, tb.branch_name, tb.shop_name,  SUM(tb.add_call)AS add_sum, COUNT(*)AS cnt
						FROM (

							SELECT DISTINCT c.add_call, c.credate,
							s.`idx`, s.`shop_name`, s.`position`, s.`shop_tel`, a.`agent_name`, b.`branch_name`

							FROM calldrivertb c
							LEFT JOIN shop s ON c.url = s.url
							LEFT JOIN agent a ON a.idx = s.`agent_idx`
							LEFT JOIN branch b ON a.`br_url` = b.`url`

							WHERE c.url IS NOT NULL AND state = 'S' AND b.id = '$id'
							
							UNION

							SELECT DISTINCT c.add_call, c.credate,
							s.`idx`, s.`shop_name`, s.`position`, s.`shop_tel`, a.`agent_name`, b.`branch_name`

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

							WHERE c.url IS NULL AND state = 'S' AND b.id = '$id'

							GROUP BY c.`idx` HAVING COUNT(*) >= 1
							ORDER BY `credate` DESC
						)AS tb
						GROUP BY tb.idx

						UNION

						SELECT tb.idx, tb.agent_name, tb.branch_name, tb.shop_name,  '0' AS add_sum, '0' AS cnt
						FROM  (
							SELECT s.idx, s.url,  s.agent_idx, s.`shop_name`, s.`ceo_name`, s.`shop_tel`, s.`shop_phone`,
												s.`rgst_date`, s.`call_num`, a.`agent_name`, a.`id`, b.`branch_name`, '0' AS cnt
							FROM shop s 
							INNER JOIN agent a ON s.`agent_idx` = a.`idx`
							INNER JOIN branch b ON a.`br_url` = b.`url` AND b.id = '$id'
						)AS tb
						
						GROUP BY tb.idx
					)AS t1
					GROUP BY t1.idx
					ORDER BY t1.cnt DESC ";
		}else if($auth == 'agent'){
			$qry = "SELECT t1.*
					FROM (
						SELECT tb.idx, tb.agent_name, tb.branch_name, tb.shop_name,  SUM(tb.add_call)AS add_sum, COUNT(*)AS cnt
						FROM (

							SELECT DISTINCT c.add_call, c.credate,
							s.`idx`, s.`shop_name`, s.`position`, s.`shop_tel`, a.`agent_name`, b.`branch_name`

							FROM calldrivertb c
							LEFT JOIN shop s ON c.url = s.url
							LEFT JOIN agent a ON a.idx = s.`agent_idx`
							LEFT JOIN branch b ON a.`br_url` = b.`url`

							WHERE c.url IS NOT NULL AND state = 'S' AND a.id = '$id'
							
							UNION

							SELECT DISTINCT c.add_call, c.credate,
							s.`idx`, s.`shop_name`, s.`position`, s.`shop_tel`, a.`agent_name`, b.`branch_name`

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

							WHERE c.url IS NULL AND state = 'S' AND a.id = '$id'

							GROUP BY c.`idx` HAVING COUNT(*) >= 1
							ORDER BY `credate` DESC
						)AS tb
						GROUP BY tb.idx

						UNION

						SELECT tb.idx, tb.agent_name, tb.branch_name, tb.shop_name,  '0' AS add_sum, '0' AS cnt
						FROM  (
							SELECT s.idx, s.url,  s.agent_idx, s.`shop_name`, s.`ceo_name`, s.`shop_tel`, s.`shop_phone`,
												s.`rgst_date`, s.`call_num`, a.`agent_name`, a.`id`, b.`branch_name`, '0' AS cnt
							FROM shop s 
							INNER JOIN agent a ON s.`agent_idx` = a.`idx` AND a.id = '$id'
							INNER JOIN branch b ON a.`br_url` = b.`url` 
						)AS tb
						
						GROUP BY tb.idx
					)AS t1
					GROUP BY t1.idx
					ORDER BY t1.cnt DESC ";
		}
		
		/* Select queries return a resultset */
		if ($result = mysqli_query($connect, $qry)) {
			//printf("Select returned %d rows.\n", mysqli_num_rows($result));
		}else{
			 echo("Error description ViewList: " . mysqli_error($connect));
		}
		
		return $result;
	}

	function ViewList_search_date($connect, $branch_url, $agent_idx, $shop_idx, $date_before, $date_after){
		
		if ($shop_idx == '0' || $agent_idx == '0'){ // 가맹점 전체

			if($shop_idx == '0' && $agent_idx != '0'){ // 대리점
				
				$qry = "SELECT tb.agent_name, tb.branch_name, tb.shop_name, tb.shop_idx, 
						CAST(tb.credate AS DATE)AS mydate,
						COUNT(IF(tb.url IS NOT NULL,1,NULL))AS s_cnt_mo, 
						COUNT(IF(tb.url IS NULL,1,NULL))AS s_cnt_app, 
						SUM(CASE WHEN tb.url IS NOT NULL THEN tb.add_call ELSE 0  END)AS s_add_sum_mo, 
						SUM(CASE WHEN tb.url IS NULL THEN tb.add_call ELSE 0  END)AS s_add_sum_app

						FROM (

							SELECT DISTINCT c.*, s.`idx` AS shop_idx,
							s.`shop_name`, s.`position`, s.`shop_tel`, a.`agent_name`, b.`branch_name`

							FROM calldrivertb c
							LEFT JOIN shop s ON c.url = s.url
							LEFT JOIN agent a ON a.idx = s.`agent_idx`
							LEFT JOIN branch b ON a.`br_url` = b.`url`

							WHERE c.url IS NOT NULL AND state = 'S' AND a.`idx` = '$agent_idx'
								
							
							UNION

							SELECT DISTINCT c.*, s.`idx` AS shop_idx,
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

							WHERE c.url IS NULL AND state = 'S' AND a.`idx` = '$agent_idx'
								

							GROUP BY c.`idx` HAVING COUNT(*) >= 1
							
						)AS tb
						WHERE  tb.credate BETWEEN '$date_before 00:00:00' AND '$date_after 23:59:59'
						GROUP BY shop_name, mydate
						ORDER BY credate";
			}else if($shop_idx == '0' && $agent_idx == '0'){ // 지사
				
				$qry = "SELECT tb.agent_name, tb.branch_name, tb.shop_name, tb.shop_idx, 
						CAST(tb.credate AS DATE)AS mydate,
						COUNT(IF(tb.url IS NOT NULL,1,NULL))AS s_cnt_mo, 
						COUNT(IF(tb.url IS NULL,1,NULL))AS s_cnt_app, 
						SUM(CASE WHEN tb.url IS NOT NULL THEN tb.add_call ELSE 0  END)AS s_add_sum_mo, 
						SUM(CASE WHEN tb.url IS NULL THEN tb.add_call ELSE 0  END)AS s_add_sum_app

						FROM (

							SELECT DISTINCT c.*, s.`idx` AS shop_idx,
							s.`shop_name`, s.`position`, s.`shop_tel`, a.`agent_name`, b.`branch_name`

							FROM calldrivertb c
							LEFT JOIN shop s ON c.url = s.url
							LEFT JOIN agent a ON a.idx = s.`agent_idx`
							LEFT JOIN branch b ON a.`br_url` = b.`url`

							WHERE c.url IS NOT NULL AND state = 'S' AND b.`url` = '$branch_url'
								
							
							UNION

							SELECT DISTINCT c.*, s.`idx` AS shop_idx,
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

							WHERE c.url IS NULL AND state = 'S' AND b.`url` = '$branch_url'
								

							GROUP BY c.`idx` HAVING COUNT(*) >= 1
							
						)AS tb
						WHERE  tb.credate BETWEEN '$date_before 00:00:00' AND '$date_after 23:59:59'
						GROUP BY shop_name, mydate
						ORDER BY credate";
			}

			
		}else if ($shop_idx != '0' && $agent_idx != '0'){ // 가맹점 선택
		
			$qry = "SELECT tb.*, CAST(tb.credate AS DATE)AS mydate,
				SUM(cnt_mo)AS s_cnt_mo, SUM(add_sum_mo)AS s_add_sum_mo, SUM(cnt_app)AS s_cnt_app, SUM(add_sum_app)AS s_add_sum_app

				FROM (
				SELECT DISTINCT c.add_call, c.credate, c.`price`, c.`state`, c.`url`,
				s.`idx`, s.`shop_name`, a.`agent_name`, b.`branch_name`,
					COUNT(IF(c.url IS NOT NULL,1,NULL))AS cnt_mo,
					SUM(c.add_call)AS add_sum_mo,
					'0' AS cnt_app, '0' AS add_sum_app
				FROM calldrivertb c
				LEFT JOIN shop s ON c.url = s.url
				LEFT JOIN agent a ON a.idx = s.`agent_idx`
				LEFT JOIN branch b ON a.`br_url` = b.`url`

				WHERE c.url IS NOT NULL AND c.state = 'S' AND s.idx = '$shop_idx'
				
				GROUP BY c.`idx` HAVING COUNT(*) >= 1
				)tb

				UNION

				SELECT tb.*, CAST(tb.credate AS DATE)AS mydate, SUM(cnt_mo), SUM(add_sum_mo), SUM(cnt_app), SUM(add_sum_app)
				FROM (
				SELECT DISTINCT c.add_call, c.credate, c.`price`, c.`state`, c.`url`,
				s.`idx`, s.`shop_name`,a.`agent_name`, b.`branch_name`,
					'0' AS cnt_mo, '0' AS add_sum_mo,
					COUNT(IF(c.url IS NULL,1,NULL))AS cnt_app,
					SUM(c.add_call)AS add_sum_app
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

				WHERE c.url IS NULL AND c.state = 'S' AND s.idx = '$shop_idx'
				
				GROUP BY c.`idx` HAVING COUNT(*) >= 1
				)AS tb

				WHERE tb.credate BETWEEN '$date_before 00:00:00' AND '$date_after 23:59:59'
				GROUP BY mydate
				ORDER BY mydate ";
		}

		$data = array();

		if($result = mysqli_query($connect, $qry)){
			//printf("Select returned %d rows.\n", mysqli_num_rows($result));
			
			while($row = mysqli_fetch_assoc($result)){
				
				if( !empty($row) && isset($row['mydate'])){ // NULL값 제외
					$t = new stdClass();
					$t->mydate = $row['mydate'];
					$t->shop_name = $row['shop_name'];
					$t->branch_name = $row['branch_name'];
					$t->agent_name = $row['agent_name'];
					$t->s_cnt_app = $row['s_cnt_app'];
					$t->s_cnt_mo = $row['s_cnt_mo'];
					$t->s_add_sum_app = $row['s_add_sum_app'];
					$t->s_add_sum_mo = $row['s_add_sum_mo'];
				
					$data[] = $t;
					unset($t);
				}
				
			}

		} else {
			echo("Error description ViewList_search_date: " . mysqli_error($connect));
			$data = array( 0 => 'empty');
		}
		
		$json_data = json_encode((array) $data);
		print_r($json_data);
	}
	
	//-----------------------------------------------------------------------------------------------------statsSearch.php
	function StoreInfo($connect, $idx){
		$qry="SELECT tb.`shop_name`, COUNT(IF(tb.state = 'S',1,NULL))AS s_cnt, COUNT(*)AS cnt, 
						MAX(tb.credate) AS max_date,		MIN(tb.credate) AS min_date
				FROM (

					SELECT DISTINCT c.add_call, c.credate, c.`hpno`, c.`current_position`, c.`dst_position`, c.`mid_pass`, c.`price`, c.`state`, c.`url`,
					s.`idx`, s.`shop_name`, s.`position`, s.`shop_tel`, a.`agent_name`, b.`branch_name`

					FROM calldrivertb c
					LEFT JOIN shop s ON c.url = s.url
					LEFT JOIN agent a ON a.idx = s.`agent_idx`
					LEFT JOIN branch b ON a.`br_url` = b.`url`

					WHERE c.url IS NOT NULL
					
					UNION

					SELECT DISTINCT c.add_call, c.credate, c.`hpno`, c.`current_position`, c.`dst_position`, c.`mid_pass`, c.`price`, c.`state`, c.`url`,
					s.`idx`, s.`shop_name`, s.`position`, s.`shop_tel`, a.`agent_name`, b.`branch_name`

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
				)AS tb
				";

			if( $idx == '' || !isset($idx) || empty($idx) )  $qry .= "WHERE tb.idx IS NULL";
			else $qry .= "WHERE tb.idx = '$idx'";

				/* Select queries return a resultset */
		if ($result = mysqli_query($connect, $qry)) {
			//printf("Select returned %d rows.\n", mysqli_num_rows($result));
		}else{
			 echo("Error description ViewList: " . mysqli_error($connect));
		}
		
		return $result;
	}
	
	function ViewList_pop($connect, $idx, $start_from, $num_rec_per_page){
		
		$qry = "SELECT tb.*
				FROM (

					SELECT DISTINCT c.add_call, c.credate, c.`hpno`, c.`current_position`, c.`dst_position`, c.`mid_pass`, c.`price`, c.`state`, c.`url`,
					s.`idx`, s.`shop_name`, s.`position`, s.`shop_tel`, a.`agent_name`, b.`branch_name`

					FROM calldrivertb c
					LEFT JOIN shop s ON c.url = s.url
					LEFT JOIN agent a ON a.idx = s.`agent_idx`
					LEFT JOIN branch b ON a.`br_url` = b.`url`

					WHERE c.url IS NOT NULL
					
					UNION

					SELECT DISTINCT c.add_call, c.credate, c.`hpno`, c.`current_position`, c.`dst_position`, c.`mid_pass`, c.`price`, c.`state`, c.`url`,
					s.`idx`, s.`shop_name`, s.`position`, s.`shop_tel`, a.`agent_name`, b.`branch_name`

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
				)AS tb
				";

		if( $idx == '' || !isset($idx) || empty($idx) )  $qry .="WHERE tb.idx IS NULL";
		else $qry .= "WHERE tb.idx = '$idx'";
				
		$qry .= "
				ORDER BY tb.credate DESC
				LIMIT $start_from, $num_rec_per_page";

				/* Select queries return a resultset */
		if ($result = mysqli_query($connect, $qry)) {
			//printf("Select returned %d rows.\n", mysqli_num_rows($result));
		}else{
			 echo("Error description ViewList_pop: " . mysqli_error($connect));
		}
		
		return $result;
	}
	
	// for pagenation
	function ViewList_count($connect, $idx){
		$qry="SELECT tb.*
				FROM (

					SELECT DISTINCT c.add_call, c.credate, c.`hpno`, c.`current_position`, c.`dst_position`, c.`mid_pass`, c.`price`, c.`state`, c.`url`,
					s.`idx`, s.`shop_name`, s.`position`, s.`shop_tel`, a.`agent_name`, b.`branch_name`

					FROM calldrivertb c
					LEFT JOIN shop s ON c.url = s.url
					LEFT JOIN agent a ON a.idx = s.`agent_idx`
					LEFT JOIN branch b ON a.`br_url` = b.`url`

					WHERE c.url IS NOT NULL
					
					UNION

					SELECT DISTINCT c.add_call, c.credate, c.`hpno`, c.`current_position`, c.`dst_position`, c.`mid_pass`, c.`price`, c.`state`, c.`url`,
					s.`idx`, s.`shop_name`, s.`position`, s.`shop_tel`, a.`agent_name`, b.`branch_name`

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
				)AS tb
				";

			if( $idx == '' || !isset($idx) || empty($idx) )  $qry .= "WHERE tb.idx IS NULL";
			else $qry .= "WHERE tb.idx = '$idx'";
					
			$qry .= "
					ORDER BY tb.credate DESC";

				/* Select queries return a resultset */
		if ($result = mysqli_query($connect, $qry)) {
			//printf("Select returned %d rows.\n", mysqli_num_rows($result));
		}else{
			 echo("Error description ViewList: " . mysqli_error($connect));
		}

		$num_rows = mysqli_num_rows($result);
		
		return $num_rows;
	}

	function ViewList_search($connect, $idx, $date_before, $date_after){
		
		$qry = "SELECT tb.*
				FROM (

					SELECT DISTINCT c.add_call, c.credate, c.`hpno`, c.`current_position`, c.`dst_position`, c.`mid_pass`, c.`price`, c.`state`, c.`url`,
					s.`idx`, s.`shop_name`, s.`position`, s.`shop_tel`, a.`agent_name`, b.`branch_name`

					FROM calldrivertb c
					LEFT JOIN shop s ON c.url = s.url
					LEFT JOIN agent a ON a.idx = s.`agent_idx`
					LEFT JOIN branch b ON a.`br_url` = b.`url`

					WHERE c.url IS NOT NULL AND c.credate BETWEEN '$date_before 00:00:00' AND '$date_after 23:59:59'
					
					UNION

					SELECT DISTINCT c.add_call, c.credate, c.`hpno`, c.`current_position`, c.`dst_position`, c.`mid_pass`, c.`price`, c.`state`, c.`url`,
					s.`idx`, s.`shop_name`, s.`position`, s.`shop_tel`, a.`agent_name`, b.`branch_name`

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

					WHERE c.url IS NULL AND c.credate BETWEEN '$date_before 00:00:00' AND '$date_after 23:59:59'

					GROUP BY c.`idx` HAVING COUNT(*) >= 1
				)AS tb

				WHERE tb.idx = '$idx'
				ORDER BY tb.credate DESC";

				/* Select queries return a resultset */

		$data = array();

		if($result = mysqli_query($connect, $qry)){
			//printf("Select returned %d rows.\n", mysqli_num_rows($result));
			while($row = mysqli_fetch_assoc($result)){
				$t = new stdClass();
				$t->idx = $row['idx'];
				$t->url = $row['url'];
				$t->credate = $row['credate'];
				$t->current_position = $row['current_position'];
				$t->dst_position = $row['dst_position'];
				$t->shop_name = $row['shop_name'];
				$t->branch_name = $row['branch_name'];
				$t->agent_name = $row['agent_name'];
				$t->hpno = $row['hpno'];
				$t->mid_pass = $row['mid_pass'];
				$t->add_call = $row['add_call'];
				$t->price = $row['price'];
				$t->state = $row['state'];
				$t->shop_tel = $row['shop_tel'];
				$data[] = $t;
				unset($t);

			}

		} else {
			echo("Error description: " . mysqli_error($connect));
			$data = array( 0 => 'empty');
		}

		$json_data = json_encode((array) $data);
		print_r($json_data);
	}

	function ViewList_search_state($connect, $idx, $state){
		
		$qry = "SELECT tb.*
				FROM (

					SELECT DISTINCT c.add_call, c.credate, c.`hpno`, c.`current_position`, c.`dst_position`, c.`mid_pass`, c.`price`, c.`state`, c.`url`,
					s.`idx`, s.`shop_name`, s.`position`, s.`shop_tel`, a.`agent_name`, b.`branch_name`

					FROM calldrivertb c
					LEFT JOIN shop s ON c.url = s.url
					LEFT JOIN agent a ON a.idx = s.`agent_idx`
					LEFT JOIN branch b ON a.`br_url` = b.`url`

					WHERE c.url IS NOT NULL AND c.state = 'S'
					
					UNION

					SELECT DISTINCT c.add_call, c.credate, c.`hpno`, c.`current_position`, c.`dst_position`, c.`mid_pass`, c.`price`, c.`state`, c.`url`,
					s.`idx`, s.`shop_name`, s.`position`, s.`shop_tel`, a.`agent_name`, b.`branch_name`

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

					WHERE c.url IS NULL AND c.state = 'S'

					GROUP BY c.`idx` HAVING COUNT(*) >= 1
				)AS tb

				WHERE tb.idx = '$idx'
				ORDER BY tb.credate DESC";

				/* Select queries return a resultset */

		$data = array();

		if($result = mysqli_query($connect, $qry)){
			//printf("Select returned %d rows.\n", mysqli_num_rows($result));
			while($row = mysqli_fetch_assoc($result)){
				$t = new stdClass();
				$t->idx = $row['idx'];
				$t->url = $row['url'];
				$t->credate = $row['credate'];
				$t->current_position = $row['current_position'];
				$t->dst_position = $row['dst_position'];
				$t->shop_name = $row['shop_name'];
				$t->branch_name = $row['branch_name'];
				$t->agent_name = $row['agent_name'];
				$t->hpno = $row['hpno'];
				$t->mid_pass = $row['mid_pass'];
				$t->add_call = $row['add_call'];
				$t->price = $row['price'];
				$t->state = $row['state'];
				$t->shop_tel = $row['shop_tel'];
				$data[] = $t;
				unset($t);

			}

		} else {
			echo("Error description: " . mysqli_error($connect));
			$data = array( 0 => 'empty');
		}

		$json_data = json_encode((array) $data);
		print_r($json_data);
	}
	
?>