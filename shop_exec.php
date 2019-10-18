<?php session_start(); ?>
<?php
	include_once $_SERVER[DOCUMENT_ROOT].'/m/data/DB_connect.php';

	//$connect = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

	/* check connection */
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}
	
	# 가맹점 등록
	if( isset($_POST['new_shop_name']) && !empty($_POST['new_shop_name']) ){
		registerShop($connect);
	}
	
	# 가맹점 등록 > 지사 선택시 대리점 목록 가져오기
	if( isset($_POST['select_branch_url']) && !empty($_POST['select_branch_url']) ){
		$br_url = $_POST['select_branch_url'];
		getAgentList($connect, $br_url);
	}
	
	// 지사 선택 후 대리점 선택하는 로직
	if(isset($_POST['branch_url']) && !empty($_POST['branch_url'])){
		$branch_url = $_POST['branch_url'];
		$id = $_POST['agent_id'];
		//echo "success";
		getAgent($connect,$branch_url, $id);
	}



	//가맹점 검색
	if(isset($_POST['search_type']) && !empty($_POST['search_type']) ){
		$search_type = $_POST['search_type'];
		$search_text = $_POST['search_text'];
		$id = $_POST['search_id'];
		$auth = $_POST['search_auth'];
		//echo "success";
		searchShop($connect, $search_type, $search_text, $auth, $id);
	}

	//가맹점 삭제
	if(isset($_POST['del_idx']) && !empty($_POST['del_idx'])){
		$idx = $_POST['del_idx'];
		//echo "success";
		deleteData_pop($connect, $idx);
	}

	//가맹점 수정
	if(isset($_POST['edit_idx']) && !empty($_POST['edit_idx'])){
		$idx = $_POST['edit_idx'];

		editShop($connect, $idx);
	}

	//가맹점 수정 > 지사 이름 가져오기
	if(isset($_POST['get_branch_edit']) && !empty($_POST['get_branch_edit'])){
		$get_branch_edit = $_POST['get_branch_edit'];
		$id =  $_POST['user_id'];

		if($get_branch_edit == "1"){
			$qry = "SELECT  u.auth
					FROM user u WHERE id = '$id'  ";

			/* Select queries return a resultset */
			if ($result = mysqli_query($connect, $qry)) {
				//printf("Select returned %d rows.\n", mysqli_num_rows($result));
				$auth = mysqli_fetch_object($result);

				$auth_result = $auth->auth;

				if($auth_result == "manager"){
					$qry2 = "SELECT b.branch_id, b.branch_name, b.branch_code
							FROM branch b";

					if ($result2 = mysqli_query($connect, $qry2)) {
						$num_rows = mysqli_num_rows($result2);
						$br_name = "";
						$chk_rows = 0;

						while ($row = mysqli_fetch_assoc($result2)) {
							$chk_rows++;
							$br_name = $br_name.$row['branch_name']."/".$row['branch_idx']."/".$row['branch_code'];

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
	
	//가맹점 수정 > 구, 군 목록 가져오기
	if(isset($_POST['location_city_code']) && !empty($_POST['location_city_code'])){
		$location_code = (int)$_POST['location_city_code'];

		getLocationDist($connect, $location_code);
	}
	
	//가맹점 수정 > 상점 전경 이미지 삭제
	if(isset($_POST['del_idx_shop']) && !empty($_POST['del_idx_shop']) ){
		$idx = $_POST['del_idx_shop'];
		deleteShopPhoto_pop($connect, $idx);
	}else{
		//echo "fail";
	}

	//가맹점 수정 > 메뉴 삭제
	if(isset($_POST['del_menu_idx']) && !empty($_POST['del_menu_idx'])){
		$idx = $_POST['del_menu_idx'];
		//echo "success";
		deleteMenu_pop($connect, $idx);
	}else{
		//echo "fail";
	}
	//가맹점 수정 > 주인장 이야기 > 번개할인 수정
	if(isset($_POST['edit_sale_idx']) && !empty($_POST['edit_sale_idx']) ){
		$idx = $_POST['edit_sale_idx'];

		editHostSale($connect, $idx);
	}else{
		//echo "fail";
	}

	//가맹점 수정 > 주인장 이야기 > 번개할인 추가
	if(isset($_POST['rgst_sale_url']) && !empty($_POST['rgst_sale_url']) ){
		rgstHostSale($connect);
	}else{
		//echo "fail";
	}

	//가맹점 수정 > 주인장 이야기 > 번개할인 삭제
	if(isset($_POST['del_sale_idx']) && !empty($_POST['del_sale_idx']) ){
		$idx = $_POST['del_sale_idx'];
		deleteHostSale($connect, $idx);
	}else{
		//echo "fail";
	}

	//가맹점 수정 > 주인장 이야기 > 손님 이야기 삭제
	if(isset($_POST['del_reply_idx']) && !empty($_POST['del_reply_idx']) ){
		$idx = $_POST['del_reply_idx'];
		deleteHostReply($connect, $idx);
	}else{
		//echo 'fail';
	}

	//가맹점 수정 > 주인장 이야기 > 태그 관리
	if( isset($_POST['tag_new']) && !empty($_POST['tag_new']) ){
		$idx = $_POST['tag_shop_idx'];
		insertTag($connect, $idx);
	}else{
		//echo fail;
	}
	if( isset($_POST['tag_del_idx']) && !empty($_POST['tag_del_idx']) ){
		$idx = $_POST['tag_shop_idx'];
		deleteTag($connect, $idx);
	}

	function getAuth($connect, $id){
		$qry = "SELECT auth FROM user WHERE id = '$id'";

		$result = mysqli_query($connect, $qry);
		$value = mysqli_fetch_object($result);
		$auth = $value->auth;

		return $auth;
	}
	

	
	function ViewList($connect, $auth, $id){

		if($auth == "branch"){

		$qry = "SELECT *
				FROM(
					SELECT tb.*
					FROM (
						SELECT s.idx, s.url,  s.agent_idx, s.`shop_name`,s.`shop_addr`, s.`shop_ceo_name`, s.`shop_phone`, s.`shop_ceo_phone`,
											s.`shop_rgst_date`, a.`agent_name`, a.`id`, b.`branch_name`, call_cnt.cnt

						FROM  shop s
						INNER JOIN agent a ON s.`agent_idx` = a.`idx`
						INNER JOIN branch b ON a.`br_url` = b.branch_code AND b.branch_id = '$id'

						JOIN(
							SELECT  s.idx, s.url,  s.agent_idx, s.`shop_name`,s.`shop_addr`, s.`shop_ceo_name`, s.`shop_phone`, s.`shop_ceo_phone`,
								s.`shop_rgst_date`,  a.`agent_name`, a.`id`, b.`branch_name`, COUNT(*) AS cnt
							FROM shop s
							LEFT JOIN calldrivertb c ON c.`url` = s.`url`
							LEFT JOIN agent a ON a.idx = s.`agent_idx`
							LEFT JOIN branch b ON a.`br_url` = b.branch_code  AND b.id = '$id'

							WHERE c.`state`='S'
							GROUP BY s.`idx`

							UNION

							SELECT  s.idx, s.url,  s.agent_idx, s.`shop_name`,s.`shop_addr`, s.`shop_ceo_name`, s.`shop_phone`, s.`shop_ceo_phone`,
								s.`shop_rgst_date`, a.`agent_name`, a.`id`, b.`branch_name`, COUNT(*) AS cnt
							FROM shop s
							LEFT JOIN (
								SELECT c.`state`, tag.`shopSeq` FROM calldrivertb c
								LEFT OUTER JOIN (
									SELECT sc.`shopSeq`, r.`hpno` FROM registertag r
									LEFT OUTER JOIN serialcode sc ON r.`tagid` = sc.`serialNum`
									GROUP BY sc.`serialNum` HAVING COUNT(*) >= 1
								) AS tag ON c.`hpno` = tag.hpno
							) AS t ON s.`idx` = t.`shopSeq`

							LEFT JOIN agent a ON a.idx = s.`agent_idx`
							LEFT JOIN branch b ON a.`br_url` = b.branch_code  AND b.id = '$id'

							WHERE t.`state` = 'S'
							GROUP BY s.`idx`
						)AS call_cnt ON s.`idx` = call_cnt.idx
					)AS tb

					UNION


					SELECT tb.* FROM  (
					SELECT s.idx, s.url,  s.agent_idx, s.`shop_name`,s.`shop_addr`, s.`shop_ceo_name`, s.`shop_phone`, s.`shop_ceo_phone`,
										s.`shop_rgst_date`,  a.`agent_name`, a.`id`, b.`branch_name`, '0' AS cnt
					FROM shop s
					INNER JOIN agent a ON s.`agent_idx` = a.`idx`
					INNER JOIN branch b ON a.`br_url` = b.branch_code AND b.id = '$id'


					)AS tb
				)AS rs

				GROUP BY rs.`idx`
				ORDER BY rs.shop_rgst_date DESC ";
		}
		else if($auth == "agent"){

		$qry = "SELECT *
				FROM(
					SELECT tb.*
					FROM (
						SELECT s.idx, s.url,  s.agent_idx, s.`shop_name`, s.`shop_addr`, s.`shop_ceo_name`, s.`shop_phone`, s.`shop_ceo_phone`,
											s.`shop_rgst_date`, a.`agent_name`, a.`id`, b.`branch_name`, call_cnt.cnt

						FROM  shop s
						INNER JOIN agent a ON s.`agent_idx` = a.`idx` AND a.id = '$id'
						INNER JOIN branch b ON a.`br_url` = b.branch_code

						JOIN(
							SELECT  s.idx, s.url,  s.agent_idx, s.`shop_name`, s.`shop_addr`, s.`shop_ceo_name`, s.`shop_phone`, s.`shop_ceo_phone`,
								s.`shop_rgst_date`, a.`agent_name`, a.`id`, b.`branch_name`, COUNT(*) AS cnt
							FROM shop s
							LEFT JOIN calldrivertb c ON c.`url` = s.`url`
							LEFT JOIN agent a ON a.idx = s.`agent_idx` AND a.id = '$id'
							LEFT JOIN branch b ON a.`br_url` = b.branch_code

							WHERE c.`state`='S'
							GROUP BY s.`idx`

							UNION

							SELECT  s.idx, s.url,  s.agent_idx, s.`shop_name`, s.`shop_addr`, s.`shop_ceo_name`, s.`shop_phone`, s.`shop_ceo_phone`,
								s.`shop_rgst_date`,  a.`agent_name`, a.`id`, b.`branch_name`, COUNT(*) AS cnt
							FROM shop s
							LEFT JOIN (
								SELECT c.`state`, tag.`shopSeq` FROM calldrivertb c
								LEFT OUTER JOIN (
									SELECT sc.`shopSeq`, r.`hpno` FROM registertag r
									LEFT OUTER JOIN serialcode sc ON r.`tagid` = sc.`serialNum`
									GROUP BY sc.`serialNum` HAVING COUNT(*) >= 1
								) AS tag ON c.`hpno` = tag.hpno
							) AS t ON s.`idx` = t.`shopSeq`

							LEFT JOIN agent a ON a.idx = s.`agent_idx` AND a.id = '$id'
							LEFT JOIN branch b ON a.`br_url` = b.branch_code

							WHERE t.`state` = 'S'
							GROUP BY s.`idx`
						)AS call_cnt ON s.`idx` = call_cnt.idx
					)AS tb

					UNION


					SELECT tb.* FROM  (
					SELECT s.idx, s.url,  s.agent_idx, s.`shop_name`, s.`shop_addr`, s.`shop_ceo_name`, s.`shop_phone`, s.`shop_ceo_phone`,
										s.`shop_rgst_date`,  a.`agent_name`, a.`id`, b.`branch_name`, '0' AS cnt
					FROM shop s
					INNER JOIN agent a ON s.`agent_idx` = a.`idx` AND a.id = '$id'
					INNER JOIN branch b ON a.`br_url` = b.branch_code


					)AS tb
				)AS rs

				GROUP BY rs.`idx`
				ORDER BY rs.shop_rgst_date DESC ";
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
		$qry = "SELECT s.*, a.`agent_name`, b.`branch_name`, a.`branch_code`, h.`pass`
				FROM  shop s
				LEFT JOIN agent a ON s.agent_idx = a.agent_idx
				LEFT JOIN branch b ON b.branch_code = a.branch_code
				LEFT JOIN shop_host h ON h.`url` = s.`url`
				WHERE  s.`idx` = '$idx' ";

		/* Select queries return a resultset */
		if ($result = mysqli_query($connect, $qry)) {
			//printf("Select returned %d rows.\n", mysqli_num_rows($result));
		}else{
			echo("Error description ViewList_pop: " . mysqli_error($connect));
		}

		return $result;
	}

	# 지사 목록 가져오기 -> 가맹점 신규, 가맹점 수정시
	function getBranchList($connect, $auth, $id){
		if( 'manager' == $auth ){
			$sql = "SELECT * FROM branch";
		}else if( 'branch' == $auth ){
			$sql = "SELECT * FROM branch WHERE branch_id = '$id'";		
		}else if ( 'agent' == $auth ){
			$sql = "SELECT b.* FROM branch AS b LEFT JOIN agent AS a ON a.branch_code = b.branch_code WHERE a.agent_id = '$id'";
		}
		$result = mysqli_query($connect, $sql);
		return $result;
	}
	
	# 지사 선택시 대리점 목록 가져오기 -> 가맹점 신규, 가맹점 수정시
	function getAgentList($connect, $br_url){
		$id = $_SESSION['id'];
		$auth = getAuth($connect, $id);
		
		if( 'manager' == $auth ){
			$sql = "SELECT * FROM agent WHERE branch_code = '$br_url'";
			
		}else if( 'branch' == $auth ){
			$sql = "SELECT a.* FROM agent AS a 
					LEFT JOIN branch AS b ON b.branch_code = a.branch_code
					WHERE a.branch_code = '$br_url' AND b.branch_id = '$id' 
					ORDER BY a.agent_name";
		}else if( 'agent' == $auth ){
			$sql = "SELECT a.* FROM agent AS a 
					WHERE branch_code = '$br_url' AND a.agent_id = '$id' 
					ORDER BY a.agent_name";
		}
		$result = mysqli_query($connect, $sql);
		
		while($row = mysqli_fetch_assoc($result)){
			echo "<option value='".$row['agent_idx']."'>".$row['agent_name']."</option>";
		}
		
	}
	
	function ViewTableInfor($connect, $idx){
		$qry = "SELECT * FROM shop_order WHERE shop_idx = '$idx' ORDER BY order_date DESC";

		$result = mysqli_query($connect, $qry);

		return $result;
	}

	function GetMenuName($connect, $menu_idx){
		$qry = "SELECT menu_name FROM shop_menu WHERE idx = '$menu_idx'";

		$result = mysqli_query($connect, $qry);
		$menuName = mysqli_fetch_assoc($result);

		return $menuName['menu_name'];

	}

	function deleteData_pop($connect, $idx){
		/*$qry = "SELECT photo_url
			FROM `shop`
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
		}*/

		$qry = "SELECT url FROM shop WHERE idx = '$idx' ";

		if(	$result = mysqli_query($connect, $qry) ){

			$url = mysqli_fetch_object($result)->url;

			if( isset($url) && !empty($url) ){
				$qry2 = "DELETE FROM shop_host WHERE url = '$url' ";
				mysqli_query($connect, $qry2);
				$qry3 = "DELETE FROM shop_menu WHERE shop_idx = '$idx' ";
				mysqli_query($connect, $qry3);
				$qry4 = "DELETE FROM shop_board WHERE url = '$url' ";
				mysqli_query($connect, $qry4);
				$qry5 = "DELETE FROM shop_photo WHERE idx = '$idx' ";
				mysqli_query($connect, $qry5);
				$qry6 = "DELETE FROM shop_event WHERE url = '$url' ";
				mysqli_query($connect, $qry6);

				$qry7 = "DELETE FROM `shop` WHERE idx = '$idx'";

				if ($result = mysqli_query($connect, $qry7)) {
					//printf("Select returned %d rows.\n", mysqli_num_rows($result));
					echo "success";
				}else{
					 echo("Error description: " . mysqli_error($connect));
				}
			}
		}
	}

	function deleteShopPhoto_pop($connect, $idx){
		$qry = "SELECT photo_url FROM shop_photo WHERE idx = '$idx'";

		if($result = mysqli_query($connect, $qry)){
			$value = mysqli_fetch_object($result);
			$photo_url = $value->photo_url;

			if(strpos($photo_url, "103.60.124.17") > 0 ){
				$unlink_name = "upload".strrchr($photo_url, "/");
				unlink($unlink_name);
			}else {
				//모바일 서버에서 업로드 된 파일
				$qry3 = "INSERT INTO trim_shop_photo (photo_url, edit_date) VALUES ('$photo_url', NOW())";
				mysqli_query($connect, $qry3);
			}

			$qry2 = "DELETE FROM shop_photo WHERE idx = '$idx'";

			if($result = mysqli_query($connect, $qry2)){
				echo "success";
			}else{
				echo("Error description getMenu: " . mysqli_error($connect));
			}
		}
	}

	function trimShopFile($connect){
		$qry = "SELECT * FROM trim_shop_photo";

		if ($result = mysqli_query($connect, $qry)){
			$row = mysqli_fetch_assoc($result);
			$photo_url = $row['photo_url'];

			if(strpos($photo_url, "103.60.124.17") > 0){
				$unlink_name = "upload".strrchr($photo_url, "/");
				unlink($unlink_name);

				$idx = $row['idx'];
				$qry2 ="DELETE FROM trim_shop_photo WHERE idx= '$idx'";
				mysqli_query($connect, $qry2);
			}

		}
	}

	function getMenu($connect, $idx){
		$qry = "SELECT m.*
				FROM  shop_menu m
				WHERE m.`shop_idx` = '$idx' ";

		/* Select queries return a resultset */
		if ($result = mysqli_query($connect, $qry)) {
			//printf("Select returned %d rows.\n", mysqli_num_rows($result));
		}else{
			echo("Error description getMenu: " . mysqli_error($connect));
		}

		return $result;
	}

	function deleteMenu_pop($connect, $idx){
		$qry = "SELECT menu_photo FROM shop_menu WHERE idx = '$idx'";

		if ($result = mysqli_query($connect, $qry)) {
			$value = mysqli_fetch_object($result);
			$photo_url = $value->menu_photo;

			if(strpos($photo_url, "103.60.124.17") > 0 ){
				$unlink_name = "upload".strrchr($photo_url, "/");
				unlink($unlink_name);
			}else { //모바일 서버에서 업로드 된 파일
				$qry3 = "INSERT INTO trim_menu_photo (menu_photo, edit_date) VALUES ('$photo_url', NOW())";
				mysqli_query($connect, $qry3);
			}

			$qry2 = "DELETE FROM shop_menu WHERE idx = '$idx'";

			/* Select queries return a resultset */
			if ($result = mysqli_query($connect, $qry2)) {
				echo "success";
			}else{
				 echo("Error description deleteMenu_pop: " . mysqli_error($connect));
			}
		}
	}

	function trimMenuFile($connect){
		$qry = "SELECT * FROM trim_menu_photo";

		if ($result = mysqli_query($connect, $qry)){
			$row = mysqli_fetch_assoc($result);
			$menu_photo = $row['menu_photo'];

			if(strpos($menu_photo, "103.60.124.17") > 0){
				$unlink_name = "upload".strrchr($menu_photo, "/");
				unlink($unlink_name);

				$idx = $row['idx'];
				$qry2 ="DELETE FROM trim_menu_photo WHERE idx= '$idx'";
				mysqli_query($connect, $qry2);
			}

		}
	}
	

	function getAgent_pop($connect, $branch_url, $id){
		if($id == ''){ //관리자, 지사

			$qry = "SELECT  `agent_name`, `agent_idx`
					FROM  `agent`
					WHERE  `branch_code` =  '$branch_url'
					AND `agent_id` IS NOT NULL AND `agent_id` != ''";
		}else{ //대리점
			$qry = "SELECT  `agent_name`, `agent_idx`
					FROM  `agent`
					WHERE  `branch_code` =  '$branch_url' AND agent_id = '$id'
					AND `agent_id` IS NOT NULL AND `agent_id` != ''";
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
						FROM shop
						WHERE agent_idx = '$agent_idx'
						GROUP BY agent_idx";

		$count_result = mysqli_query($connect, $count_qry);

		while ($row = mysqli_fetch_assoc($count_result)) {
			$cnt = $row['cnt'];
		}

		$make_url = $br_url.$ag_url;

		$cnt++; //대리점 갯수 +1

		return $make_url.",".$cnt;
	}

	function checkURL($connect, $check_url, $cnt){

		$shop_url =  $check_url.$cnt;

		$qry = "SELECT url FROM shop WHERE url = '$shop_url'";

		$result = mysqli_query($connect, $qry);
		$num_chk_url = mysqli_num_rows($result);

		return $num_chk_url;
	}

	# 가맹점 리스트
	function getShopList($connect, $auth, $id){
		if( 'manager' == $auth ){
			$sql = "SELECT s.*, CONCAT(tlc.location_place, ' ', s.shop_addr) AS address, b.branch_name, a.agent_name FROM shop AS s 
					LEFT JOIN table_location_code AS tlc ON tlc.location_code = s.location_code 
					LEFT JOIN agent AS a ON a.agent_idx = s.agent_idx
					LEFT JOIN branch AS b ON b.branch_code = a.branch_code OR b.branch_code = s.manager_code 
					ORDER BY shop_rgst_date DESC";

		}else if( 'branch' == $auth ){
			$sql = "SELECT s.*, CONCAT(tlc.location_place, ' ', s.shop_addr) AS address, b.branch_name, a.agent_name, b.branch_id FROM shop AS s
					LEFT JOIN table_location_code AS tlc ON tlc.location_code = s.location_code
					LEFT JOIN agent AS a ON a.agent_code = s.manager_code
					LEFT JOIN branch AS b ON b.branch_code = a.branch_code OR b.branch_code = s.manager_code
					HAVING b.branch_id = '$id'

					ORDER BY shop_rgst_date DESC";
					
		}else if( 'agent' == $auth ){
			$sql = "SELECT s.*, CONCAT(tlc.location_place, ' ', s.shop_addr) AS address, b.branch_name, a.agent_name, a.agent_id FROM shop AS s
					LEFT JOIN table_location_code AS tlc ON tlc.location_code = s.location_code
					LEFT JOIN agent AS a ON a.agent_code = s.manager_code
					LEFT JOIN branch AS b ON b.branch_code = a.branch_code
					HAVING a.agent_id = '$id'

					ORDER BY shop_rgst_date DESC";	
		}

		$result = mysqli_query($connect, $sql);
		return $result;
	}
	
	# 업소 정보 가져오기 -> 가맹점 신규시, 가맹점 수정시
	function getShopInfo($connect, $shop_idx){
		$sql = "SELECT s.*, CONCAT(tlc.location_place, ' ', s.shop_addr) AS address FROM shop AS s 
				LEFT JOIN table_location_code AS tlc ON tlc.location_code = s.location_code 
				WHERE s.idx = $shop_idx";
		
		$result = mysqli_query($connect, $sql);
		$row = mysqli_fetch_assoc($result);
		return $row;
	}

	# 지사, 대리점 정보 가져오기 -> 가맹점 수정시
	function getManagerInfo($connect, $shop_idx){
		$sql = "SELECT branch_name, agent_name
				from branch left join agent on agent.br_url = branch.branch_code
				where (select manager_code from shop where idx = $shop_idx) = branch.branch_code
				or (select manager_code from shop where idx = $shop_idx) = agent.url";
	}

	function registerShop($connect){
		$shop_name = $_POST['new_shop_name'];
		$shop_type = (int)$_POST['shop_type'];
		$agent_idx = (int)$_POST['agent'];
		$shop_ceo_name = $_POST['shop_ceo_name'];
		$shop_phone = $_POST['shop_phone'];
		
		$branch = $_POST['branch'];
		$agent = $_POST['agent'];
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

		$sql = "INSERT shop (url, type, shop_name, agent_idx, shop_ceo_name, shop_phone, shop_rgst_date) VALUES ('$shop_url', $shop_type, '$shop_name', $agent_idx, '$shop_ceo_name', '$shop_phone', CURRENT_TIMESTAMP()) ";
			
		if(mysqli_query($connect,$sql)){
			$shop_idx = mysqli_insert_id($connect);
			
			$sql = "INSERT shop_host VALUES('$shop_url', '1234')";
			
			if(mysqli_query($connect, $sql)){
				
				$list_new = getShopInfo($connect, $shop_idx);
				
				echo "<tr class='list_item list_click list_new'>";
				echo 	"<input type='hidden' name='shop_idx' value='".$shop_idx."'>";
				echo 	"<td></td>";
				echo	"<td>http://hanjicds001.gabia.io/index.jsp?p=".$shop_url."</td>";
				echo 	"<td>".$shop_name."</td>";
				echo 	"<td></td>";
				echo 	"<td>".$list_new['branch_name']."</td>";
				echo 	"<td>".$list_new['agent_name']."</td>";
				echo 	"<td>".$shop_ceo_name."</td>";
				echo	"<td>".$ceo_phone."</td>";
				echo	"<td>".$list_new['shop_rgst_date']."</td>";
				echo "</tr>";
			}	
		}
	}
	

	
	function editShop($connect, $idx){

		$shop_name = $_POST['shop_name'];
		$type = $_POST['store_type'];
		$url = $_POST['edit_url'];
		$del_idx = $_POST['del_idx'];
		$agent_idx = $_POST['agent_name_edit'];

		$shop_ceo_name = $_POST['shop_ceo_name'];
		$shop_phone = $_POST['shop_phone'];
		$shop_ceo_phone = $_POST['shop_ceo_phone'];
		$shop_addr = $_POST['shop_addr'];
		$shop_rgst_date = $_POST['shop_rgst_date'];
		$open_weekDay = $_POST['open_weekDay'];
		$close_weekDay = $_POST['close_weekDay'];
		$open_weekEnd = $_POST['open_weekEnd'];
		$close_weekEnd = $_POST['close_weekEnd'];
		$offday = $_POST['offday'];
		$recom_menu = $_POST['recom_menu'];
		$intro_text = $_POST['intro_text'];

		$discount = $_POST['discount'];

		$isReserve = isset($_POST['isReserve']) ? 1 : 0;
		$isFree_cds = isset($_POST['isFree_cds']) ? 1 : 0;
		$isParking = isset($_POST['isParking']) ? 1 : 0;
		$isSeats = isset($_POST['isSeats']) ? 1 : 0;

		$lat = (double) $_POST['lat'];
		$lng = (double) $_POST['lng'];

		$r_beer_cup = $_POST['beer_cup'];
		$r_beer_bottle = $_POST['beer_bottle'];
		$r_drink = $_POST['drink'];
		$r_food = $_POST['food'];
		$r_soju = $_POST['soju'];
		$r_airplane  = $_POST['airplane'];


		$qry = "UPDATE shop SET
		shop_name = '$shop_name',
		type = '$type',
		url = '$url',
		agent_idx = '$agent_idx',
		shop_addr = '$shop_addr',
		shop_ceo_name = '$shop_ceo_name',
		shop_phone = '$shop_phone',
		shop_ceo_phone = '$shop_ceo_phone',
	
		/*shop_rgst_date = '$shop_rgst_date',*/
		shop_edit_date = CURRENT_TIMESTAMP,
		open_weekDay = '$open_weekDay',
		close_weekDay = '$close_weekDay',
		open_weekEnd = '$open_weekEnd',
		close_weekEnd = '$close_weekEnd',
		recom_menu = '$recom_menu',
		offday = '$offday',
		intro_text = '$intro_text',
		discount = '$discount ',
		isParking = '$isParking',
		isSeats = '$isSeats',
		lat = '$lat',
		lng = '$lng'


		WHERE  shop.idx = '$idx'";

		if ($connect->query($qry) === TRUE) {
			//echo "success";
			$del_array =  explode(",", trim($del_idx));
			if(sizeof( $del_array ) >0){
				for($i = 0; $i <sizeof($del_array); $i++){
					$qry = "DELETE FROM shop_photo WHERE idx  = '$del_array[i]'";
					mysqli_query($connect, $qry);
				}
			}
		} else {
			echo "Error qry1: " . $qry . "<br>" . $connect->error;
		}

		//--------------------------------------------- qry : 기존 가맹점정보 업데이트
/*
		$qry2 = "SELECT shop_idx
				FROM roulette
				WHERE  shop_idx = '$idx'";

		//--------------------------------------------- qry2 : 룰렛정보 검색

		if ($result = mysqli_query($connect, $qry2)) {
			//printf("Select returned %d rows.\n", mysqli_num_rows($result));
			$num_rows = mysqli_num_rows($result);

			if ( $num_rows == 0 ){
				$qry3 = "INSERT INTO  `roulette` (
						`shop_idx` ,
						`beer_cup` ,
						`beer_bottle` ,
						`drink` ,
						`food` ,
						`soju` ,
						`airplane`
						)
						VALUES (
						'$idx', '$r_beer_cup', '$r_beer_bottle', '$r_drink', '$r_food', '$r_soju', '$r_airplane'
						);";
			}else if ( $num_rows == 1 ){
				$qry3 = "UPDATE  `roulette`
						SET
						`beer_cup` = '$r_beer_cup',
						`beer_bottle` = '$r_beer_bottle',
						`drink` = '$r_drink',
						`food` = '$r_food',
						`soju` = '$r_soju',
						`airplane` = '$r_airplane'

						WHERE  shop_idx = '$idx'";
			}

			if ($connect->query($qry3) === TRUE) {
				echo "success";
			} else {
				echo "Error qry3: " . $qry3 . "<br>" . $connect->error;
			}

		//--------------------------------------------- qry3 : 룰렛 기존정보 유무에 따라 생성 or 업데이트

		}else{
			 echo("Error description qry2: " . mysqli_error($connect));
		}*/
	}

	function searchShop($connect, $search_type, $search_text, $auth, $id){

		if($auth == 'manager'){
			$qry = "SELECT s.idx, s.url, s.shop_name, b.branch_name, a.agent_name, s.agent_idx, s.shop_ceo_name, a.id, s.shop_phone, s.shop_addr, s.shop_rgst_date,
					FROM shop AS s, agent AS a, branch AS b
					WHERE $search_type LIKE '%$search_text%' AND s.agent_idx = a.idx AND a.br_url = b.url";
		}
		else if($auth == 'branch'){
			$qry = "SELECT s.idx, s.url, s.shop_name, b.branch_name, a.agent_name, s.agent_idx, s.shop_ceo_name, a.id, s.shop_phone, s.shop_addr, s.shop_rgst_date,
					FROM shop AS s, agent AS a, branch AS b
					WHERE $search_type LIKE '%$search_text%' AND s.agent_idx = a.idx AND a.br_url = b.url AND b.id = '$id'";

		}
		else if($auth == 'agent'){
			$qry =  "SELECT s.idx, s.url, s.shop_name, b.branch_name, a.agent_name, s.agent_idx, s.shop_ceo_name, a.id, s.shop_phone, s.shop_addr, s.shop_rgst_date,
					FROM shop AS s, agent AS a, branch AS b
					WHERE $search_type LIKE '%$search_text%' AND s.agent_idx = a.idx AND a.br_url = b.url AND a.id = '$id'";

		}


		/* Select queries return a resultset */
		if ($result = mysqli_query($connect, $qry)) {
			//printf("Select returned %d rows.\n", mysqli_num_rows($result));
			$num_rows = mysqli_num_rows($result);
		}else{
			 echo("Error description searchShop: " . mysqli_error($connect));
		}

		if ($num_rows == 0) echo '0';
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
				$t->shop_ceo_name = $row['shop_ceo_name'];
				$t->id = $row['id'];
				$t->shop_phone = $row['shop_phone'];
				$t->shop_addr = $row['shop_addr'];
				$t->shop_rgst_date = $row['shop_rgst_date'];
				$t->cnt = $row['cnt'];
				$data[] = $t;
				unset($t);
			}

			$json_data = json_encode((array) $data);
			print_r($json_data);
		}

	}
	
	# 업소 정보 수정 - 주소 광역시/도 목록 가져오기
	function getLocationCityList($connect){
		$sql = "SELECT location_code, SUBSTRING_INDEX(location_place, ' ', 1) AS city FROM table_location_code 
				WHERE NOT (location_code = 1000) 
				GROUP BY city 
				ORDER BY location_code";
				
		$result = mysqli_query($connect, $sql);
		
		return $result;
	}
	
	# 업소 정보 수정 - 구, 군 목록 가져오기
	function getLocationDist($connect, $shop_location_code){
		
		$sql = "SELECT * FROM table_location_code WHERE MOD(location_code,100) != 0 AND FLOOR(location_code/100) = FLOOR($shop_location_code/100)";
		
		if($result = mysqli_query($connect, $sql)){
			while($row = mysqli_fetch_assoc($result)){
				$location_code = $row['location_code'];
				$location_place = $row['location_place'];
				$selected = "";
				
				if( $location_code == $shop_location_code )
					$selected = "selected"; 
				echo "<option value='".$location_code."' ".$selected.">".$location_place."</option>";
			}
		}
	}
	
	//-----------------------------------------업소 사진 가져오기
	function viewPhoto($connect, $idx){
		$qry = "SELECT * FROM shop_photo WHERE shop_idx = '$idx'";
		$result = mysqli_query($connect, $qry);
		return $result;
	}


	//----------------------------------------------번개할인 수정
	function editHostSale($connect, $idx){
		$url = $_POST['url'];

		$day_period = $_POST['day_period'];
		$time_start = $_POST['time_start'];
		$time_end = $_POST['time_end'];

		$date_start_str = $day_period." ".$time_start;
		$date_end_str = $day_period." ".$time_end;

		$date_start = date("Y-m-d H:i:s", strtotime($date_start_str));
		$date_end = date("Y-m-d H:i:s", strtotime($date_end_str));

		$menu = $_POST['menu'];
		$dc_rate = $_POST['dc_rate'];
		$etc = $_POST['etc'];

		$qry = "UPDATE shop_sale SET date_start='$date_start', date_end='$date_end', menu='$menu', dc_rate='$dc_rate', etc='$etc' WHERE idx = '$idx'";

		if ($connect->query($qry) === TRUE) {
			echo "success";
		} else {
			echo "Error qry: " . $qry . "<br>" . $connect->error;
		}

	}

	//----------------------------------------------번개할인 삭제
	function deleteHostSale($connect, $idx){
		$qry = "DELETE FROM shop_sale WHERE idx='$idx' ";
		if ($connect->query($qry) === TRUE) {
			echo "success";

		} else {
			echo "Error qry: " . $qry . "<br>" . $connect->error;
		}
	}

	function viewHostReply($connect, $url){
		$qry = "SELECT * FROM reply WHERE url = '$url' ORDER BY credate DESC ";

		if ($result = mysqli_query($connect, $qry)) {
			//printf("Select returned %d rows.\n", mysqli_num_rows($result));
		}else{
			echo("Error description: " . mysqli_error($connect));
		}
		return $result;
	}

	function viewReplyPhoto($connect, $idx){
		$qry = "SELECT * FROM reply_photo WHERE reply_idx = '$idx'";

		if ($result = mysqli_query($connect, $qry)) {
			//printf("Select returned %d rows.\n", mysqli_num_rows($result));
		}else{
			echo("Error description: " . mysqli_error($connect));
		}
		return $result;
	}

	function deleteHostReply($connect, $idx){
		$qry = "DELETE FROM reply WHERE idx = '$idx' ";

		if($result = mysqli_query($connect, $qry)){
			//printf("Select returned %d rows.\n", mysqli_num_rows($result));
		}else{
			echo("Error description: " . mysqli_error($connect));
		}
	}
?>
