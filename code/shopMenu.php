<?php session_start(); ?>
<?php 
header("Content-Type: text/html;charset=UTF-8");
include_once $_SERVER[DOCUMENT_ROOT].'/m/header.php';
include_once $_SERVER[DOCUMENT_ROOT].'/m/code/shop_exec.php';
?>
<?php
	$idx = $_GET['idx'];
?>
<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="<?=$path?>/js/shopFunction.js"></script>

<h2 class="title_sub"> 가맹점 메뉴 등록 </h2>
<form method="post" id="shop_edit_form" name="shop_edit_form">
<input type="hidden" name="edit_idx" value="<?=$idx?>" id="h_idx">
<div class="pop_btn_set">
<input type="submit" name="btn_rgst" value="저장 / 수정" class="edit_btn" id="editButton">
<input type="submit" name="btn_rgst" value="닫기" class="edit_btn" id="closeButton">
</div>
<?php

while ($row = mysqli_fetch_assoc($result)) {
	
?>
<table class="list_data">
	<colgroup>
		<col style="width:30%;">
		<col align="left" style="width:70%;" >
	</colgroup>
	<tr class="pop_list_item">
		<th>가맹점명</th>
		<td><input type="text" name="shop_name" class="input_text" value="<?=$row['shop_name']?>">
			( 콜 수 : <?=$row['call_num']?> )
		</td>
	</tr>
	<tr class="pop_list_item">
		<th>가맹일</th>
		<td><input type="text" name="shop_date" class="input_text" value="<?=$row['shop_date']?>"></td>
	</tr>
	<tr class="pop_list_item">
		<th>고유 파라미터 (URL)</th>
		<td><input type="text" name="url" class="input_text" value="<?=$row['url']?>"></td>
	</tr>
	<tr class="pop_list_item">
		<th>지사 / 센터</th>
		<td>
			<select id="branch_name" name="branch_name">
			  <option value="0"><?=$row['branch_name']?></option>
			</select>
			<select id="agent_name" name="agent_name">
			  <option value="0"><?=$row['agent_name']?></option>
			</select>
		</td>
	</tr>
	<tr class="pop_list_item">
		<th>대표자</th>
		<td><input type="text" name="ceo_name" class="input_text" value="<?=$row['ceo_name']?>"></td>
	</tr>
	<tr class="pop_list_item">
		<th>번호</th>
		<td>대표 번호 : <input type="text" name="shop_tel" class="input_text" value="<?=$row['shop_tel']?>"><br/>
			가맹점 H.P : <input type="text" name="shop_phone" class="input_text" value="<?=$row['shop_phone']?>">
		</td>
	</tr>
	<tr class="pop_list_item">
		<th>주소(출발지)</th>
		<td><input type="text" name="position" class="input_text" value="<?=$row['position']?>"></td>
	</tr>
	<tr class="pop_list_item">
		<th>GPS</th>
		<td><input type="text" name="lat" class="input_text" value="<?=$row['lat']?>">
			<input type="text" name="lon" class="input_text" value="<?=$row['lon']?>">
		</td>
	</tr>
	<tr class="pop_list_item">
		<th>등록일</th>
		<td><input type="text" name="rgst_date" class="input_text" value="<?=$row['rgst_date']?>">
			(정보 수정일 : <?=$row['edit_date']?>)
		</td>
	</tr>
	<tr class="pop_list_item">
		<th>상점 전경</th>
		<td>파일업로드</td>
	</tr>
	<tr class="pop_list_item">
		<th>영업정보</th>
		<td>시간 : <input type="text" name="open_close" class="input_text" value="<?=$row['open_close']?>"><br/>
			영업일 관련 정보 : <input type="text" name="add_info" class="input_text" value="<?=$row['add_info']?>">
		</td>
	</tr>
	<tr class="pop_list_item">
		<th>추천메뉴</th>
		<td><input type="text" name="recom_menu" class="input_text" value="<?=$row['recom_menu']?>"></td>
	</tr>
	<tr class="pop_list_item">
		<th>한 줄 소개</th>
		<td><input type="text" name="intro_text" class="input_text" value="<?=$row['intro_text']?>"></td>
	</tr>
	<tr class="pop_list_item">
		<th>부가정보</th>
		<td>
			할인율<input type="text" name="discount" class="input_text" value="<?=$row['discount']?>">%
			<input type='checkbox' name='isReserve' value="<?=$row['isReserve']?>" <?=$chk_res?>>예약가능
			<input type='checkbox' name='isFree_cds' value="<?=$row['isFree_cds']?>" <?=$chk_free?>>대리운전 무상
			<input type='checkbox' name='isParking' value="<?=$row['isParking']?>" <?=$chk_park?>>주차시설
			<input type='checkbox' name='isSeats' value="<?=$row['isSeats']?>" <?=$chk_seats?>>단체석 완비	
		</td>
	</tr>
	<tr class="pop_list_item">
		<th>룰렛정보(준비중)</th>
		<td><input type="text" name="roulette" class="input_text" value=""></td>
	</tr>
	<tr class="pop_list_item">
		<th>메뉴정보(준비중)</th>
		<td><input type="text" name="menu" class="input_text" value=""></td>
	</tr>

</table>
<?php
}
/* free result set */
mysqli_free_result($result);
?>

</body>
</html>