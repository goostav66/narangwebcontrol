<?php session_start(); ?>
<?php 
header("Content-Type: text/html;charset=UTF-8");
include_once $_SERVER[DOCUMENT_ROOT].'/m/nav.php';
include_once $_SERVER[DOCUMENT_ROOT].'/m/guest/guest_exec.php';
?>

<?php
	$result = msgList($connect);
	$num_rows = mysqli_num_rows($result);
	$num = $num_rows + 1; // 고객 수
?>

<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="<?=$path?>/js/fcmHelper.js"></script>

<form method="post" id="fcm_form" name="fcm_form">
<div class="messageWrapper">

  <textarea name="fcm_message" id="fcm_message" rows="4" cols="50" placeholder="메세지를 입력하세요"  required></textarea>
  <input type="submit" name="submit" value="Send" id="submitButton">
</div>

<div id="searchBar_hpno">
	<select id="search" name="search">
		<option value="shop_name">가맹점명</option>
		<option value="branch_name">지사</option>
		<option value="agent_name">대리점</option>
		<option value="shop_tel">가맹점 대표번호</option>
	</select>
	<input type="text" name="search_text" id="search_text">
	<input type="hidden" name="auth" id="auth" value="<?=$auth?>">
	<input type="hidden" name="search_id" id="search_id" value="<?=$id?>">
	<input type="button" name="btn_search" value="검색" id="btn_search">
	<br/>
</div>

<div id="list_state"> <span id="s_search"></span><?php echo "전체 : ".$num_rows; ?></div>
<table class="list_data">
	<colgroup>
		<col style="width:10%;">
		<col style="width:15%;">
		<col style="width:15%;">
		<col style="width:10%;">
		<col style="width:10%;">
		<col style="width:10%;">
		<col style="width:10%;">
		<col style="width:20%;">
	</colgroup>
	<tr>
		<th><input type="button" name="btn_all" value="전체선택" id="btn_all"></th>
		<th>고객 H.P</th>
		<th>목적지</th>
		<th>콜수</th>
		<th>등록일</th>
		<th>지사</th>
		<th>대리점</th>
		<th class="last"> 가맹점</th>

	</tr>

<?php
while ($row = mysqli_fetch_assoc($result)) {
	$num--;

	$hpno1 =  substr($row["hpno"], 0, 3);
	$hpno2 =  substr($row["hpno"], 3, 4);
	$hpno3 =  substr($row["hpno"], 7, 4);

	$hpno = $hpno1."-".$hpno2."-".$hpno3;
?>
<tr class="list_item">
	<td> <?php if($row["token"]){ 
		echo "<input type='checkbox' name='idx[]' value='".$row['hpno']."']> ";
		 } ?> 
	</td>
	<td> <?=$hpno ?></td>
	<td> <?=$row["dst_position"] ?></td>
	<td> <?=$row["cnt"]+$row["sum_call"] ?></td>
	<td> <?=$row["credate"]  ?></td>
	<td> <?=$row["branch_name"]  ?></td>
	<td> <?=$row["agent_name"]  ?></td>
	<td class="last"> <?=$row["shop_name"]  ?> </td>
	
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