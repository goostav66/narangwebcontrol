<?php session_start(); ?>
<?php 
header("Content-Type: text/html;charset=UTF-8");
include_once $_SERVER[DOCUMENT_ROOT].'/m/nav.php';
include_once $_SERVER[DOCUMENT_ROOT].'/m/stats/stats_exec_test.php';
?>

<?php
	$auth = $_SESSION['auth'];
	$id = $_SESSION['id'];

	$yesterday = date("Y-m-d",strtotime("-1 day"));
	$today = date("Y-m-d");
	

	$result_viewList = ViewList($connect, $auth, $id);
	$num_rows = mysqli_num_rows($result_viewList);
	
	$result_viewList_today = ViewList_today($connect, $auth, $id, $today, $yesterday);

	$count_today = Count_today($connect, $auth, $id, $today, $yesterday);
	$result_today = mysqli_fetch_object($count_today);

	$count_all = Count_all($connect, $auth, $id);
	$result_all = mysqli_fetch_object($count_all);

	//-------------------------------------------------------------------------------------
	
	if($auth == 'manager' || $auth == 'branch'){
		$branch = getBranch($connect, $auth, $id);
		$isAgent = 'n';
	}elseif ($auth == 'agent'){
		$branch = getBranch($connect, $auth, $id);
		$isAgent = 'y';
	}

	
?>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="<?=$path?>/js/statsFunction_test.js"></script>

<h2 class="title_sub"> 통계 현황 </h2>

