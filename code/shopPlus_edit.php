<?php session_start(); ?>
<?php 
header("Content-Type: text/html;charset=UTF-8");
include_once $_SERVER[DOCUMENT_ROOT].'/m/header.php';
include_once $_SERVER[DOCUMENT_ROOT].'/m/code/shopPlus_exec.php';
?>
<?php

//error_reporting(E_ALL);
//ini_set("display_errors", 1);

	$idx = $_GET['idx'];
	$isAgent = $_GET['isA'];
	$id = $_GET['id'];
	$type = $_GET['type'];

	$result = ViewList_pop($connect,$idx);
	$price_result = getPrice($connect, $idx, 0);

?>
<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="<?=$path?>/js/shopFunctiontest.js"></script>

<h2 class="title_sub"> 가맹점 정보 수정 </h2>
<form id="frmSearch">
<input type="hidden" name="h_search_type" id="h_search_type" value="<?=$_GET['s_type']?>">
<input type="hidden" name="h_search_text" id="h_search_text" value="<?=$_GET['s_text']?>">
</form>

<div class="pop_btn_set">
	<input type="button" name="btn_rgst" value="NFC" class="edit_btn" id="NFCButton">
	<input type="submit" name="btn_rgst" value="저장 / 수정" class="edit_btn" id="editButton">
	<input type="submit" name="btn_rgst" value="가맹점삭제" class="edit_btn" id="removeButton">
	<input type="button" name="btn_rgst" value="닫기" class="edit_btn" id="closeButton">
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
		<th>상점 전경</th>
		<td>
		<?php
			if(isset($row['photo_url']) && !empty($row['photo_url']) ){
				echo "<form id='uploadimage_store' method='post' enctype='multipart/form-data'>"
				."<input type='hidden' name='shop_img_idx' value='".$idx."'>"
				."<h4 id='loading' >
					업로드 가능한 확장자 : jpg, jpeg, png, gif<br/>
					(* 사진파일 업로드 용량 제한을 개선하였습니다)
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
					업로드 가능한 확장자 : jpg, jpeg, png, gif, bmp<br/>
					(* 사진파일 업로드 용량 제한을 개선하였습니다)
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
		<th>가맹점명</th>
		<td><input type="text" name="shop_name" class="input_text" value="<?=$row['shop_name']?>"></td>
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
		</td>
	</tr>
	<tr class="pop_list_item">
		<th>주소</th>
		<td><input type="text" name="position" class="input_text" value="<?=$row['position']?>" size="80"></td>
	</tr>
	<tr class="pop_list_item">
		<th>등록일</th>
		<td><input type="text" name="rgst_date" class="input_text" value="<?=$row['rgst_date']?>"></td>
	</tr>
	<tr class="pop_list_item">
		<th>한 줄 소개</th>
		<td><input type="text" name="intro_text" class="input_text" value="<?=$row['intro_text']?>" size="80"></td>
	</tr>
	<tr class="pop_list_item">
		<th>상세 정보</th>
		<td><textarea rows='10' cols='80' name="info" class="input_text" ><?=$row['info']?></textarea></td>
	</tr>
	
<? } ?>
	

<?php
while ($row = mysqli_fetch_assoc($price_result)) {

	if ($row['name'] == 'time'){
		$time_price = $row['price'];
		$info = $row['info'];
	}else if ($row['name'] == 'day'){
		$day_price = $row['price'];
	}
 } ?>
	<tr class="pop_list_item">
		<th>기본 가격정보<br/><br/>(리스트에 표시되는 기본가격정보 입니다.)</th>
		<td>
			대실 : <input type="text" name="time_stay" class="input_text" value="<?=$time_price?>">원 <br/>
			대실 부가설명 :  
					 <input type="text" name="time_stay_info" class="input_text" value="<?=$info?>">
					 ( * 부가설명 예시 : 최대 4시간, 24:00까지 ) <br/>
			숙박 : <input type="text" name="day_stay" class="input_text" value="<?=$day_price?>">원 <br/>

			<input type="button" name="btn_menu" value="상세가격등록" class="edit_btn" id="menuListButton">
		</td>
	</tr>
	<tr class="pop_list_item">
		<th>사진</th>
		<td>
			<input type="button" name="btn_menu" value="사진등록" class="edit_btn" id="PhotoListButton">
		</td>
	</tr>
</form>
</table>
<?php
/* free result set */
mysqli_free_result($result);
?>

</body>
</html>