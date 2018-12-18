<?php session_start(); ?>
<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/m/data/DB_connect.php';
	/* check connection */
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}

	//날짜별 조회
	if( isset($_POST['date_start']) && !empty($_POST['date_start'])){
		$date_start = $_POST['date_start'];
		$date_end = $_POST['date_end'];
		
		getCallListByDate($connect, $date_start, $date_end);
		
	}
	
	//상태별 조회
	if( isset($_POST['select_status']) && !empty($_POST['select_status']) ){
		$status = $_POST['select_status'];
		
		getCallListByStatus($connect, $status);
	}
	
	//가맹점별 조회
	if( isset($_POST['shop_code']) && !empty($_POST['shop_code'])){
		$code = $_POST['shop_code'];
		
		getCallListByCode($connect, $code);
	}
	
	//가맹점, 주소, 코드 검색
	if( isset($_POST['search_shop_text']) && !empty($_POST['search_shop_text']) ){
		$word = strtolower( $_POST['search_shop_text'] );
		
		searchShopByKeyword($connect, $word);
	}
	
	//가맹점 -> 주소 검색
	if( isset($_POST['shop_name']) && !empty($_POST['shop_name'])){
		$shop_name = $_POST['shop_name'];
		
		searchAddrByName($connect, $shop_name);
	}
	
	//가맹점 추가
	if( isset($_POST['insert_url']) && !empty($_POST['insert_url']) ){
		$url = $_POST['insert_url'];
		$datetime = $_POST['date_time'];
		insertCall($connect, $url, $datetime);
	}
	//상태 변경
	if( isset($_POST['idx_call']) && !empty($_POST['idx_call']) && isset($_POST['status']) && !empty($_POST['status']) ){
		$idx = $_POST['idx_call'];
		$status = $_POST['status'];

		updateCallStatus($connect, $idx, $status);
	}

	//삭제
	if( isset($_POST['idx_del']) && !empty($_POST['idx_del']) ){
		$idx = $_POST['idx_del'];

		deleteCall($connect, $idx);
	}

	function getCallListInit($connect){
		$sql = "SELECT sd.idx, sd.d_url, sd.d_datetime, s.shop_name, s.shop_addr, case when d_status = 1 then '대기' when d_status = 2 then '완료' when d_status = 3 then '취소' END as td_status FROM shop AS s, shop_driver AS sd WHERE s.url = sd.d_url AND SUBSTRING(sd.d_datetime, 1, 10) BETWEEN SUBDATE(CURDATE(), INTERVAL 1 DAY) AND CURDATE() ORDER BY d_datetime DESC";

		$result = mysqli_query($connect, $sql);

		return $result;
	}
	
	function getCallListByDate($connect, $date_start, $date_end){
		$sql = "SELECT sd.idx, sd.d_url, sd.d_datetime, s.shop_name, s.shop_addr, case when d_status = 1 then '대기' when d_status = 2 then '완료' when d_status = 3 then '취소' END as td_status FROM shop AS s, shop_driver AS sd WHERE s.url = sd.d_url AND SUBSTRING(sd.d_datetime, 1, 10) BETWEEN '$date_start' AND '$date_end' ORDER BY d_datetime DESC";
		
		$result = mysqli_query($connect, $sql);
		if(mysqli_affected_rows($connect) == 0){
			echo "<tr class='insert_item history_row'><td colspan='5'> 해당하는 데이터가 없습니다.</tr>";
			return;
		}
		while($row = mysqli_fetch_assoc($result)){
			echo "<tr class='insert_item history_row'>";
			echo "<td><input type='checkbox' class='check_item' value='".$row['idx']."'></td>";
			echo "<td>".$row['shop_name']."</td>";
			echo "<td>".$row['shop_addr']."</td>";
			echo "<td>".$row['d_datetime']."</td>";
			echo "<td>".$row['td_status']."</td>";
			echo "</tr>";
		}
	}
	
	function getCallListByStatus($connect, $status){
		$sql = "SELECT sd.idx, sd.d_url, sd.d_datetime, s.shop_name, s.shop_addr, CASE WHEN d_status = 1 THEN '대기' WHEN d_status = 2 THEN '완료' WHEN d_status = 3 THEN '취소' END AS td_status FROM shop AS s, shop_driver AS sd WHERE s.url = sd.d_url AND sd.d_status = '$status' ORDER BY d_datetime DESC";
		
		$result = mysqli_query($connect, $sql);
		if(mysqli_affected_rows($connect) == 0){
			echo "<tr class='insert_item history_row'><td colspan='5'> 해당하는 데이터가 없습니다.</tr>";
			return;
		}
		while($row = mysqli_fetch_assoc($result)){
			echo "<tr class='insert_item history_row'>";
			echo "<td><input type='checkbox' class='check_item' value='".$row['idx']."'></td>";
			echo "<td>".$row['shop_name']."</td>";
			echo "<td>".$row['shop_addr']."</td>";
			echo "<td>".$row['d_datetime']."</td>";
			echo "<td>".$row['td_status']."</td>";
			echo "</tr>";
		}
	}
	
	function getCallListByCode($connect, $code){
		$sql = "SELECT sd.idx, sd.d_url, sd.d_datetime, s.shop_name, s.shop_addr, CASE WHEN d_status = 1 THEN '대기' WHEN d_status = 2 THEN '완료' WHEN d_status = 3 THEN '취소' END AS td_status FROM shop AS s, shop_driver AS sd WHERE s.url = sd.d_url AND sd.d_url = '$code' ORDER BY d_datetime DESC";
		
		$result = mysqli_query($connect, $sql);
		if(mysqli_affected_rows($connect) == 0){
			echo "<tr class='insert_item history_row'><td colspan='5'> 해당하는 데이터가 없습니다.</tr>";
			return;
		}
		while($row = mysqli_fetch_assoc($result)){
			echo "<tr class='insert_item history_row'>";
			echo "<td><input type='checkbox' class='check_item' value='".$row['idx']."'></td>";
			echo "<td>".$row['shop_name']."</td>";
			echo "<td>".$row['shop_addr']."</td>";
			echo "<td>".$row['d_datetime']."</td>";
			echo "<td>".$row['td_status']."</td>";
			echo "</tr>";
		}
	}
	
	function searchShopByKeyword($connect, $word){
		$sql = "SELECT shop_name, shop_addr, url FROM shop WHERE INSTR(LOWER(shop_name), '$word') > 0 OR INSTR(LOWER(shop_addr), '$word') > 0 OR INSTR(LOWER(url), '$word') > 0";
		
		$result = mysqli_query($connect, $sql);
		
		while($row = mysqli_fetch_assoc($result)){
			echo "<tr class='insert_item searchRs_row'><td>".$row['shop_name']."</td><td>".$row['shop_addr']."</td><td>".$row['url']."</td></tr>";
		}
	}
	
	function searchAddrByName($connect, $shop_name){
		$sql = "SELECT shop_name, url, shop_addr FROM shop WHERE INSTR(shop_name, '$shop_name')";
		
		$result = mysqli_query($connect, $sql);
		if(mysqli_affected_rows($connect) == 0){
			echo "<tr class='insert_item searchRs_row'><td colspan='3'>해당하는 데이터가 없습니다.</td></tr>";
			return;
		}
		while($row = mysqli_fetch_assoc($result)){
			echo "<tr class='insert_item searchRs_row'>";
			echo "<td>".$row['shop_name']."</td>";
			echo "<td><input type='radio' name='search_shop_radio' value='".$row['url']."'>".$row['shop_addr']."</td>";
			echo "<td>".$row['url']."</td>";
			echo "</tr>";
		}
	}
	function updateCallStatus($connect, $idx, $status){//항목 상태 변경하기(대기: 1, 완료: 2, 취소: 3)
		$sql = "UPDATE shop_driver SET d_status = '$status' WHERE idx = '$idx'";
		mysqli_query($connect, $sql);
	}

	function insertCall($connect, $url, $datetime){//항목 추가(완료 상태로 추가)
		$sql = "INSERT INTO shop_driver (d_url, d_status, d_datetime) VALUES ('$url', '2', '$datetime')";
		mysqli_query($connect, $sql);
	}
	function deleteCall($connect, $idx){//항목 삭제하기
		$sql = "DELETE FROM shop_driver WHERE idx = '$idx'";
		mysqli_query($connect, $sql);
	}
?>
