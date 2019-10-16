<?php session_start(); ?>
<?php 
header("Content-Type: text/html;charset=UTF-8");
include_once $_SERVER[DOCUMENT_ROOT].'/m/nav.php';
include_once $_SERVER[DOCUMENT_ROOT].'/m/code/branch_exec.php';
?>

<?php

	$id = $_SESSION['id'];

	$s_type = $_GET['s_type'];
	$s_text = $_GET['s_text'];

	$isAgent = 'n';
	
	if($auth == 'manager')
		$result = getBranchList($connect);
	else{//관리자 외 : 페이지 이동

	}

	$num_rows = mysqli_num_rows($result);
	$num = $num_rows + 1; // 가맹점 수
?>

<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="<?=$path?>/js/branchFunction.js?ver=1"></script>

<h2 class="title_sub"> 지사관리 </h2>

<table class="list_data">
	<colgroup>
		<col style="width:15%;">
		<col style="width:15%;">
		<col style="width:15%;">
		<col style="width:20%;">
		<col style="width:20%;">
		<col style="width:15%;">
	</colgroup>

	<tr>
		<th>지사코드</th>
		<th>지사명</th>
		<th>대표자</th>
		<th>대표자 연락처</th>
		<th>콜센터 번호</th>
		<th class="last">등록</th>
	</tr>
    
	<tr class="list_item"><form method="post" id="form_rgst_branch">
		<td><input type="text" name="new_branch_code" class="input_text"></td>
        <td><input type="text" name="branch_name" class="input_text"></td>
		<td><input type="text" name="branch_ceo_name" class="input_text"></td>
		<td><input type="text" name="branch_ceo_phone" class="input_text"></td>
		<td><input type="text" name="call_center" class="input_text"></td>
		<td class="last"><button class="edit_btn" id="btn_regist_branch">등록</button></td>
	</form></tr>
</table>

<table class="list_data" id="insert_data">
	<colgroup>
		<col style="width:5%; ">
        <col style="width:8%; ">
		<col style="width:10%;">
		<col style="width:10%;">
		<col style="width:10%;">
		<col style="width:10%;">
		<col style="width:13%;">
		<col style="width:12%;">
		<col style="width:20%;">
	</colgroup>
	<tr>
		<th>No.</th>
        <th>지사코드</th>
		<th>지사</th>
		<th>ID</th>
		<th>대표자</th>
		<th>담당자</th>
		<th>담당자 연락처</th>
		<th>콜센터 번호</th>
		<th class="last">등록일</th>
	</tr>

<?php
while ($row = mysqli_fetch_assoc($result)) {
	$num--;
?>
	<tr class="list_item list_click insert_item">
		<input type="hidden" name="branch_idx" value="<?=$row["branch_idx"]?>" />
		<td><?=$num ?></td>
	    <td><?=$row["branch_code"]?></td>
		<td><?=$row["branch_name"]?></td>
		<td><?=$row["branch_id"]?></td>
		<td><?=$row["branch_ceo_name"]?></td>
		<td><?=$row["branch_manager_name"]?></td>
		<td><?=$row["branch_manager_phone"]?></td>
		<td><?=$row["call_center"]?></td>
	    <td><?=$row["branch_rgst_date"]?></td>
	</tr>

<?php
}
/* free result set */
mysqli_free_result($result);
?>

</table>

</body>
</html>