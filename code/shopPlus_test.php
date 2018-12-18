<?php session_start(); ?>
<?php
include_once $_SERVER[DOCUMENT_ROOT].'/m/data/DB_connect.php';

//error_reporting(E_ALL);
//ini_set("display_errors", 1);


	//$connect = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	
	/* check connection */
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}

	// 지사 선택 후 대리점 선택하는 로직
	if(isset($_POST['branch_url']) && !empty($_POST['branch_url'])){
		$branch_url = $_POST['branch_url'];
		$id = $_POST['agent_id'];
		//echo "success";
		getAgent($connect,$branch_url, $id);
	}else{
		//echo "fail";
	}

	//가맹점 등록
	if(isset($_POST['rgst_name']) && !empty($_POST['rgst_name']) ){
		$shop_type = $_POST['shop_type'];
		//echo "success";
		registerShop($connect, $shop_type);
	}else{
		//echo "fail";
	}

	//가맹점 검색
	if(isset($_POST['search_type']) && !empty($_POST['search_type']) ){
		$search_type = $_POST['search_type'];
		$search_text = $_POST['search_text'];
		$id = $_POST['search_id'];
		$auth = $_POST['search_auth'];
		//echo "success";
		searchShop($connect, $search_type, $search_text, $auth, $id);
	}else{
		//echo "fail";
	}

	//가맹점 삭제
	if(isset($_POST['del_idx']) && !empty($_POST['del_idx'])){
		$idx = $_POST['del_idx'];
		deleteData_pop($connect, $idx);
	}else{
		//echo "fail";
	}

	//가맹점 수정
	if(isset($_POST['edit_idx']) && !empty($_POST['edit_idx'])){
		$idx = $_POST['edit_idx'];
		
		editShop($connect, $idx);
	}else{
		//echo "fail";
	}

	//가맹점 수정 > 지사 이름 가져오기
	if(isset($_POST['get_branch_edit']) && !empty($_POST['get_branch_edit'])){
		$get_branch_edit = $_POST['get_branch_edit'];
		$id =  $_POST['user_id'];

		if($get_branch_edit == "1"){
			$qry = "SELECT  u.auth
					FROM USER u WHERE id = '$id'  ";

			/* Select queries return a resultset */
			if ($result = mysqli_query($connect, $qry)) {
				//printf("Select returned %d rows.\n", mysqli_num_rows($result));
				$auth = mysqli_fetch_object($result);
				
				$auth_result = $auth->auth;

				if($auth_result == "manager"){
					$qry2 = "SELECT b.`id`, b.`branch_name`, b.`url`
							FROM branch b";

					if ($result2 = mysqli_query($connect, $qry2)) {
						$num_rows = mysqli_num_rows($result2);
						$br_name = "";
						$chk_rows = 0;

						while ($row = mysqli_fetch_assoc($result2)) {
							$chk_rows++;
							$br_name = $br_name.$row['branch_name']."/".$row['idx']."/".$row['url'];

							if($chk_rows != $num_rows){
								$br_name = $br_name.",";
							}
							
						}

						echo $br_name;

					}else {
						echo("Error description get branch for manager: " . mysqli_error($connect));
					}
				}// 관리자만 지사 변경 가능
				else if ($auth == "branch" or $auth == "agent"){ echo "NoPermission"; }

			}else{
				echo("Error description get branch: " . mysqli_error($connect));
			}

			
		}
	}

	//가맹점 수정 > 대리점 이름 가져오기
	if(isset($_POST['get_agent']) && !empty($_POST['get_agent'])){
		$ag_name = $_POST['get_branch'];
		if($ag_name == "1"){
			getAgent($connect,$branch_name);
		}
	}else{
		//echo "fail";
	}

	//가맹점 수정 > 메뉴 삭제
	if(isset($_POST['del_price_idx']) && !empty($_POST['del_price_idx'])){
		$shop_idx = $_POST['del_price_idx'];
		$price_type = $_POST['del_price_type'];
		//echo "success";
		deletePrice($connect, $shop_idx, $price_type);
	}

	//가맹점 수정 > 사진 삭제 - 전체
	if(isset($_POST['del_photo_all_idx']) && !empty($_POST['del_photo_all_idx'])){
		$idx = $_POST['del_photo_all_idx'];
		
		deletePhoto($connect, $idx);
	}

	//가맹점 수정 > 사진 삭제 - 선택
	if(isset($_POST['del_photo_idx']) && !empty($_POST['del_photo_idx'])){
		$idx = $_POST['del_photo_idx'];
		$del_idx = array();
		$del_idx = $_POST['photo_idx'];

		//print_r($del_idx);

		deletePhoto_select($connect, $idx, $del_idx);
	}


	//가맹점 수정 > 가격 등록
	if(isset($_POST['insert_price']) && !empty($_POST['insert_price'])){
		$idx = $_POST['shop_menu_idx'];
		$price_type = $_POST['price_type'];
		$price_type2 = $_POST['price_type2'];
		$price = $_POST['price'];
		$info = $_POST['info'];
		$name = $_POST['name'];
		//print_r($_POST);
		insertPrice($connect, $idx, $price_type, $price_type2, $name, $price, $info);
	}
	function insertPrice($connect, $idx, $price_type, $price_type2, $name, $price, $info){
		
		$success = count($name)*count($price_type2);

		//echo $success;

		for($j = 0; $j < count($name); $j ++){
			for($i = 0; $i < count($price_type2); $i ++){
				//echo $price_type2[$i]."/". $name[$j]." : ".$price[$i+(count($price_type2)*$j)]."(".$info[$i+(count($price_type2)*$j)]."\n";
				
				$qry = "INSERT INTO  `jnfc`.`plus_price` (
						`idx` ,
						`shop_idx`,
						`price_type`,
						`price_type2`,
						`name`,
						`price`,
						`info` 

						)
						VALUES ( NULL, ' $idx', '$price_type', '".$price_type2[$i]."', '".$name[$j]."', 
								'".$price[$i+(count($price_type2)*$j)]."', '".$info[$i+(count($price_type2)*$j)]."'
						); ";
						
				if ($connect->query($qry) === TRUE) {
					$success--;
				}
			}
			//echo "\n";
		}

		if($success == 0){
			echo "success";
		}else { echo "fail"; }
	}

	//가맹점 수정 > 가격 수정
	if(isset($_POST['edit_price']) && !empty($_POST['edit_price'])){
		$idx = $_POST['shop_menu_idx'];
		$price_type = $_POST['price_type'];
		$price_type2 = $_POST['price_type2'];
		$price = $_POST['price'];
		$info = $_POST['info'];
		$name = $_POST['name'];
		$price_idx = $_POST['idx'];
		editPrice($connect, $idx, $price_type, $price_type2, $name, $price, $info, $price_idx);
	}
	function editPrice($connect, $idx, $price_type, $price_type2, $name, $price, $info, $price_idx){
		
		$success = count($name)*count($price_type2);

		//echo $success;

		for($j = 0; $j < count($name); $j ++){
			for($i = 0; $i < count($price_type2); $i ++){
				//echo $price_type2[$i]."/". $name[$j]." : ".$price[$i+(count($price_type2)*$j)]."(".$info[$i+(count($price_type2)*$j)]."\n";
				$qry = "UPDATE `jnfc`.`plus_price` 
						SET
						`price` = '".$price[$i+(count($price_type2)*$j)]."',
						`info` = '".$info[$i+(count($price_type2)*$j)]."'

						WHERE  `idx` = '".$price_idx[$i+(count($price_type2)*$j)]."'";
		
				if ($connect->query($qry) === TRUE) {
					$success--;
				}
			}
			//echo "\n";
		}

		if($success == 0){
			echo "success";
		}else { echo "fail"; }
	}

	
	function ViewList($connect, $auth, $id){
	
		if($auth == "manager"){
		
			$qry = "SELECT s.*, a.agent_name, b.branch_name, a.id FROM shop_plus s
					JOIN agent a ON s.`agent_idx` = a.`idx`
					JOIN branch b ON a.`br_url` = b.`url`
					WHERE s.shop_type = '1'
					ORDER BY s.rgst_date DESC";
		}
		else if($auth == "branch"){
		
			$qry = "SELECT s.*, a.agent_name, b.branch_name, a.id FROM shop_plus s
					JOIN agent a ON s.`agent_idx` = a.`idx`
					JOIN branch b ON a.`br_url` = b.`url`
					WHERE s.shop_type = '1'  AND b.`id` = '$id'
					ORDER BY s.rgst_date DESC";
		}
		else if($auth == "agent"){
		
			$qry = "SELECT s.*, a.agent_name, b.branch_name, a.id FROM shop_plus s 
					JOIN agent a ON s.`agent_idx` = a.`idx`
					JOIN branch b ON a.`br_url` = b.`url`
					WHERE s.shop_type = '1'  AND a.`id` = '$id'
					ORDER BY s.rgst_date DESC";
		}

		/* Select queries return a resultset */
		if ($result = mysqli_query($connect, $qry)) {
			//printf("Select returned %d rows.\n", mysqli_num_rows($result));
		}else{
			 echo("Error description ViewList: " . mysqli_error($connect));
		}
		
		return $result;
	}

	function ViewList_pop($connect, $idx){
		
		$qry = "SELECT s.*, a.`agent_name`, b.`branch_name`, a.`br_url`
				FROM  shop_plus s 
				JOIN agent a ON s.agent_idx = a.idx
				JOIN branch b ON b.`url` = a.br_url
				WHERE  s.`idx` = '$idx' ";
		
		/* Select queries return a resultset */
		if ($result = mysqli_query($connect, $qry)) {
			//printf("Select returned %d rows.\n", mysqli_num_rows($result));
		}else{
			echo("Error description ViewList_pop: " . mysqli_error($connect));
		}
		
		return $result;
	}
	
	function ViewList_photo($connect,$idx){
		$qry = "SELECT * FROM plus_photo WHERE shop_idx = '$idx' ";
		
		/* Select queries return a resultset */
		if ($result = mysqli_query($connect, $qry)) {
			//printf("Select returned %d rows.\n", mysqli_num_rows($result));
		}else{
			echo("Error description ViewList_pop: " . mysqli_error($connect));
		}
		
		return $result;
	}

	function deleteData_pop($connect, $idx){
		$qry = "SELECT photo_url 
			FROM shop_plus 
			WHERE idx = '$idx'";

		if ($result = mysqli_query($connect, $qry)) {
			
			$value = mysqli_fetch_object($result);

			$unlink_name = $value->photo_url;

			if(isset($unlink_name) && !empty($unlink_name)){
				if( strcmp( $unlink_name, 'upload/nostorephoto.jpg' ) == 0 ){
				}else if( strcmp( $unlink_name, 'upload/nostorephoto.jpg' ) != 0 ){
					unlink($unlink_name);
				}
			}
		}
		
		$qry2 = "DELETE FROM `shop_plus` WHERE idx = '$idx'";
		
		/* Select queries return a resultset */
		if ($result = mysqli_query($connect, $qry2)) {
			//printf("Select returned %d rows.\n", mysqli_num_rows($result));
			

			echo "가맹점 삭제를 완료하였습니다.";
		}else{
			 echo("Error description: " . mysqli_error($connect));
		}
		
	}

	function deletePhoto($connect, $idx){
		$qry = "SELECT * FROM plus_photo WHERE shop_idx = '$idx'";
		
		$row_nums = mysqli_num_rows($result);
		$success = 0;

		if ($result = mysqli_query($connect, $qry)) {
		
			while ($value = mysqli_fetch_assoc($result)) {	
			
				$unlink_name = $value['photo_url'];

				if(isset($unlink_name) && !empty($unlink_name)){
					unlink($unlink_name);
					$success++;
				}
			}
		}
		
		$qry2 = "DELETE FROM `plus_photo` WHERE shop_idx = '$idx'";
		
		/* Select queries return a resultset */
		if ($result = mysqli_query($connect, $qry2)) {
			//printf("Select returned %d rows.\n", mysqli_num_rows($result));
			echo $success."개의 사진을 삭제하였습니다.";
		}else{
			 echo("Error description: " . mysqli_error($connect));
		}
		
	}
	function deletePhoto_select($connect, $idx, $del_idx){
		
		$cnt = count($del_idx);
		
		//print_r($del_idx);
		//echo $cnt;

		$success = 0;
		$success2 = 0;

		for($i = 0; $i < $cnt ; $i++){
			$qry = "SELECT * FROM plus_photo WHERE shop_idx = '$idx' AND idx = '".$del_idx[$i]."'";
		
			$row_nums = mysqli_num_rows($result);

			if ($result = mysqli_query($connect, $qry)) {
			
				while ($value = mysqli_fetch_assoc($result)) {	
				
					$unlink_name = $value['photo_url'];

					if(isset($unlink_name) && !empty($unlink_name)){
						unlink($unlink_name);
						$success++;
					}
				}
			}
			
			$qry2 = "DELETE FROM `plus_photo` WHERE shop_idx = '$idx' AND idx = '".$del_idx[$i]."'";
			
			/* Select queries return a resultset */
			if ($result = mysqli_query($connect, $qry2)) {
				$success2++;
			}
		}

		if($success == $success2){
			echo "삭제가 완료되었습니다.";
		}else echo "일부 사진의 삭제에 실패하였습니다.";
		
	}
	
	function getPrice_type($connect, $idx){
		
		$qry = "SELECT p.*
				FROM  shop_plus s 
				JOIN  plus_price p ON s.`idx` = p.`shop_idx`
				WHERE  s.`idx` = '$idx' AND price_type2 IS NOT NULL
				GROUP BY price_type";
		
		/* Select queries return a resultset */
		if ($result = mysqli_query($connect, $qry)) {
			//printf("Select returned %d rows.\n", mysqli_num_rows($result));
		}else{
			echo("Error description getMenu: " . mysqli_error($connect));
		}
		
		return $result;
	}

	function getPrice($connect, $idx, $type){
		
		$qry = "SELECT p.*
				FROM  shop_plus s 
				JOIN  plus_price p ON s.`idx` = p.`shop_idx`
				WHERE  s.`idx` = '$idx' AND price_type = '$type'";
		
		/* Select queries return a resultset */
		if ($result = mysqli_query($connect, $qry)) {
			//printf("Select returned %d rows.\n", mysqli_num_rows($result));
		}else{
			echo("Error description getMenu: " . mysqli_error($connect));
		}
		
		return $result;
	}

	function getPrice_num($connect, $idx, $type){
		
		$qry = "SELECT p.*, COUNT(NAME) AS name_num
				FROM  shop_plus s 
				JOIN  plus_price p ON s.`idx` = p.`shop_idx`
				WHERE  s.`idx` = '$idx' AND price_type2 IS NOT NULL AND price_type = '$type'
				GROUP BY price_type2
				ORDER BY idx";
		
		/* Select queries return a resultset */
		if ($result = mysqli_query($connect, $qry)) {
			//printf("Select returned %d rows.\n", mysqli_num_rows($result));
		}else{
			echo("Error description getMenu: " . mysqli_error($connect));
		}
		
		return $result;
	}

	function deletePrice($connect, $shop_idx, $price_type){

		$qry2 = "DELETE FROM `plus_price` WHERE shop_idx = '$shop_idx' AND price_type = '$price_type'";

		/* Select queries return a resultset */
		if ($result = mysqli_query($connect, $qry2)) {
			echo "success";
		}else{
			 echo("Error description deletePrice: " . mysqli_error($connect));
		}
		
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

	function getAgent_pop($connect, $branch_url, $id){
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

		return $result;
	
	}

	function makeURL($connect, $agent_idx){
		$url_qry = "SELECT a.`url`, a.`br_url`
					FROM agent a
					WHERE  a.`idx` = '$agent_idx' ";

		$url_result = mysqli_query($connect, $url_qry);

		while ($row = mysqli_fetch_assoc($url_result)) {
			$br_url = $row['br_url'];
			$ag_url = $row['url'];
		}
		
		$count_qry = "SELECT COUNT(*) AS cnt
						FROM shop_plus
						WHERE agent_idx = '$agent_idx'
						GROUP BY agent_idx";

		$count_result = mysqli_query($connect, $count_qry);

		while ($row = mysqli_fetch_assoc($count_result)) {
			$cnt = $row['cnt'];
		}

		$make_url = 'b_'.$br_url.$ag_url; // 숙박업소 전용 url : p=b_braga1...
		
		$cnt++; //대리점 갯수 +1

		return $make_url.",".$cnt;
	}

	function checkURL($connect, $check_url, $cnt){
		
		$shop_url =  $check_url.$cnt;

		$qry = "SELECT url FROM shop_plus WHERE url = '$shop_url'";
		
		$result = mysqli_query($connect, $qry);
		$num_chk_url = mysqli_num_rows($result);
		
		return $num_chk_url;
	}
	
	function registerShop($connect, $shop_type){
		
		$shop_name = $_POST['rgst_name'];
		$agent_idx = $_POST['agent'];
		$ceo_name = $_POST['ceo_name'];
		$shop_tel = $_POST['shop_tel'];

		// 가맹점 고유 url 생성 코드
		// makeURL에서 [ 지사url+대리점url, 해당대리점에 소속된 가맹점 수 ] 리턴
		$make_url = makeURL($connect, $agent_idx);
		list($check_url, $cnt) = split(',', $make_url);
		
		do{
			// url 중복체크 
			$num_chk_url = checkURL($connect, $check_url, $cnt);

			// 중복된 url이 없다면 체크된 url을 저장
			// 중복된 url이 있다면 cnt 늘리고 반복
			if($num_chk_url == 0){
				$shop_url = $check_url.$cnt; //shop_url 결정
				break;
			}else {
				$cnt++;
			}
			
		}while($num_chk_url >= 1);
				
		$qry = "INSERT INTO  `jnfc`.`shop_plus` (
			`idx` ,
			`shop_type`,
			`url` ,
			`shop_name` ,
			`agent_idx` ,

			`position` ,
			`ceo_name` ,
			`shop_tel` ,

			`rgst_date` ,
			`photo_url` ,
			`info` ,
			`intro_text` 

			)
			VALUES (
			NULL , '$shop_type', '$shop_url', '$shop_name', '$agent_idx',
			'', '$ceo_name',  '$shop_tel', 
			CURRENT_TIMESTAMP , 'upload/nostorephoto.jpg',  '',  ''
			); ";

		if ($connect->query($qry) === TRUE) {
			//echo "success";
			
			$qry2 = "SELECT s.idx, s.url, s.agent_idx, s.`shop_name`, s.`ceo_name`, s.`shop_tel`,
						s.`rgst_date`, a.`agent_name`, b.`branch_name`, a.`id`

					FROM  shop_plus s 
					INNER JOIN agent a ON s.agent_idx = a.idx
					INNER JOIN branch b ON a.`br_url` = b.`url`
					WHERE s.`shop_name` = '$shop_name' AND s.`shop_tel` = '$shop_tel'";
			
			$data = array();

			if($result = mysqli_query($connect, $qry2)){
				//printf("Select returned %d rows.\n", mysqli_num_rows($result));
				while($row = mysqli_fetch_assoc($result)){
					$t = new stdClass();
					$t->idx = $row['idx'];
					$t->url = $row['url'];
					$t->shop_name = $row['shop_name'];
					$t->branch_name = $row['branch_name'];
					$t->agent_name = $row['agent_name'];
					$t->agent_idx = $row['agent_idx'];
					$t->ceo_name = $row['ceo_name'];
					$t->id = $row['id'];
					$t->shop_tel = $row['shop_tel'];
					$t->rgst_date = $row['rgst_date'];
					$data[] = $t;
					unset($t);

				}

			} else {
				echo("Error description registerShop : " . mysqli_error($connect));
				$data = array( 0 => 'empty');
			}

			$json_data = json_encode((array) $data);
			print_r($json_data);

		} else {
			echo "Error: " . $qry . "<br>" . $connect->error;
		}
			
	}

	function editShop($connect, $idx){
		
		$shop_name = $_POST['shop_name'];
		$url = $_POST['url'];
		$agent_idx = $_POST['agent_name_edit'];
		$ceo_name = $_POST['ceo_name'];
		$shop_tel = $_POST['shop_tel'];
		$position = $_POST['position'];
		$rgst_date = $_POST['rgst_date'];
		$info = $_POST['info'];
		$intro_text = $_POST['intro_text'];
		
		$time_stay = $_POST['time_stay'];
		$time_stay_info = $_POST['time_stay_info'];
		$day_stay = $_POST['day_stay'];

		$success = 0;



		$qry = "UPDATE `jnfc`.`shop_plus` 
		SET
		`shop_name` = '$shop_name',
		`url` = '$url',
		`agent_idx` = '$agent_idx',
		`ceo_name` = '$ceo_name',
		`shop_tel` = '$shop_tel',
		`position` = '$position',
		`rgst_date` = '$rgst_date',
		`info` = '$info',
		`intro_text` = '$intro_text'

		WHERE  `idx` = '$idx'";

		if ($connect->query($qry) === TRUE) {
			$success++;

			$qry = "SELECT * FROM plus_price WHERE shop_idx = '$idx' AND price_type = '0' AND NAME='time'";
			if ($result = mysqli_query($connect, $qry)) {
				$num_rows = mysqli_num_rows($result);
				if($num_rows > 0){
					$qry = "UPDATE jnfc.`plus_price`
							SET 
							`price` = '$time_stay',
							`info` = '$time_stay_info'


							WHERE shop_idx =  '$idx' AND price_type = '0' AND NAME='time'";
					if ($connect->query($qry) === TRUE) {
						$success++;
					}
				}else if($num_rows == 0){
					$qry = "INSERT INTO  `jnfc`.`plus_price` (
						`idx` ,
						`shop_idx`,
						`price_type`,
						`price_type2`,
						`name`,
						`price`,
						`info` 

						)
						VALUES ( NULL, ' $idx', '0', NULL, 'time', 
								'$time_stay', '$time_stay_info'
						); ";
						
					if ($connect->query($qry) === TRUE) {
						$success++;
					}
				}
			}

			$qry = "SELECT * FROM plus_price WHERE shop_idx = '$idx' AND price_type = '0' AND NAME='day'";
			if ($result = mysqli_query($connect, $qry)) {
				$num_rows = mysqli_num_rows($result);
				if($num_rows > 0){
					$qry = "UPDATE jnfc.`plus_price`
							SET 
							`price` = '$time_stay',
							`info` = '$time_stay_info'


							WHERE shop_idx = '$idx' AND price_type = '0' AND NAME='day'";
					if ($connect->query($qry) === TRUE) {
						$success++;
					}
				}else if($num_rows == 0){
					$qry = "INSERT INTO  `jnfc`.`plus_price` (
						`idx` ,
						`shop_idx`,
						`price_type`,
						`price_type2`,
						`name`,
						`price`,
						`info` 

						)
						VALUES ( NULL, ' $idx', '0', NULL, 'day', 
								'$day_stay', NULL
						); ";
						
					if ($connect->query($qry) === TRUE) {
						$success++;
					}
				}
			}

		} else {
			echo "Error editShop: " . $qry . "<br>" . $connect->error;
		}

		if($success == 3){
			echo "success";
		}
		
	}

	function searchShop($connect, $search_type, $search_text, $auth, $id){
		
		if($auth == 'manager'){
			$qry = "";
		
		}
		else if($auth == 'branch'){
			$qry = "";
		
		}
		else if($auth == 'agent'){
			$qry = "";
		
		}
		
		
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
				$t->shop_name = $row['shop_name'];
				$t->branch_name = $row['branch_name'];
				$t->agent_name = $row['agent_name'];
				$t->agent_idx = $row['agent_idx'];
				$t->ceo_name = $row['ceo_name'];
				$t->id = $row['id'];
				$t->shop_tel = $row['shop_tel'];
				$t->position = $row['position'];
				$t->rgst_date = $row['shop_date'];
				$t->cnt = $row['cnt'];
				$data[] = $t;
				unset($t);
			}

			$json_data = json_encode((array) $data);
			print_r($json_data);
		}
		
	}

	function searchShop_re($connect, $search_type, $search_text, $auth, $id){
		
		if($auth == 'manager'){
			$qry = "";
		
		}
		else if($auth == 'branch'){
			$qry = "";
		
		}
		else if($auth == 'agent'){
			$qry = "";
		
		}
		
		
		/* Select queries return a resultset */
		if ($result = mysqli_query($connect, $qry)) {
			//printf("Select returned %d rows.\n", mysqli_num_rows($result));
			$num_rows = mysqli_num_rows($result);
		}else{
			 echo("Error description searchShop: " . mysqli_error($connect));
		}

		if ($num_rows == 0) echo "0";
		else {
			return $result;
		}
		
	}

?>