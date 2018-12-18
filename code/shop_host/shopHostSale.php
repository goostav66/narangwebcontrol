<?php session_start(); ?>
<?php
header("Content-Type: text/html;charset=UTF-8");
include_once $_SERVER[DOCUMENT_ROOT].'/m/header.php';
include_once $_SERVER[DOCUMENT_ROOT].'/m/code/shop_host/shop_host_exec.php';
?>
<?php
	$url = $_GET['p'];
	$result = viewHostSale($connect,$url);
?>

<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="<?=$path?>/js/shopHostFunction.js?ver=1"></script>
<h2 class="title_sub"> 번개할인 등록/수정 </h2>


<div class="pop_btn_set">
	<input type="submit" name="btn_rgst" value="저장 / 수정" class="edit_btn" id="editButton_host">
	<input type="submit" name="btn_rgst" value="삭제" class="edit_btn" id="removeButton_host">
	<input type="button" name="btn_rgst" value="닫기" class="edit_btn" id="closeButton">
</div>
<?php
if($row = mysqli_fetch_assoc($result)){
	$day = explode(" ",$row['date_start']);
	$day_period = $day[0];
	$time_start = substr($day[1], 0, 5);
	$time_end = substr($row['date_end'], 11, 5);
	$etc = $row['etc'];
?>
<table class="list_data">
	<colgroup>
		<col style="width:30%;">
		<col align="left" style="width:70%;" >
	</colgroup>
   <form id="sale_edit_form" name="sale_edit_form" method="POST">
   <input type="hidden" name="edit_sale_idx" value="<?=$row['idx']?>" id="sale_idx"/>
   <input type="hidden" name="url" value="<?=$url?>"/>
    <tr class="pop_list_item">
    	<th>날짜</th>
        <td><input type="date" name="day_period" value="<?=$day_period?>"/></td>
    </tr>
    <tr class="pop_list_item">
    	<th>시간</th>
        <td><input type="time" name="time_start" value="<?=$time_start?>"/> ~ <input type="time" name="time_end" value="<?=$time_end?>"/></td>
    </tr>
    <tr class="pop_list_item">
    	<th>할인메뉴</th>
        <td><input type="text" name="menu" value="<?=$row['menu']?>"/></td>
    </tr>
    <tr class="pop_list_item">
    	<th>가격</th>
        <td>정가할인 <input type="number" name="dc_rate" value="<?=$row['dc_rate']?>"/>%</td>
    </tr>
    <tr class="pop_list_item">
    	<th>비고</th>
        <td><textarea name="etc"><?=$row['etc']?></textarea></td>
    </tr>
    </form>
</table>
<?php }
else{?>
<table class="list_data">
	<colgroup>
		<col style="width:30%;">
		<col align="left" style="width:70%;" >
	</colgroup>
    <form id="sale_edit_form" name="sale_edit_form" method="POST">
    <input type="hidden" name="rgst_sale_url" value="<?=$url?>" id="sale_url"/>
    <tr class="pop_list_item">
    	<th>날짜</th>
        <td><input type="date" name="day_period"/></td>
    </tr>
    <tr class="pop_list_item">
    	<th>시간</th>
        <td><input type="time" name="time_start"/> ~ <input type="time" name="time_end"/></td>
    </tr>
    <tr class="pop_list_item">
    	<th>할인메뉴</th>
        <td><input type="text" name="menu"/></td>
    </tr>
    <tr class="pop_list_item">
    	<th>가격</th>
        <td>정가할인 <input type="number" name="dc_rate" />%</td>
    </tr>
    <tr class="pop_list_item">
    	<th>비고</th>
        <td><textarea name="etc"></textarea></td>
    </tr>
    </form>
</table>


	<? } ?>
<?php
/* free result set */
mysqli_free_result($result);
?>
</body>
</html>
