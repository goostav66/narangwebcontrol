<?php session_start(); ?>
<?php 
header("Content-Type: text/html;charset=UTF-8");
include_once $_SERVER[DOCUMENT_ROOT].'/m/nav.php';
include_once $_SERVER[DOCUMENT_ROOT].'/m/code/user_exec.php';
?>

<?php
	$result = ViewList($connect);
	//$branch = getBranch($connect);
	$num_rows = mysqli_num_rows($result);
	$num = $num_rows + 1; // 고객 수
?>

<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="<?=$path?>/js/userFunction.js"></script>

<h2 class="title_sub"> 회원 관리 </h2>
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
		<col style="width:20%;">
		<col style="width:20%;">
		<col style="width:20%;">
		<col style="width:20%;">
	</colgroup>
	<tr>
		<th>No.</th>
		<th>아이디</th>
		<th>이름</th>
		<th>권한</th>
		<th class="last">등록일</th>
	</tr>

<?php
while ($row = mysqli_fetch_assoc($result)) {
	$num--;
?>
<tr class="list_item list_click">
	<input type="hidden" name="idx" id="shop_idx" value="<?=$row["idx"] ?>" />
	<td> <?=$num ?></td>
	<td> <?=$row["id"] ?></td>
	<td> <?=$row["name"] ?></td>
	<td> <?=$row["auth"]?></td>
	<td class="last"><?=$row["rgst_date"]  ?></td>	
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