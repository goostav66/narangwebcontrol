<?php session_start(); ?>
<?php
	include_once $_SERVER['DOCUMENT_ROOT'].'/m/data/DB_connect.php';
	
	//파일 업로드를 위해 메모리처리량 무제한으로 설정
	ini_set('memory_limit','-1');
	
	/* check connection */
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}
		
	# 광고업소 - 광고 삭제
	if( isset($_POST['incommerce_delete_idx']) && !empty($_POST['incommerce_delete_idx']) ){
		$sc_idx = $_POST['incommerce_delete_idx'];
		deleteIncommerce($connect, $sc_idx);
	}
	
	# 광고업소 - 날짜순 보기
	if( isset($_POST['incommerce_date']) && !empty($_POST['incommerce_date']) ){
		$date = $_POST['incommerce_date'];
		$order = $_POST['incommerce_order'];
		
		if( $_POST['location_code'] != null ){
			$location_code = (int) $_POST['location_code'];
			getEqualLocationCommerce($connect, $location_code, $date, $order);
		}
		else
			getCommerceList($connect, $date, $order);
	}
	

	# 광고업소 신규등록 - 등록하기
	if( isset($_POST['submit_incommerce_url']) && !empty($_POST['submit_incommerce_url'])){
		$url = $_POST['submit_incommerce_url'];
		registShopIncommerce($connect, $url);
	}
	
	# 광고업소 - 수정
	if( isset($_POST['submit_incommerce_idx']) && !empty($_POST['submit_incommerce_idx']) ){
		$sc_idx = (int) $_POST['submit_incommerce_idx'];
		modifyIncommerce($connect, $sc_idx);
	}
	

	
	# 광고업소 신규등록 - 업소 찾기
	if( isset($_POST['search_shop_text']) && !empty($_POST['search_shop_text'])){
		$search_word = $_POST['search_shop_text'];
		searchShop($connect, $search_word);
	}
	
	# 광고업소 - 지역 구/군 목록 가져오기
	if( isset($_POST['selected_city_location_code']) && !empty($_POST['selected_city_location_code']) ){
		$location_code = $_POST['selected_city_location_code'];
		getLocationDistList($connect, $location_code);
	}
	
	# 광고업소 - 업소코드로 지역코드, 구/군 찾기
	if( isset($_POST['selected_shop_url']) && !empty($_POST['selected_shop_url']) ){
		$url = $_POST['selected_shop_url'];
		getLocationCodeByURL($connect, $url);
	}
	
	# 광고업소 - 지역코드로 지역명 가져오기
	if( isset($_POST['selected_location_code']) && !empty($_POST['selected_location_code']) ){
		$location_code = $_POST['selected_location_code'];
		getLocationPlaceByCode($connect, $location_code);
	}
	
	# 외부업체 - 수정 
	if( isset($_POST['submit_excommerce_idx']) && !empty($_POST['submit_excommerce_idx']) ){
		$e_idx = (int)$_POST['submit_excommerce_idx'];
		modifyExcommerce($connect, $e_idx);
	}
	# 외부업체 - 삭제
	if( isset($_POST['excommerce_delete_idx']) && !empty($_POST['excommerce_delete_idx']) ){
		$e_idx = (int)$_POST['excommerce_delete_idx'];
		deleteExcommerce($connect, $e_idx);

	}
	# 외부업체 신규등록 - 등록하기
	if( isset($_POST['excommerce_enterprise']) && !empty($_POST['excommerce_enterprise']) ){
		$e_enterprise = $_POST['excommerce_enterprise'];
		registEnterExcommerce($connect, $e_enterprise);
	}
	
	# 외부업체 - 날짜순 보기
	if( isset($_POST['excommerce_date']) && !empty($_POST['excommerce_date']) ){
		$date = $_POST['excommerce_date'];
		$order = $_POST['excommerce_order'];
		
		getExCommerceList($connect, $date, $order);
	}

	# 외부업체 - 조건부 조회
	if( isset($_POST['selected_location_code_ex']) && !empty($_POST['selected_location_code_ex']) ){
		$location_code = $_POST['selected_location_code_ex'];
		$e_type = $_POST['e_type'];
		$regdate = $_POST['date'];
		$order = $_POST['order'];

		getExcommerceListConditional($connect, $e_type, $regdate, $order, $location_code);
	} 
	# 외부업체 - 검색
	if( isset($_POST['search_excommerce_column']) && !empty($_POST['search_excommerce_column']) ){
		$column = $_POST['search_excommerce_column'];
		$text = $_POST['search_excommerce_text'];

		searchExcommerce($connect, $column, $text);
	}

	# 광고업소 - 광고 등록한 가맹점 가져오기(전 지역)
	function getCommerceList($connect, $date, $order){
		$sql = "SELECT sc.idx, s.shop_name, s.location_code, sc.url, tlc.location_place, s.shop_addr, sc.regdate, sc.expdate 
				FROM shop_commerce AS sc 
				LEFT JOIN shop AS s ON s.url = sc.url 
				LEFT JOIN table_location_code AS tlc ON s.location_code = tlc.location_code  
				ORDER BY $date $order";
		
		$result = mysqli_query($connect, $sql);
		
		if(mysqli_num_rows($result) == 0){
			echo "<tr class='insert_item'>";
			echo 	"<td colspan='6'>광고 등록한 업소가 없습니다.</td>";	  	
			echo "</tr>";
		} else{
			while($row = mysqli_fetch_assoc($result)){
				echo "<tr class='insert_item row_incommerce'>";
				echo 	"<input type='hidden' name='idx' value='".$row['idx']."'>";
				echo 	"<input type='hidden' name='s_location_code' value='".$row['location_code']."'>";
				echo 	"<td>".$row['shop_name']."</td>";
				echo 	"<td>".$row['url']."</td>";
				echo 	"<td>".$row['location_place']."</td>";
				echo	"<td>".$row['regdate']."</td>";
				echo	"<td>".$row['expdate']."</td>";
				echo 	"<td>";
				
				$locationList = getCommerceLocation($connect, $row['idx']);
				while($location = mysqli_fetch_assoc($locationList)){
					echo 	"<span class='location_trans_dist'>".$location['location_place']."</span> ";}
				echo 	"</td>";
				echo "</tr>";
			}
		}
	}
	
	# 광고업소 - idx로 광고 정보 가져오기
	function getCommerceInfo($connect, $sc_idx){
		$sql = "SELECT s.shop_name, sc.url, CONCAT(tlc.location_place, ' ', s.shop_addr) AS address, sc.regdate, sc.expdate 
				FROM shop_commerce AS sc 
				LEFT JOIN shop AS s ON s.url = sc.url 
				LEFT JOIN table_location_code AS tlc ON tlc.location_code = s.location_code 
				WHERE sc.idx = $sc_idx";
				
		$result = mysqli_query($connect, $sql);
		$row = mysqli_fetch_assoc($result);
		return $row; 
	}
	


	# 광고업소 - 각 등록된 가맹점마다 광고지역 가져오기
	function getCommerceLocation($connect, $sc_idx){
		$sql = "SELECT tlc.location_code, tlc.location_place 
				FROM shop_commerce_location AS scl 
				LEFT JOIN table_location_code AS tlc ON scl.location_code = tlc.location_code 
				WHERE sc_idx = $sc_idx 
				ORDER BY scl.location_code ASC";
		
		$result = mysqli_query($connect, $sql);
		
		return $result;
	}
	
	# 광고업소 - 같은 지역 업소 보기
	function getEqualLocationCommerce($connect, $location_code, $date, $order){
		if(fmod($location_code, 100) == 0){ //전 지역에서 찾기
			$sql = "SELECT sc.idx, s.shop_name, s.location_code, sc.url, s.shop_addr, tlc.location_place, sc.regdate, sc.expdate 
				FROM shop_commerce AS sc 
				LEFT JOIN shop AS s ON s.url = sc.url 
				LEFT JOIN table_location_code AS tlc ON s.location_code = tlc.location_code 
				WHERE FLOOR(s.location_code/100) = FLOOR($location_code/100) 
				ORDER BY $date $order";
				
		}else{ //특정 구,군에서 찾기
			$sql = "SELECT sc.idx, s.shop_name, s.location_code, sc.url, s.shop_addr, tlc.location_place, sc.regdate, sc.expdate 
				FROM shop_commerce AS sc 
				LEFT JOIN shop AS s ON s.url = sc.url 
				LEFT JOIN table_location_code AS tlc ON s.location_code = tlc.location_code 
				WHERE s.location_code = $location_code 
				ORDER BY $date $order";
		}
		
		$result = mysqli_query($connect, $sql);
		
		if(mysqli_num_rows($result) == 0){
			echo "<tr class='insert_item'>";
			echo 	"<td colspan='6'>광고 등록한 업소가 없습니다.</td>";	  	
			echo "</tr>";
		} else{
			while($row = mysqli_fetch_assoc($result)){
				echo "<tr class='insert_item row_incommerce'>";
				echo 	"<input type='hidden' name='idx' value='".$row['idx']."'>";
				echo 	"<input type='hidden' name='s_location_code' value='".$row['location_code']."'>";
				echo 	"<td>".$row['shop_name']."</td>";
				echo 	"<td>".$row['url']."</td>";
				echo 	"<td>".$row['location_place']."</td>";
				echo	"<td>".$row['regdate']."</td>";
				echo	"<td>".$row['expdate']."</td>";
				echo 	"<td>";
				
				$locationList = getCommerceLocation($connect, $row['idx']);
				while($location = mysqli_fetch_assoc($locationList)){
					echo 	"<span class='location_trans_dist'>".$location['location_place']."</span> ";}
				echo 	"</td>";
				echo "</tr>";
			}
		}
	}

	# 광고업소 - 광고 삭제
	function deleteIncommerce($connect, $sc_idx){
		$sql = "DELETE FROM shop_commerce_location WHERE sc_idx = $sc_idx";
		
		if($result = mysqli_query($connect, $sql)){
			$sql = "DELETE FROM shop_commerce WHERE idx = $sc_idx";
			$result = mysqli_query($connect, $sql);
		}
	}
	
	# 광고업소 - 정보 변경
	function modifyIncommerce($connect, $sc_idx){
		$regdate = $_POST['submit_incommerce_regdate'];
		$expdate = $_POST['submit_incommerce_expdate'];
		
		$sql = "UPDATE shop_commerce SET regdate = '$regdate', expdate = '$expdate' WHERE idx = $sc_idx";
		
		if(mysqli_query($connect, $sql)){
			$sql = "DELETE FROM shop_commerce_location WHERE sc_idx = $sc_idx";
			
			if(mysqli_query($connect, $sql)){
				$location_code_arr = explode(",", $_POST['location_code_arr']);
				$sql = "";
				for($x = 0; $x < count($location_code_arr); $x++){
					$location_code = $location_code_arr[$x];
					$sql .= "INSERT shop_commerce_location (sc_idx, location_code) VALUES ($sc_idx, $location_code);";
				}
				mysqli_multi_query($connect, $sql);
			}
		}
	}
	
	# 광고업소 신규등록 - 등록
	function registShopIncommerce($connect, $url){
		$regdate = $_POST['submit_incommerce_regdate'];
		$expdate = $_POST['submit_incommerce_expdate'];
		
		$sql = "INSERT shop_commerce (url, regdate, expdate) VALUES ('$url', '$regdate', '$expdate')";
		
		if(mysqli_query($connect, $sql)){
			$location_code_arr = explode(",", $_POST['location_code_arr']);
			$sc_idx = mysqli_insert_id($connect);
			$sql = "";
			for($x = 0; $x < count($location_code_arr); $x++){
				$location_code = $location_code_arr[$x];
				$sql .= "INSERT shop_commerce_location (sc_idx, location_code) VALUES ('$sc_idx', '$location_code'); ";
			}
			mysqli_multi_query($connect, $sql);
			
		}		
	}
	
	# 광고업소 신규등록 - 업소 찾기
	function searchShop($connect, $search_word){
		$sql = "SELECT s.shop_name, s.url, CONCAT(tlc.location_place, ' ', s.shop_addr) AS address 
				FROM shop AS s 
				LEFT JOIN table_location_code AS tlc ON s.location_code = tlc.location_code 
				WHERE INSTR(s.shop_name, '$search_word') > 0 OR INSTR(s.url, '$search_word') > 0 ";
			
		$result = mysqli_query($connect, $sql);
		if(mysqli_num_rows($result) == 0){
			echo "<tr><td colspan='3'>검색 결과가 없습니다.</td></tr>";
		}else{
			while($row = mysqli_fetch_assoc($result)){
				echo "<tr class='insert_item'>";
				echo 	"<td>".$row['shop_name']."</td>";
				echo 	"<td>".$row['url']."</td>";
				echo	"<td>".$row['address']."</td>";
				echo "</tr>";
			}
		}
	
	}
	
	# 광고업소 - 지역 시/도 목록 가져오기
	function getLocationCityList($connect){
		$sql = "SELECT location_code, SUBSTRING_INDEX(location_place, ' ', 1) AS city FROM table_location_code GROUP BY city ORDER BY location_code";
		
		$result = mysqli_query($connect, $sql);
		
		return $result;
	}
	
	# 광고업소 - 지역 구/군 목록 가져오기
	function getLocationDistList($connect, $location_code){
		$sql = "SELECT * FROM table_location_code WHERE FLOOR(location_code/100) = FLOOR($location_code/100)";
		
		$result = mysqli_query($connect, $sql);
		
		$size = mysqli_num_rows($result);
		
		for($x = 0; $x<$size; $x++){
			$row = mysqli_fetch_assoc($result);	
			echo "<option value='".$row['location_code']."'>".$row['location_place']."</option>";
		}
	}
	
	# 광고업소 - 업소코드로 지역코드, 구/군 찾기
	function getLocationCodeByURL($connect, $url){
		$sql = "SELECT s.location_code, tlc.location_place FROM shop AS s LEFT JOIN table_location_code AS tlc ON s.location_code = tlc.location_code WHERE s.url = '$url'";
		
		$result = mysqli_query($connect, $sql);
		$row = mysqli_fetch_assoc($result);
			
		$opt = "<option value='".$row['location_code']."'>".$row['location_place']."</option>";
		echo $opt;
	}
	
	# 광고업소 - 지역코드로 지역명 가져오기
	function getLocationPlaceByCode($connect, $location_code){
		$sql = "SELECT location_place FROM table_location_code WHERE location_code = $location_code";
		
		$result = mysqli_query($connect, $sql);
		
		if($row = mysqli_fetch_assoc($result)){
			$place = $row['location_place'];
			echo $place;
		}
	}
	
	# 외부업체 - 수정
	function modifyExcommerce($connect, $e_idx){
		$e_enterprise = $_POST['submit_excommerce_enterprise'];
		$e_info = $_POST['submit_excommerce_info'];
		$e_type = $_POST['submit_excommerce_type'];
		$e_page_url = $_POST['submit_excommerce_page_url'];
		$e_regdate = $_POST['submit_excommerce_regdate'];
		$e_expdate = $_POST['submit_excommerce_expdate'];

		# 내부 호스팅일 때 && 페이지 변경시 파일 새로 생성 
		$new_php_page = "";
		if( $e_type == 0 && isset($_POST['files_amount']) ){
			$new_php_page = check_create_page($connect, $e_page_url);
			$page_absolute_path = "http://replica66.cafe24.com/m/code/upload/commercial_pages/";
			$e_page_url = $page_absolute_path . $new_php_page;
		}

		$sql = "UPDATE ext_commerce SET e_enterprise = '$e_enterprise', e_info = '$e_info', e_type = $e_type, e_page_url = '$e_page_url', e_regdate = '$e_regdate', e_expdate = '$e_expdate' WHERE idx = '$e_idx'"; 

		# 광고 지역 변경
		if(mysqli_query($connect, $sql)){
			$sql = "DELETE FROM ext_commerce_location WHERE e_idx = '$e_idx'";

			if(mysqli_query($connect, $sql)){
				$location_code_arr = explode(",", $_POST['location_code_arr']);
				$sql = "";
				for($x = 0; $x < count($location_code_arr); $x++){
					$location_code = $location_code_arr[$x];
					$sql .= "INSERT ext_commerce_location (e_idx, e_location_code) VALUES ($e_idx, $location_code);";
				}
				mysqli_multi_query($connect, $sql);
			}
		}

		# 내부호스팅 - 파일 변경
		if( 0 == $e_type && isset($_POST['submit_excommerce_page_url']) && isset($_POST['files_amount']) ){		
			$count = $_POST['files_amount'];
				
			for( $y = 0; $y < $count; $y++ ){
				$clientImgFile = $_FILES['file_'.$y]['name'];
				$serverImgFile = $_FILES['file_'.$y]['tmp_name'];
				$file_renamed = uploadImage($clientImgFile, $serverImgFile, $e_idx, $y);
				if( isset($file_renamed) && !empty($file_renamed) ){
					$page_path = "upload/commercial_pages/";
					$php_file = fopen( $page_path.$new_php_page, "a") or die("파일을 작성하는데 실패하였습니다.");
					$append_txt = "<img src='http://replica66.cafe24.com/m/code/upload/commercial_imgs/".$file_renamed."'>";
					fwrite($php_file, $append_txt);
					fclose($php_file);
				}
			}
		}

		# 배너 이미지 변경(이미지 업로드 있을 때에만)
		if( isset($_FILES['submit_excommerce_main_img']['type']) ){
			$clientFile = $_FILES['submit_excommerce_main_img']['name'];
			$serverFile = $_FILES['submit_excommerce_main_img']['tmp_name'];
			$main_renamed = uploadImage($clientFile, $serverFile, $e_idx, 'main');
			$e_main_img = "http://replica66.cafe24.com/m/code/upload/commercial_imgs/". $main_renamed;
			$sql = "UPDATE ext_commerce SET e_main_img = '$e_main_img' WHERE idx = $e_idx";

			mysqli_query($connect, $sql);
		}
		echo "수정이 완료되었습니다.";
	}
	
	# 외부업체 - 광고 목록 가져오기
	function getExCommerceList($connect, $date, $order){
		$sql = "SELECT *, if(e_type=0, '내부', '외부') AS hosting FROM ext_commerce ORDER BY $date $order";//hosting: 0=내부, 1=외부
		
		$result = mysqli_query($connect, $sql);
		
		if( mysqli_num_rows($result) == 0 ){
			echo "<tr class='insert_item'>";
			echo 	"<td colspan='6'>광고 등록한 업체가 없습니다.</td>";
			echo "</tr>";
		}
		while($row = mysqli_fetch_assoc($result)){
			$idx = $row['idx'];
			echo "<tr class='insert_item row_excommerce'>";
			echo 	"<input type='hidden' name='idx' value='".$row['idx']."'>";
			echo 	"<td>".$row['e_enterprise']."</td>";
			echo 	"<td>".$row['e_info']."</td>";
			echo	"<td>".$row['hosting']."</td>";
			echo	"<td>".$row['e_page_url']."</td>";
			echo	"<td>".$row['e_regdate']."</td>";
			echo	"<td>".$row['e_expdate']."</td>";
			echo 	"<td>";
			
			$locationList = getExCommerceLocation($connect, $idx);
			while($location = mysqli_fetch_assoc($locationList)){
				echo 	"<span class='location_trans_dist'>".$location['location_place']."</span> ";
			}
			echo	"</td>";
			echo "</tr>";
		}
	}
	
	# 외부업체 - 광고 목록 가져오기(Conditional)
	function getExcommerceListConditional($connect, $e_type, $regdate, $order, $location_code){
		if( isset($e_type) && !empty($e_type) ){
			$cond_type = "AND e_type = '$e_type' ";
		}
		if( isset($location_code) && !empty($location_code) ){
			$cond_location = "AND ecl.e_location_code = '$location_code' ";
		}

		$sql = "SELECT ec.idx, ec.e_enterprise, ec.e_info, ec.e_page_url, ec.e_regdate, ec.e_expdate, if(e_type=0, '내부', '외부') AS hosting FROM ext_commerce AS ec
				LEFT JOIN ext_commerce_location AS ecl ON ec.idx = ecl.e_idx 
				WHERE ec.idx != 0 ".$cond_type." ".$cond_location." 
				ORDER BY $regdate $order";

		$result = mysqli_query($connect, $sql);

		if( mysqli_num_rows($result) == 0 ){
			echo "<tr class='insert_item'>";
			echo 	"<td colspan='7'>결과가 없습니다.</td>";
			echo "</tr>";
		}
		while($row = mysqli_fetch_assoc($result)){
			$idx = $row['idx'];
			echo "<tr class='insert_item row_excommerce'>";
			echo 	"<input type='hidden' name='idx' value='".$row['idx']."'>";
			echo 	"<td>".$row['e_enterprise']."</td>";
			echo 	"<td>".$row['e_info']."</td>";
			echo	"<td>".$row['hosting']."</td>";
			echo	"<td>".$row['e_page_url']."</td>";
			echo	"<td>".$row['e_regdate']."</td>";
			echo	"<td>".$row['e_expdate']."</td>";
			echo 	"<td>";
			
			$locationList = getExCommerceLocation($connect, $idx);
			while($location = mysqli_fetch_assoc($locationList)){
				echo 	"<span class='location_trans_dist'>".$location['location_place']."</span> ";
			}
			echo	"</td>";
			echo "</tr>";
		}
		
	}

	# 외부업체 - idx로 광고정보 가져오기
	function getExCommerceInfo($connect, $e_idx){
		$sql = "SELECT * FROM ext_commerce WHERE idx = '$e_idx'";

		$result = mysqli_query($connect, $sql);
		$row = mysqli_fetch_assoc($result);
		return $row;
	}

	# 외부업체 - 각 업체마다 광고지역 가져오기
	function getExCommerceLocation($connect, $e_idx){
		$sql = "SELECT ecl.e_location_code, tlc.location_place FROM ext_commerce_location AS ecl 
				LEFT JOIN table_location_code AS tlc ON tlc.location_code = ecl.e_location_code 
				WHERE ecl.e_idx = '$e_idx'";
		
		$result = mysqli_query($connect, $sql);

		return $result;
	}
	
	# 외부업체 - 삭제
	function deleteExcommerce($connect, $e_idx){
		$sql = "DELETE FROM ext_commerce WHERE idx = '$e_idx'";

		if( mysqli_query($connect, $sql) ){
			$sql = "DELETE FROM ext_commerce_location WHERE e_idx = '$e_idx'";
			mysqli_query($connect, $sql);
		}
	}

	# 외부업체 - 광고 등록
	function registEnterExcommerce($connect, $e_enterprise){
		$e_info = $_POST['e_info'];
		$e_type = (int)$_POST['e_type'];
		$e_page_url = $_POST['e_page_url'];
		$e_regdate = $_POST['e_regdate'];
		$e_expdate = $_POST['e_expdate'];
		
		# 내부 호스팅일 때, 페이지 파일 생성
		$new_php_page = "";
		if( $e_type == 0 ){
			$new_php_page = check_create_page($connect, $e_page_url);
			$page_absolute_path = "http://replica66.cafe24.com/m/code/upload/commercial_pages/";
			$e_page_url = $page_absolute_path . $new_php_page;
		}
		
		$sql = "INSERT ext_commerce (e_enterprise, e_info, e_type, e_page_url, e_regdate, e_expdate) VALUES ('$e_enterprise', '$e_info', '$e_type', '$e_page_url', '$e_regdate', '$e_expdate')";
		
		# 외부업체 신규등록 - 광고지역 등록 
		if(mysqli_query($connect, $sql)){
			$location_code_arr = explode(",", $_POST['location_code_arr']);
			$e_idx = mysqli_insert_id($connect); 
			$sql = "";
			for($x = 0; $x < count($location_code_arr); $x++){
				$location_code = $location_code_arr[$x];
				$sql .= "INSERT ext_commerce_location (e_idx, e_location_code) VALUES ($e_idx, $location_code); ";
			}
			mysqli_multi_query($connect, $sql);
			
			# 배너 이미지 업로드 & absolute path 생성
			if( isset($e_idx) && !empty($e_idx) ){
				$clientMainImg = $_FILES['e_main_img']['name'];
				$serverMainImg = $_FILES['e_main_img']['tmp_name'];
				$main_renamed = uploadImage($clientMainImg, $serverMainImg, $e_idx, 'main');
				$e_main_img = "http://replica66.cafe24.com/m/code/upload/commercial_imgs/".$main_renamed;
				$sql = "UPDATE ext_commerce SET e_main_img = '$e_main_img' WHERE idx = $e_idx";		
					
				mysqli_query($connect, $sql);
			}
			
			# 외부업체 - 내부 호스팅 이미지 업로드 & 파일 쓰기
			if( $e_type == 0 && isset($_POST['e_page_url']) && isset($_POST['files_amount'])  ){
				$count = $_POST['files_amount'];
				
				for( $y = 0; $y < $count; $y++ ){
					$clientImgFile = $_FILES['file_'.$y]['name'];
					$serverImgFile = $_FILES['file_'.$y]['tmp_name'];
					$file_renamed = uploadImage($clientImgFile, $serverImgFile, $e_idx, $y);
					if( isset($file_renamed) && !empty($file_renamed) ){
						$page_path = "upload/commercial_pages/";
						$php_file = fopen( $page_path.$new_php_page, "a") or die("파일을 작성하는데 실패하였습니다.");
						$append_txt = "<img src='http://replica66.cafe24.com/m/code/upload/commercial_imgs/".$file_renamed."'>";
						fwrite($php_file, $append_txt);
						fclose($php_file);
					}
				}
			}	
		}
		echo "등록이 완료되었습니다.";
	}
	
	# 내부 호스팅 - 이미지 파일 업로드
	function uploadImage($clientFile, $serverFile, $e_idx, $x){ 
		$file_extenstion = end( explode(".", $clientFile) );
		$file_renamed = "img_".$e_idx."_".$x."_".date("YmdHis").".".$file_extenstion;		
		$target_path = "upload/commercial_imgs/".$file_renamed;
		
		$move = move_uploaded_file($serverFile, $target_path);
		
		if($move){ #업로드 성공
			return $file_renamed;
		}else{
			echo "Not uploaded because of error menu #".$_FILES["file_".$x]["error"];
		}
	}
	
	# 내부 호스팅 - 페이지명 중복확인 및 생성
	function check_create_page($connect, $e_page_url){
		$sql = "SELECT STRCMP( SUBSTRING_INDEX(e_page_url, '/', -1), CONCAT('$e_page_url', '.php') ) AS isDuplicate FROM ext_commerce";
		
		$result = mysqli_query($connect, $sql);
		
		$isDuplicate = false;
		while($row = mysqli_fetch_assoc($result)){
			$dup = $row['isDuplicate'];
			if($dup == 0){
				echo "입력한 페이지명은 이미 존재합니다.\n다른 페이지명을 입력해주세요.";
				$isDuplicate = true;
				exit;
			}
		}
		
		if(!$isDuplicate){ # 페이지 생성 & 기본 css 설정 입력
			$file_path = "upload/commercial_pages/";
			$new_file_name = $e_page_url.".php";
			
			$file_setting = "<style> body{ margin: 0; } img{ width: 100vw; }</style>";
			
			$file = fopen($file_path.$new_file_name, "x+");
			fwrite($file, $file_setting);
			fclose($file);	
			
			return $new_file_name;
		}
	}

	function searchExcommerce($connect, $column, $text){
		$sql = "SELECT *, if(e_type=0, '내부', '외부') AS hosting 
				FROM ext_commerce AS ec 
				WHERE INSTR( $column, '$text' ) ";
	
		$result = mysqli_query($connect, $sql);

		while($row = mysqli_fetch_assoc($result)){
			$idx = $row['idx'];
			echo "<tr class='insert_item row_excommerce'>";
			echo 	"<input type='hidden' name='idx' value='".$idx."'>";
			echo 	"<td>".$row['e_enterprise']."</td>";
			echo 	"<td>".$row['e_info']."</td>";
			echo	"<td>".$row['hosting']."</td>";
			echo	"<td>".$row['e_page_url']."</td>";
			echo	"<td>".$row['e_regdate']."</td>";
			echo	"<td>".$row['e_expdate']."</td>";
			echo 	"<td>";
			
			$locationList = getExCommerceLocation($connect, $idx);
			while($location = mysqli_fetch_assoc($locationList)){
				echo 	"<span class='location_trans_dist'>".$location['location_place']."</span> ";
			}
			echo	"</td>";
			echo "</tr>";
		}
	}
?>