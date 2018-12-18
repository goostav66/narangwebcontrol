<?php session_start(); ?>
<?php 
header("Content-Type: text/html;charset=UTF-8");
include_once $_SERVER[DOCUMENT_ROOT].'/m/header.php';
include_once $_SERVER[DOCUMENT_ROOT].'/m/code/shop_exec.php';
?>
<?php
	$idx = $_GET['idx'];
	$isAgent = $_GET['isA'];
	$id = $_GET['id'];

	$result = ViewList_pop($connect,$idx);
	$r_result = ViewRoulette_pop($connect, $idx);
	$menu_result = getMenu($connect, $idx);

	$r_row =  mysqli_fetch_assoc($r_result); // 룰렛 데이터

	

?>
<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="<?=$path?>/js/shopFunctiontest.js"></script>

<h2 class="title_sub"> 가맹점 정보 수정 </h2>
<input type="hidden" name="h_search_type" id="h_search_type" value="<?=$_GET['s_type']?>">
<input type="hidden" name="h_search_text" id="h_search_text" value="<?=$_GET['s_text']?>">
<div class="pop_btn_set">
	<input type="button" name="btn_rgst" value="NFC" class="edit_btn" id="NFCButton">
	<input type="submit" name="btn_rgst" value="저장 / 수정" class="edit_btn" id="editButton">
	<input type="submit" name="btn_rgst" value="가맹점삭제" class="edit_btn" id="removeButton">
	<input type="button" name="btn_rgst" value="닫기" class="edit_btn" id="closeButton">
</div>

<?php

$chk_res = "";
$chk_free = "";
$chk_park = "";
$chk_seats = "";

