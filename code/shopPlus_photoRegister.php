<?php session_start(); ?>
<?php 
header("Content-Type: text/html;charset=UTF-8");
include_once $_SERVER[DOCUMENT_ROOT].'/m/header.php';
include_once $_SERVER[DOCUMENT_ROOT].'/m/code/shopPlus_exec.php';
?>
<?php

	$shop_idx = $_GET['idx'];
	$shop_name = $_GET['shop_name'];

	$result = ViewList_photo($connect,$shop_idx);
	$row_nums = mysqli_num_rows($result);

	if($row_nums > 0){
		$list = "display:block";
	}else if ($row_nums == 0){
		$list = "display:none";
		$text = "현재 등록된 사진이 없습니다.";
	}

?>
<style>
#image_preview, #photo_list {display:inline-block; width:100%}
#image_preview img {width:24%; height:120px; margin-right:5px; margin-bottom:10px; float:left}
#photo_list div	{ width:24%;  margin-right:5px; margin-bottom:10px; text-align:center; float:left}
#photo_list div img {display:block; width:100%; height:120px}
 #photo_list div input{display:block; margin:0 auto; margin-top:3px}
</style>
<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="<?=$path?>/js/shopFunctiontest.js"></script>
<script>
$( document ).ready(function() {
	
});

function preview_image() {

	var total_file = document.getElementById("file_menu").files.length;

	$('#image_preview').empty();
	for(var i=0;i<total_file;i++){
		$('#image_preview').append("<img src='"+URL.createObjectURL(event.target.files[i])+"'>");
	}
}
</script>
<h2 class="title_sub"> 가맹점 사진 리스트 </h2>

<div class="pop_btn_set">
	
	<input type="button" name="btn_rgst" value="닫기" class="edit_btn" id="closeButton" />
</div>

<table class="list_data">
	<colgroup>
		<col style="width:20%;">
		<col align="left" style="width:80%;" >
	</colgroup>
<input type="hidden" value="<?=$shop_idx?>" id="h_idx" />
	<tr class="pop_list_item">
		<th>가맹점명</th>
		<td><?=$shop_name?></td>
	</tr>
	<tr class="pop_list_item">
		<th>메뉴정보</th>
		<td>
			<div class="shop_menu">
				<?/* 
				<input type="button" value="삭제" class="edit_btn" name="btn_price_delete" id="btn_price_delete" style="float: right; margin-bottom:10px" />
				<input type="submit" value="메뉴저장" class="edit_btn" style="float: right; margin-top: -38px;" />
				*/?>
				 <form id="uploadimage" method="post" enctype="multipart/form-data">
					<input type="hidden" name="shop_menu_idx" value="<?=$shop_idx?>" id="h_idx" />			
				  <input type="file" accept="image/*" id="file_menu" name="file_menu[]" onchange="preview_image();" multiple/>
				  <input type="submit" name='submit_image' value="사진 업로드" class="edit_btn"/>
				 </form>
				
				<div id="image_preview"></div>
				
					<? //메뉴판 입력폼 ?>
			</div>

			<?=$text?>

			<div class="shop_menu" style="<?=$list?>">
				[ 등록된 사진 리스트]
				<div id="photo_list">
				<?
					while ($row = mysqli_fetch_assoc($result)) {
						echo "<div>";
							echo "<img src='".$row['photo_url']."' />";
							echo "<input type='checkbox' name='photo_idx[]' value='".$row['idx']."' />";
						echo "</div>";
					}
				?>
			
				</div>
				<input type="button" name='delete_all_image' id='delete_all_image' value="전체 삭제" class="edit_btn"/>
				<input type="button" name='delete_image' id='delete_image' value="선택 삭제" class="edit_btn"/>
			</div>
		</td>
	</tr>

</table>


</body>
</html>