<?php session_start(); ?>
<?php
header("Content-Type: text/html;charset=UTF-8");
include_once $_SERVER[DOCUMENT_ROOT].'/m/header.php';
include_once $_SERVER[DOCUMENT_ROOT].'/m/code/shop_host/shop_host_exec.php';
?>
<?php
	$url = $_GET['p'];
?>
<style>
body{ background-color:#fff; }
</style>
<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="<?=$path?>/js/shopHostFunction.js?ver=1"></script>
<h2 class="title_sub"> 게시물 등록 </h2>
<div class="pop_btn_set">
	<input type="submit" name="btn_rgst" value="저장" class="edit_btn" id="saveButton_board">
	<input type="button" name="btn_rgst" value="닫기" class="edit_btn" id="closeButton">
</div>
<form>
    <input type="hidden" name="url" value="<?=$url?>" id="board_url">
    <div class="text_editor" style="width: 100%; margin-top: 5px;">
        <div id="editor" style="height: 500px"></div>
    </div>
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
