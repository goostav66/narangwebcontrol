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
	
	// 호출 상태 변경 코드
	if(isset($_POST['change_idx']) && !empty($_POST['change_idx'])){
		$idx = $_POST['change_idx'];
		$item_name = $_POST['item_name'];
		$value = $_POST['value'];
		
		editItem($connect, $item_name, $value, $idx);
	}

	// FOR PAGENATION
	function ViewList($connect, $auth, $id, $start_from, $num_rec_per_page){
	
		if($auth == "manager"){
		
			$qry = "SELECT r.*, a.`agent_name`, b.`branch_name`
					FROM reserve r 

					LEFT JOIN shop s ON r.url = s.url
					LEFT JOIN agent a ON a.idx = s.`agent_idx`
					LEFT JOIN branch b ON a.`br_url` = b.`url`

					GROUP BY r.idx HAVING COUNT(*) >= 1
					ORDER BY r.idx DESC
					LIMIT $start_from, $num_rec_per_page";
		}
		else if($auth == "branch"){
		
			$qry = "SELECT r.*, a.`agent_name`, b.`branch_name`
					FROM reserve r 

					LEFT JOIN shop s ON r.url = s.url
					LEFT JOIN agent a ON a.idx = s.`agent_idx`
					LEFT JOIN branch b ON a.`br_url` = b.`url`

					WHERE b.id = '$id'

					GROUP BY r.idx HAVING COUNT(*) >= 1
					ORDER BY r.idx DESC
					LIMIT $start_from, $num_rec_per_page";
		}
		else if($auth == "agent"){
		
			$qry = "SELECT r.*, a.`agent_name`, b.`branch_name`
					FROM reserve r 

					LEFT JOIN shop s ON r.url = s.url
					LEFT JOIN agent a ON a.idx = s.`agent_idx`
					LEFT JOIN branch b ON a.`br_url` = b.`url`

					WHERE a.id = '$id'

					GROUP BY r.idx HAVING COUNT(*) >= 1
					ORDER BY r.idx DESC
					LIMIT $start_from, $num_rec_per_page";
		}

		/* Select queries return a resultset */
		if ($result = mysqli_query($connect, $qry)) {
			//printf("Select returned %d rows.\n", mysqli_num_rows($result));
		}else{
			 echo("Error description ViewList: " . mysqli_error($connect));
		}
		
		return $result;
	}

	function _ViewListCount($connect,  $auth, $id){

		if($auth == 'manager' or $auth == 'center'){

			$qry = "SELECT r.*, a.`agent_name`, b.`branch_name`
					FROM reserve r 

					LEFT JOIN shop s ON r.url = s.url
					LEFT JOIN agent a ON a.idx = s.`agent_idx`
					LEFT JOIN branch b ON a.`br_url` = b.`url`

					GROUP BY r.idx HAVING COUNT(*) >= 1
					ORDER BY r.idx DESC";
		}

		else if ($auth == 'branch'){

			$qry = "SELECT r.*, a.`agent_name`, b.`branch_name`
					FROM reserve r 

					LEFT JOIN shop s ON r.url = s.url
					LEFT JOIN agent a ON a.idx = s.`agent_idx`
					LEFT JOIN branch b ON a.`br_url` = b.`url`

					WHERE b.id = '$id'

					GROUP BY r.idx HAVING COUNT(*) >= 1
					ORDER BY r.idx DESC";
		
		}
		else if ($auth == 'agent'){

			$qry = "SELECT r.*, a.`agent_name`, b.`branch_name`
					FROM reserve r 

					LEFT JOIN shop s ON r.url = s.url
					LEFT JOIN agent a ON a.idx = s.`agent_idx`
					LEFT JOIN branch b ON a.`br_url` = b.`url`

					WHERE a.id = '$id'

					GROUP BY r.idx HAVING COUNT(*) >= 1
					ORDER BY r.idx DESC";
		
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
	
	function editItem($connect, $item_name, $value, $idx){
		
		$qry = "UPDATE reserve
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
	
	//검색 기능 개발예정
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