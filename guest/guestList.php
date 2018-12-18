<?php session_start(); ?>
<?php 
header("Content-Type: text/html;charset=UTF-8");
include_once $_SERVER[DOCUMENT_ROOT].'/m/nav.php';
include_once $_SERVER[DOCUMENT_ROOT].'/m/guest/guest_exec.php';
?>

<?php
	$result = ViewList($connect);
	$num_rows = mysqli_num_rows($result);
	$num = $num_rows + 1; // 고객 수
?>

<h2 class="title_sub"> 고객리스트 </h2>

<form action="push_notification.php" method="post">

<div id="list_state"> <span id="s_search"></span><?php echo "전체 누적고객 : ".$num_rows; ?></div>
<table class="list_data">
	<colgroup>
		<col style="width:5%;">
		<col style="width:8%;">
		<col style="width:7%;">
		<col style="width:10%;">
		<col style="width:10%;">
		<col style="width:10%;">
		<col style="width:10%;">
		<col style="width:10%;">
		<col style="width:15%;">
		<col style="width:15%;">
	</colgroup>
	<tr>
		<th>No.</th>
		<th>호출경로</th>
		<th>지사</th>
		<th>대리점</th>
		<th>가맹점</th>
		<th>고객 H.P</th>
		<th>최근 목적지</th>
		<th>콜수</th>
		<th class="last">등록일</th>

	</tr>

<?php
while ($row = mysqli_fetch_assoc($result)) {
	$num--;

	$hpno1 =  substr($row["hpno"], 0, 3);
	$hpno2 =  substr($row["hpno"], 3, 4);
	$hpno3 =  substr($row["hpno"], 7, 4);

	$hpno = $hpno1."-".$hpno2."-".$hpno3;

	if(isset($row['url']) && !empty($row['url'])) $type = "모바일웹";
	else $type = "어플";
?>
<tr class="list_item list_click">
	<td> <?=$num ?></td>
	<td> <?=$type ?></td>
	<td> <?=$row["branch_name"] ?></td>
	<td> <?=$row["agent_name"] ?></td>
	<td> <?=$row["shop_name"] ?></td>
	<td> <?=$hpno ?></td>
	<td> <?=$row["dst_position"] ?></td>
	<td> <?=$row["cnt"]+$row["sum_call"] ?></td>
	<td class="last"> <?=$row["credate"]  ?> </td>
	
</tr>

<?php
}
/* free result set */
mysqli_free_result($result);
?>




</table>
</form>

</body>
</html>