while ($row = mysqli_fetch_assoc($result)) {
	
	if($row['isReserve'] != 0) $chk_res = "checked='checked'";
	if($row['isFree_cds'] != 0) $chk_free = "checked='checked'";
	if($row['isParking'] != 0) $chk_park = "checked='checked'";
	if($row['isSeats'] != 0) $chk_seats = "checked='checked'";

?>
<table class="list_data">
	<colgroup>
		<col style="width:30%;">
		<col align="left" style="width:70%;" >
	</colgroup>
	<tr class="pop_list_item">
		<th>상점 전경</th>
		<td>
		<?php
			if(isset($row['photo_url']) && !empty($row['photo_url']) ){
				echo "<form id='uploadimage_store' method='post' enctype='multipart/form-data'>"
				."<input type='hidden' name='shop_img_idx' value='".$idx."'>"
				."<h4 id='loading' >
					업로드 가능한 확장자 : jpg, jpeg, png<br/>
					1000kb 이하의 파일만 업로드 가능합니다.
				</h4>"
				."<div id='message'></div>
				<div id='image_preview'><img id='previewing' src='".$row['photo_url']."' width='300px'/></div>
				<input type='file' name='file' id='file' required />
				<input type='submit' value='Upload' class='edit_btn' />
				</form>";
			}else{
				echo "<form id='uploadimage_store' method='post' enctype='multipart/form-data'>"
				."<input type='hidden' name='shop_img_idx' value='".$idx."'>"
				."<h4 id='loading' >
					업로드 가능한 확장자 : jpg, jpeg, png <br/>
					1000kb 이하의 파일만 업로드 가능합니다.
				</h4>"
				."<div id='message'></div>
				<div id='image_preview'><img id='previewing' src='upload/noimage.png' /></div>
				<input type='file' name='file' id='file' required />
				<input type='submit' value='Upload' class='edit_btn' />
				</form>";
			}
		?>
		</td>
	</tr>
<form method="post" id="shop_edit_form" name="shop_edit_form">
<input type="hidden" name="edit_idx" value="<?=$idx?>" id="h_idx">
<input type="hidden" name="edit_id" value="<?=$id?>" id="h_id">
	<tr class="pop_list_item">
		<th>가맹점명 / 업소분류</th>
		<td><input type="text" name="shop_name" class="input_text" value="<?=$row['shop_name']?>">
			<select id="store_type" name="store_type">
				<?
				$type = array("노래방", "한식/일식", "퓨전주점", "유흥주점");
				
				for($i = 1; $i < 5 ; $i++){
					if($row['type'] == $i) $select = "selected";
					else $select = "";
					
					echo "<option value='".$i."' ".$select.">".$type[($i-1)]."</option>";	
					
				}
				
				?>
			</select>
			( 콜 수 : <?=$row['call_num']?> )
		</td>
	</tr>
	<tr class="pop_list_item">
		<th>가맹일</th>
		<td><input type="text" name="shop_date" class="input_text" value="<?=$row['shop_date']?>"></td>
	</tr>
	<tr class="pop_list_item">
		<th>고유 파라미터 (URL)</th>
		<td>http://m.nfczone.co.kr/nfc_index.jsp?p=<input type="text" name="url" class="input_text" value="<?=$row['url']?>"></td>
	</tr>
	<tr class="pop_list_item">
		<th>지사 / 센터</th>
		<td>
			<select id="branch_name_edit" name="branch_name_edit">
			  <option value="<?=$row['br_url']?>"><?=$row['branch_name']?></option>
			</select>
			<input type="hidden" name="isAgent" id="isAgent" value="<?=$isAgent?>" />
			<select id="agent_name_edit" name="agent_name_edit">

				<?php 
					if($isAgent == 'n'){
						$id = '';
					}
					$agent = getAgent_pop($connect, $row['br_url'], $id);
					while ($ag = mysqli_fetch_assoc($agent)) {
						if($ag['idx'] == $row['agent_idx'] ){
							
							$isSelect = "selected";
						}else $isSelect = "";

						echo "<option value='".$ag['idx']."' ".$isSelect.">".$ag["agent_name"]."</option>";	
					}
				?>

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
		<td><input type="text" name="position" class="input_text" value="<?=$row['position']?>" size="80"></td>
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
		<th>영업정보</th>
		<td>시간 : <input type="text" name="open_close" class="input_text" value="<?=$row['open_close']?>"><br/>
			영업일 관련 정보 : <input type="text" name="add_info" class="input_text" value="<?=$row['add_info']?>"
								size="50">
		</td>
	</tr>
	<tr class="pop_list_item">
		<th>추천메뉴</th>
		<td><input type="text" name="recom_menu" class="input_text" value="<?=$row['recom_menu']?>" size="50"></td>
	</tr>
	<tr class="pop_list_item">
		<th>한 줄 소개</th>
		<td><input type="text" name="intro_text" class="input_text" value="<?=$row['intro_text']?>" size="80"></td>
	</tr>
	<tr class="pop_list_item">
		<th>부가정보</th>
		<td>
			할인율<input type="text" name="discount" class="input_text" value="<?=$row['discount']?>" size="5">%
			<input type='checkbox' name='isReserve' value="<?=$row['isReserve']?>" <?=$chk_res?>>예약가능
			<input type='checkbox' name='isFree_cds' value="<?=$row['isFree_cds']?>" <?=$chk_free?>>대리운전 무상
			<input type='checkbox' name='isParking' value="<?=$row['isParking']?>" <?=$chk_park?>>주차시설
			<input type='checkbox' name='isSeats' value="<?=$row['isSeats']?>" <?=$chk_seats?>>단체석 완비	
		</td>
	</tr>
<? } ?>
	<tr class="pop_list_item">
		<th>룰렛정보 (개수입력)</th>
		<td>

			맥주1잔 <input type="text" name="beer_cup" class="input_text" value="<?=$r_row['beer_cup']?>" size="5" >
			맥주1병 <input type="text" name="beer_bottle" class="input_text" value="<?=$r_row['beer_bottle']?>" size="5">
			음료 <input type="text" name="drink" class="input_text" value="<?=$r_row['drink']?>" size="5"> <br/>
			안주1접시 <input type="text" name="food" class="input_text" value="<?=$r_row['food']?>" size="5">
			소주1병 <input type="text" name="soju" class="input_text" value="<?=$r_row['soju']?>" size="5">
			항공권 <input type="text" name="airplane" class="input_text" value="<?=$r_row['airplane']?>" size="5">

		</td>
	</tr>
</form>

	<tr class="pop_list_item">
		<th>메뉴정보</th>
		<td>
			<input type="button" name="btn_menu" value="메뉴등록" class="edit_btn" id="menuListButton">
		</td>
	</tr>

</table>
<?php
/* free result set */
mysqli_free_result($result);
?>

</body>
</html>