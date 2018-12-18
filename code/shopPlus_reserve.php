<?php session_start(); ?>
<?php 
header("Content-Type: text/html;charset=UTF-8");
include_once $_SERVER[DOCUMENT_ROOT].'/m/nav.php';
include_once $_SERVER[DOCUMENT_ROOT].'/m/code/shopPlus_reserve_exec.php';
?>

<?php
	$id = $_SESSION['id'];

	$s_type = $_GET['s_type'];
	$s_text = $_GET['s_text'];

	$isAgent = 'n';

	// 페이지네이션
	$num_rec_per_page = 20;
	if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=1; }; 
	$start_from = ($page-1) * $num_rec_per_page;

	if(isset($s_type) && !empty($s_type) && isset($s_text) && !empty($s_text)){
		
		$result = searchShop_re($connect, $s_type, $s_text, $auth, $id);

		if ($auth == 'agent'){			
			$isAgent = 'y';
		}
	
		$num_rows = mysqli_num_rows($result);
		$num = $num_rows + 1; // 가맹점 수

	}else {
		
		$result = ViewList($connect, $auth, $id, $start_from, $num_rec_per_page);

		if ($auth == 'agent'){
			$isAgent = 'y';
		}
	
		$num_rows = mysqli_num_rows($result);
		$num = $num_rows + 1; // 가맹점 수
	}

	// 총 리스트 갯수
	$total_records = _ViewListCount($connect, $auth, $id);
	$total_pages = ceil($total_records / $num_rec_per_page); 
	

?>

<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="<?=$path?>/js/reserveFunction.js"></script>
<script>
window.name = "register";
</script>

<h2 class="title_sub"> 가맹점 예약 관리 </h2>

<div class="list_line"></div>


<div id="list_state"> <span id="s_search"></span><?php echo "전체 : ".$num_rows; ?></div>
<table class="list_data" id="insert_data">
	<colgroup>
		<col style="width:3%;">
		<col style="width:10%;">
		<col style="width:17%;">
		<col style="width:10%;">
		<col style="width:15%;">
		<col style="width:15%;">
		<col style="width:5%;">
		<col style="width:10%;">
		<col style="width:8%;">
		<col style="width:7%;">
	</colgroup>
	<tr>
		<th>No.</th>
		<th>신청날짜</th>
		<th>가맹점상호</th>
		<th>고객 정보</th>
		<th>예약날짜</th>
		<th>예약가맹점</th>
		<th>예약인원</th>
		<th>상태</th>
		<th>지사</th>
		<th class="last">대리점</th>
	</tr>

<?php
while ($row = mysqli_fetch_assoc($result)) {
	$num--;
	
	// 상태
	$state_param = $row['state'];

	if ($state_param == "0") { // 접수대기
		$class = "";
		$state_num = 0;
		$state_text = "접수대기";
	}else if ($state_param == "1") { // 예약완료
		$class = "success";
		$state_num = 1;
		$state_text = "예약완료";
	}else if ($state_param == "2") { // 이용완료
		$class = "finish";
		$state_num = 2;
		$state_text = "이용완료";
	}else if ($state_param == "3") { // 고객취소
		$class = "fail";
		$state_num = 3;
		$state_text = "취소-고객취소";
	}else if ($state_param == "4") { // 전화두절
		$class = "fail";
		$state_num = 4;
		$state_text = "취소-전화두절";
	}else if ($state_param == "5") { // 가맹점취소
		$class = "fail";
		$state_num = 5;
		$state_text = "취소-가맹점취소";
	}else if ($state_param == "6") { // 테스트
		$class = "fail";
		$state_num = 6;
		$state_text = "취소-테스트";
	}

	$state = array("", "", "", "", "", "", "");
	$state[$state_num] = "selected";
	

	$hpno1 =  substr($row["hpno"], 0, 3);
	$hpno2 =  substr($row["hpno"], 3, 4);
	$hpno3 =  substr($row["hpno"], 7, 4);

	$hpno = $hpno1."-".$hpno2."-".$hpno3;

?>
<tr class="list_item insert_item <?=$class?>">
	<input type="hidden" name="idx" class="idx" value="<?=$row["idx"] ?>" />
	<td> <?=$num ?></td>
	<td><?=$row["credate"] ?></td>
	<td><?=$row["shop_name"] ?></td>
	<td><?=$row["name"] ?> <?=$hpno ?></td>
	<td><? echo date("Y-m-d H시i분", strtotime($row["r_date"].$row["r_time"])); ?></td>
	<td><?=$row["r_shop_name"] ?></td>
	<td><?=$row["person_num"] ?></td>
	<td>
		<?php // 상태
		if($auth == "manager" or $auth == 'center'){
		echo "
		<select class='state select_state' name='state' >
			<option value='0' ".$state[0].">접수대기</option>
			<option value='1' ".$state[1].">예약완료</option>
			<option value='2' ".$state[2].">이용완료</option>
			<option value='3' ".$state[3].">취소-고객취소</option>
			<option value='4' ".$state[4].">취소-전화두절</option>
			<option value='5' ".$state[5].">취소-가맹점취소</option>
			<option value='6' ".$state[6].">취소-테스트</option>
		</select>
		";
		}else if ($auth == "branch" || $auth == "agent"){
			echo $state_text;
		} ?>
		<input type="hidden" class="state" value="<?=$row['state']?>">
	</td>
	<td><?=$row["branch_name"] ?></td>
	<td class="last"><?=$row["agent_name"] ?></td>	
</tr>

<?php
}
/* free result set */
mysqli_free_result($result);
?>

</table>

<div class="page">
<a  href='shopPlus_reserve.php?page=1' class='page off'>◀</a>
<?php
for ($i=1; $i<=$total_pages; $i++) { 
	
	if($page == $i) $class = "on";
	else			$class = "off";
	
	if( ($i % 20) == 0 ) $br = "<br/>";
	else				 $br = "";

	?>
	<a href='shopPlus_reserve.php?page=<?=$i?>' class='<?=$class?>'><?=$i?></a> <?=$br?>
<?php } ?>
<a  href='shopPlus_reserve.php?page=<?=$total_pages?>' class='page off' >▶</a>
</div>

</body>
</html>