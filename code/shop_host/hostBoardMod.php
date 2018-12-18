<?php session_start(); ?>
<?php
header("Content-Type: text/html;charset=UTF-8");
include_once $_SERVER[DOCUMENT_ROOT].'/m/header.php';
include_once $_SERVER[DOCUMENT_ROOT].'/m/code/shop_host/shop_host_exec.php';
?>
<?php
	$idx = $_GET['idx'];
	$url = $_GET['p'];
	$result = viewHostBoard($connect, $idx);
?>
<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="<?=$path?>/js/shopHostFunction.js?ver=1"></script>
<style>
	#editor{ background-color: #fff; }
</style>
<h2 class="title_sub"> 게시물 수정 </h2>

<div class="pop_btn_set">
	<input type="submit" name="btn_rgst" value="저장 / 수정" class="edit_btn" id="editButton_board">
	<input type="submit" name="btn_rgst" value="삭제" class="edit_btn" id="removeButton">
	<input type="button" name="btn_rgst" value="닫기" class="edit_btn" id="closeButton">
</div>

<?php
	if($row = mysqli_fetch_assoc($result)){?>
<form>
	<input type="hidden" name="idx" value="<?=$row['idx'] ?>" id="host_board_idx"/>

	<div id='editor'><?=$row['content']?></div>
<? } ?>
</form>
<script src="<?=$path?>/js/quill.min.js?ver=1"></script>
<link href="<?=$path?>/js/quill.snow.css" rel="stylesheet">
<script>
	$(document).ready(function(){
		var toolbarOptions = [
			['bold', 'italic', 'underline', 'strike'],
			[{ 'color': [] }, { 'background': [] }],          // dropdown with defaults from theme
			['image']
		];
		var quill = new Quill('#editor', {
			modules:{ toolbar: toolbarOptions },
		    theme: 'snow'
	  	});
	});
</script>
