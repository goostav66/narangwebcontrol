<?php session_start(); ?>
<?php 
header("Content-Type: text/html;charset=UTF-8");
include_once $_SERVER[DOCUMENT_ROOT].'/m/nav.php';
include_once $_SERVER[DOCUMENT_ROOT].'/m/code/tag_exec.php';
?>

<?php
	$id = $_SESSION['id'];

	$result = ViewList($connect, $auth, $id);

	//$branch = getBranch($connect);
	$num_rows = mysqli_num_rows($result);
	$num = $num_rows + 1; // 고객 수
?>

<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="<?=$path?>/js/tagFunction.js"></script>

<h2 class="title_sub"> 태그 ID 관리 </h2>
<form method="post" id="shop_rgst_form" name="shop_rgst_form">

<div class="list_line"></div>


<div id="searchBar_hpno">
* 검색기능 준비 중
	<select id="search" name="search">
		<option value="shop_name">가맹점명</option>
		<option value="hpno">휴대폰 번호</option>
	</select>
	<input type="text" name="search_text" id="search_text">
	<input type="hidden" name="search_id" id="search_id" value="<?=$id?>">
	<input type="button" name="btn_search" value="검색" id="btn_search">
	<br/>
</div>

<div id="list_state"> <span id="s_search"></span><?php echo "전체 : ".$num_rows; ?></div>
<table class="list_data" id="insert_data">
	<colgroup>
		<col style="width:5%;">
		<?php if ($auth == "manager" or $auth == "branch"){
		echo "<col style='width:10%;'>
				<col style='width:10%;'>";
		}?>
		<col style="width:10%;">
		<col style="width:10%;">
		<col style="width:10%;">
		<col style="width:20%;">
		<col style="width:20%;">
		<col style="width:5%;">

	</colgroup>
	<tr>
		<th>No.</th>
		<?php if ($auth == "manager" or $auth == "branch") echo "<th>대리점이름</th>"; ?>
		<?php if ($auth == "manager" or $auth == "branch") echo "<th>대리점 ID</th>"; ?>
		<th>가맹점이름</th>
		<th>TAG ID</th>
		<th>고객 H.P</th>
		<th>멤버쉽 발급일</th>
		<th>고객 등록일</th>
		<th class="last">비고</th>
	</tr>

<?php
while ($row = mysqli_fetch_assoc($result)) {
	$num--;

	$hpno1 =  substr($row["hpno"], 0, 3);
	$hpno2 =  substr($row["hpno"], 3, 4);
	$hpno3 =  substr($row["hpno"], 7, 4);

	$hpno = $hpno1."-".$hpno2."-".$hpno3;
?>
<tr class="list_item list_click">
	<input type="hidden" name="idx" id="shop_idx" value="<?=$row["idx"] ?>" />
	<td> <?=$num ?></td>
	<?php if ($auth == "manager" or $auth == "branch") echo "<td>".$row["agent_name"]."</td>"; ?>
	<?php if ($auth == "manager" or $auth == "branch") echo "<td>".$row["id"]."</td>"; ?>
	<td> <?=$row["shop_name"] ?></td>
	<td> <?=$row["serialNum"]?></td>
	<td> <?=$hpno?></td>
	<td> <?=$row["insertDate"] ?></td>
	<td> <?=$row["credate"] ?></td>
	<td class="last"><?=$row["serialText"]  ?></td>	
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