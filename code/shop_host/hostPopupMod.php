<?php session_start(); ?>
<?php
header("Content-Type: text/html;charset=UTF-8");
include_once $_SERVER[DOCUMENT_ROOT].'/m/header.php';
include_once $_SERVER[DOCUMENT_ROOT].'/m/code/shop_host/shop_host_exec.php';
?>
<?php
	$url = $_GET['p'];
	$idx = $_GET['idx'];
	
	if($idx != null && $idx != 0)
		$event = getEvent($connect, $idx);

?>
<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="<?=$path?>/js/shopHostFunction.js?ver=1"></script>

<? if($idx != null && $idx != 0) 
		echo "<h2 class='title_sub'> 이벤트 수정 </h2>";
	else 
		echo "<h2 class='title_sub'> 이벤트 등록 </h2>";
?>

<div class="pop_btn_set">
	<input type="submit" name="btn_rgst" value="저장 / 수정" class="edit_btn" id="editButton_event">
	<input type="button" name="btn_rgst" value="닫기" class="edit_btn" id="closeButton">
</div>

<div class="event_message">
    <div id="editor"><? if($idx != null && $idx != 0) echo $event['message'];?></div>
</div>

<div class="event_date">
    게시일: 
    <input type='date' name="date_start" <? if($idx != null && $idx != 0) echo "value='"+$event['date_start']+"'";?>/> - 
    <input type='date' name="date_end" <? if($idx != null && $idx != 0) echo "value='"+$event['date_end']+"'";?>/>
</div>
<script src="<?=$path?>/js/quill.min.js?ver=1"></script>
<link href="<?=$path?>/js/quill.snow.css?ver=1" rel="stylesheet">

<script>
	$(document).ready(function(){
		var toolbarOptions = [
			['bold', 'italic', 'underline', 'strike'],
			[{ 'color': [] }, { 'background': [] }]
		];
		var quill = new Quill('#editor', {
			modules:{ toolbar: toolbarOptions },
		    theme: 'snow'
	  	});
	});
</script>
