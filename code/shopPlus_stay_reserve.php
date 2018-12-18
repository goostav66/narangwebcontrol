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

	

	if(isset($s_type) && !empty($s_type) && isset($s_text) && !empty($s_text)){
		
		$result = searchShop_re($connect, $s_type, $s_text, $auth, $id);

		$branch = getBranch_M($connect);
		if ($auth == 'agent'){			
			$isAgent = 'y';
		}
	
		$num_rows = mysqli_num_rows($result);
		$num = $num_rows + 1; // 가맹점 수

	}else {
		
		$branch = getBranch($connect, $auth, $id);
		$result = ViewList($connect, $auth, $id);

		if ($auth == 'agent'){
			$isAgent = 'y';
		}
	
		$num_rows = mysqli_num_rows($result);
		$num = $num_rows + 1; // 가맹점 수

		
	}
	

?>

<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="<?=$path?>/js/shopFunctiontest.js"></script>
<script>
window.name = "register";
</script>

<h2 class="title_sub"> 가맹점 예약 관리 </h2>

<div class="list_line"></div>


<div id="list_state"> <span id="s_search"></span><?php echo "전체 : ".$num_rows; ?></div>
<table class="list_data" id="insert_data">
	<colgroup>
		<col style="width:3%;">
		<col style="width:12%;">
		<col style="width:10%;">
		<col style="width:20%;">
		<col style="width:25%;">
		<col style="width:5%;">
		<col style="width:10%;">
		<col style="width:8%;">
		<col style="width:7%;">
	</colgroup>
	<tr>
		<th>No.</th>
		<th>가맹점상호</th>
		<th>고객 H.P</th>
		<th>예약날짜</th>
		<th>예약메세지</th>
		<th>예약인원</th>
		<th>상태</th>
		<th>지사</th>
		<th class="last">대리점</th>
	</tr>

<?php
while ($row = mysqli_fetch_assoc($result)) {
	$num--;


?>
<tr class="list_item insert_item">
	<input type="hidden" name="idx" id="shop_idx" value="<?=$row["idx"] ?>" />
	<td> <?=$num ?></td>
	<td> </td>
	<td> </td>
	<td> </td>
	<td> </td>
	<td> </td>
	<td> </td>
	<td class="last"> </td>	
</tr>

<?php
}
/* free result set */
mysqli_free_result($result);
?>

</table>


</body>
</html>