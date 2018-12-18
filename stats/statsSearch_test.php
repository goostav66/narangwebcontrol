<?php session_start(); ?>
<?php 
header("Content-Type: text/html;charset=UTF-8");
include_once $_SERVER[DOCUMENT_ROOT].'/m/header.php';
include_once $_SERVER[DOCUMENT_ROOT].'/m/stats/stats_exec_test.php';
?>

<?php
	$idx = $_GET['idx'];
	$id = $_GET['id'];
	$auth = $_GET['auth'];

	// 페이지네이션
	$num_rec_per_page = 10;
	if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=1; }; 
	$start_from = ($page-1) * $num_rec_per_page; 
	// 페이지네이션
	
	$yesterday = date("Y-m-d",strtotime("-1 day"));
	$today = date("Y-m-d");
	
	// $num_rec_per_page로 계산한 만큼 리스트 가져오기
	$result_viewList = ViewList_pop($connect, $idx,  $start_from, $num_rec_per_page);

	// 총 리스트 갯수
	$total_records = ViewList_count($connect, $idx);
	$total_pages = ceil($total_records / $num_rec_per_page); 

	$count_all = Count_all($connect, $auth, $id);
	$result_all = mysqli_fetch_object($count_all);

	$result_info = StoreInfo($connect, $idx);

?>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="<?=$path?>/js/statsFunction.js"></script>
<?php while ($row = mysqli_fetch_assoc($result_info)) { ?>
<h2 class="title_sub"><?=$row['shop_name']?></h2>

<style>
.stats_box_area {margin:0 auto; margin-bottom:15px; width:90%; }
.stats_box		{float: left; margin: 10px; padding:10px; width:45%; height:50px; border:1px solid #ccc; background:#fff}
.stats_box h1	{float:left; width:80%; line-height: 50px; border-right: 1px solid #ccc; text-overflow: ellipsis; overflow:hidden; white-space:nowrap}
.stats_box span	{display: inline-block; width:19%; line-height: 50px; text-align:center; font-size:20px; font-weight:bolder; color:#ffb5a7; overflow:hidden;}
.stats_text		{margin:0 auto; margin-bottom:10px; width:90%; font-size:15px; font-weight:bolder; color:#414141;}
.store_info		{margin:0 auto; line-height:30px; text-align:right}
.store_info	img	{vertical-align:middle}
.search_area	{margin:0 auto; width:90%;}
.list_data		{margin-bottom:10px}
</style>

<table class="search_area">
	<tr>
		<td>
			<div id="">
				<form method="post" id="date_search_form" name="date_search_form">
					<input type="hidden" name="idx" id="idx" value="<?=$idx?>">
					<input type="date" name="date_before" id="date_before" value="<?=date("Y-m-d", strtotime($row['min_date']))?>"> ~ 
					<input type="date" name="date_after" id="date_after" value="<?=$today ?>">
					<input type="hidden" name="search_id" id="search_id" value="<?=$id?>">
					<input type="button" name="btn_search" value="검색" id="btn_search" class="edit_btn">
				</form>
				<br/>
			</div>
		</td>
		<td>
			<div class="store_info"> [ 콜 현황 ] 
				<img src="../images/icon_call_ok.png" width="20px"/>완료콜 : <?=$row['s_cnt']?> / 
				<img src="../images/icon_call_all.png" width="20px"/>전체콜 : <?=$row['cnt']?>
			</div>
		</td>
		<td style="text-align:right">
			<input type="button" name="btn_search" value="전체보기" id="btn_all" class="edit_btn">
			<input type="button" name="btn_search" value="완료콜 보기" id="btn_ok" class="edit_btn">
		</td>
	</tr>
</table>

<? } ?>
<table class="list_data">
	<colgroup>
		<col style="width:3%;">
		<col style="width:20%;">
		<col style="width:21%;">
		<col style="width:15%;">
		<col style="width:15%;">
		<col style="width:3%;">
		<col style="width:3%;">
		<col style="width:5%;">
		<col style="width:5%;">
		<col style="width:5%;">
	</colgroup>
	<tr>
		<th>No.</th>
		<th>고객 H.P</th>
		<th>호출시간</th>
		<th>출발지</th>
		<th>목적지</th>
		<th>경유지</th>
		<th>추가콜</th>
		<th>요금</th>
		<th>규정 요금</th>
		<th class="last">상태</th>
	</tr>

<?php
// No. 
if ($page == 1) $num = $total_records + 1;
else			$num = ($total_records + 1) - (($page-1) * 10);

while ($row = mysqli_fetch_assoc($result_viewList)) {
	$num--;
	
	// 상태
	$state_param = $row['state'];

	if ($state_param == "S") { // 완료
		$class = "success";
		$state_num = 1;
		$state_text = "완료";
	}else if ($state_param == "C") { // 최소
		$class = "fail";
		$state_num = 2;
		$state_text = "취소";
	}
	else if ($state_param == "O") { //접수 대기
		$class = "";
		$state_num = 0;
		$state_text = "접수대기";
	}
	
	$hpno1 =  substr($row["hpno"], 0, 3);
	$hpno2 =  substr($row["hpno"], 3, 4);
	$hpno3 =  substr($row["hpno"], 7, 4);

	$hpno = $hpno1."-".$hpno2."-".$hpno3;
	
	if( isset($row["current_position"]) && !empty($row["current_position"]) ){
		$position = $row["current_position"];
	}
	else if($row["current_position"] == null or $row["current_position"] == ""){
		$position = $row["position"];
	}
	
	//어플과 모바일웹 구분하는 아이콘
	$icon = "";
	if ( isset($row['url']) && !empty($row["url"]) ){
		$icon = "icon_call_02.png";
	}else {$icon = "icon_call_01.png";}

		

?>
<tr class="list_item <?=$class?>">
	<td> <?=$num ?><img src="../images/<?=$icon?>" width="50px"/></td>
	
	<td style="font-weight:bolder"> <?= $hpno ?></td>
	<td> <?=$row["credate"] ?></td>
	<td><?= $position?></td>
	
	<td> <?=$row["dst_position"]?></td>
	<td><?=$row["mid_pass"]?></td>
	<td><?=$row["add_call"]?></td>
	<td> <?=$row["price"]?></td>
	
	<td> <?=$row["rule_price"] ?></td>
	<td> <?=$state_text?></td>
	
</tr>

<?php
}
/* free result set */
mysqli_free_result($page_result);
?>

</table>

<div class="page">
<a  href='statsSearch.php?idx=<?=$idx?>&id=<?=$id?>&auth=<?=$auth?>&page=1' class='page off'>◀</a>
<?php
for ($i=1; $i<=$total_pages; $i++) { 
	
	if($page == $i) $class = "on";
	else			$class = "off";
	

	if( ($i % 10) == 0 ) $br = "<br/>";
	else				 $br = "";

	?>
	<a href='statsSearch.php?idx=<?=$idx?>&id=<?=$id?>&auth=<?=$auth?>&page=<?=$i?>' class='<?=$class?>'><?=$i?></a> <?=$br?>
<?php } ?>
<a  href='statsSearch.php?idx=<?=$idx?>&id=<?=$id?>&auth=<?=$auth?>&page=<?=$total_pages?>' class='page off' >▶</a>
</div>


</body>
</html>