<style>
.stats_box_area {margin:0 auto; margin-bottom:50px; width:90%; border-collapse: separate;  border-spacing: 10px;}
.stats_box		{padding:10px; height:50px; border:1px solid #ccc; background:#fff}
.stats_box h1	{float:left; width:80%; line-height: 50px; border-right: 1px solid #ccc; text-overflow: ellipsis; overflow:hidden; white-space:nowrap}
.stats_box span	{display: inline-block; width:19%; line-height: 50px; text-align:center; font-size:20px; font-weight:bolder; color:#ffb5a7; overflow:hidden;}
.stats_text		{clear:both; margin:0 auto; margin-bottom:10px; width:90%; font-size:15px; font-weight:bolder; color:#414141;}
#search_bar		{margin:0 auto; margin-top:10px; margin-bottom:20px; width:90%; border:2px solid #fff; border-collapse: separate;  border-spacing: 10px; text-align:right}
#search_bar	td	{margin-bottom:0px}
#search_bar	td input {margin-left:5px; margin:0; vertical-align:middle; }
#search_bar	td select{margin-left:5px; margin-top:3px; vertical-align:middle; height:24px; line-height:20px;}
#date_search_form	{float:right; margin-left:5px;}
.list_data		{margin-bottom:10px}
</style>

<table class="stats_box_area">
	<colgroup>
		<col style="width:50%;">
		<col style="width:50%;">
	</colgroup>
	<tr>
		<td class="stats_box">
			<h1>전날 집계 ( <?=$yesterday."~".$today?> )</h1>
			<span><? echo $result_today->cnt+$result_today->add_sum; ?></span>
		</td>
		<td class="stats_box">
			<h1>전체 ( <?=$result_all->min_date?> ~ <?=$result_all->max_date?> )</h1>
			<span><? echo $result_all->cnt+$result_all->add_sum;?></span>
		</td>
	</tr>
</table>
<input type="hidden" name="auth" id="auth" value="<?=$auth?>">
<input type="hidden" name="search_id" id="search_id" value="<?=$id?>">

<div class="stats_text">전날 집계 현황 (  <?=$yesterday."~".$today?>  )</div>
<table class="list_data">
	<colgroup>
		<col style="width:20%;">
		<col style="width:20%;">
		<col style="width:20%;">
		<col style="width:20%;">
		<col style="width:20%;">
	</colgroup>
	<tr>
		<th>집계 일자</th>
		<th>지사</th>
		<th>대리점</th>
		<th>가맹점상호</th>
		<th class="last">완료 콜수</th>
	</tr>
	
<?php
while ($row = mysqli_fetch_assoc($result_viewList_today)) {
	$num += $row['cnt']+$row['add_sum'];


?>
	
	<tr class="list_item">

		<td> <?=$row['mydate'] ?></td>
		<td> <? echo !empty($row['branch_name'])? $row['branch_name'] : "본사콜"; ?></td>
		<td> <? echo !empty($row['agent_name'])? $row['agent_name'] : "-"; ?></td>
		<td> <? echo !empty($row['shop_name'])? $row['shop_name'] : "-"; ?></td>
		<td class="last"> <? echo $row['cnt']+$row['add_sum']; ?></td>
		
	</tr>

<?php
}
/* free result set */
mysqli_free_result($result_viewList_today);
?>
	<tr class="list_item">

		<td colspan="4" align="center">전체합계</td>
		<td class="last"> <?=$num ?></td>
	</tr>

</table>

<div class="list_line"></div>

<table id="search_bar" >
	<tr>
		<td>
			<form method="post" id="date_search_form" name="date_search_form">

				<select id='branch' name='branch'>
				<?php if($auth == 'manager') {
							echo "<option value='0'>지사 선택</option>";
							 $selected = "";
						}else if($auth == 'manager') {
							$selected = "selected='selected'";
						}else { $selected = ""; }
				?>
				<?php 
					while ($br = mysqli_fetch_assoc($branch)) {
						if($auth == "manager" or $auth == "branch"){
							echo "<option value='".$br['url']."'".$selected.">".$br["branch_name"]."</option>";
						}else if($auth == "agent"){
							echo "<option value='".$br['br_url']."'".$selected.">".$br["branch_name"]."</option>";
						}
					}
				?>
				</select>
			
				<input type="hidden" name="isAgent" id="isAgent" value="<?=$isAgent?>" />
				<select id="agent" name="agent">
					<option value="0">대리점 선택</option>
				</select>

				<select id="shop" name="shop">
					<option value="0">가맹점 선택</option>
				</select>
				
				<input type="date" name="date_before_stats" id="date_before" value="<?=date("Y-m-d", strtotime($result_all->min_date))?>"> ~ 
				<input type="date" name="date_after_stats" id="date_after" value="<?=$today ?>">
				<input type="hidden" name="search_id" id="search_id" value="<?=$id?>">
				<input type="hidden" name="search_stats" id="search_stats" value="1">
				<input type="button" name="btn_search" value="검색" id="btn_search_stats" class="edit_btn">
			</form>
		</td>
	</tr>
	<tr>
		<td>
			<table class="list_data insert_data"  style="display:none; width:100%">
				<tr>
					<th>날짜</th>
					<th>지사</th>
					<th>대리점</th>
					<th>가맹점상호</th>
					<th>모바일웹 콜</th>
					<th>어플 콜</th>
					<th class="last">완료 콜수</th>
				</tr>
			</table>
		</td>
	</tr>
</table>



<div class="stats_text">가맹점별 통계 현황 ( 전체 ) 
	<input type="button" name="list_view" value="접기" class="list_view edit_btn">
	<input type="button" name="list_view" value="보기" class="list_view edit_btn" style="display:none">
</div>
<table class="list_data list_all" style="display:block">
	<colgroup>
		<col style="width:5%;">
		<col style="width:20%;">
		<col style="width:20%;">
		<col style="width:20%;">
		<col style="width:20%;">
	</colgroup>
	<tr>
		<th>No.</th>
		<th>지사</th>
		<th>대리점</th>
		<th>가맹점상호</th>
		<th class="last">완료 콜수</th>
	</tr>
	
<?php
$num_rows += 1 ;
$call_num = 0;
while ($row = mysqli_fetch_assoc($result_viewList)) {
	$num_rows--;
	$call_num += ($row['cnt']+$row['add_sum']);
?>
	
	<tr class="list_item list_click">
		<input type="hidden" name="idx" id="shop_idx" value="<?=$row["idx"] ?>" />
		<td> <?=$num_rows ?></td>
		<td> <? echo !empty($row['branch_name'])? $row['branch_name'] : "본사콜"; ?></td>
		<td> <? echo !empty($row['agent_name'])? $row['agent_name'] : "-"; ?></td>
		<td> <? echo !empty($row['shop_name'])? $row['shop_name'] : "-"; ?></td>
		<td class="last"> <? echo $row['cnt']+$row['add_sum']; ?></td>
		
	</tr>

<?php
}
/* free result set */
mysqli_free_result($result_viewList);
?>
	<tr class="list_item">

		<td colspan="4" align="center">전체합계</td>
		<td class="last"> <?=$call_num ?></td>
	</tr>

</table>

</body>
</html>
