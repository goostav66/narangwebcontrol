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
	$img_path = "http://hanjicds001.gabia.io/images/sample_img/";
	$img_arr = array("bg_pop_sample1.png", "bg_pop_sample2.png", "bg_pop_sample3.jpg", "bg_pop_sample4.jpg", "bg_pop_sample5.png", "bg_pop_sample6.jpg", "bg_pop_sample7.jpg",); 
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

<div class="event_wrap" align="center">
    <div class="event_textEditor">
        <div id="editor"><? if($idx != null && $idx != 0) echo $event['message'];?></div>
    </div>
	<span>배경</span>
	<div class="event_background" align="center">
    <? for($x = 0; $x < count($img_arr); $x++){?>	
        <label>
            <input type="radio" name="background_img" value="<?=$img_arr[$x]?>"/>
            <img src="<?=$img_path . $img_arr[$x]?>"/>
        </label>
    <? }?>
    </div>
    <span>날짜</span>
    <div class="event_date">
        <input type='text' name="date_start" <? if($idx != null && $idx != 0) echo "value='".$event['date_start']."'";?>/>&nbsp;부터 
        <input type='text' name="date_end" <? if($idx != null && $idx != 0) echo "value='".$event['date_end']."'";?>/>&nbsp;까지
    </div>
    
    
</div>
<script src="<?=$path?>/js/quill.min.js?ver=1"></script>
<link href="<?=$path?>/js/quill.snow.css?ver=1" rel="stylesheet">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="<?=$path?>/js/datepicker-ko.js"></script>
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
		$("input[name='date_start']").datepicker($.datepicker.regional["ko"]);
		$("input[name='date_start']").datepicker("option", "dateFormat", "yy-mm-dd");
		$("input[name='date_end']").datepicker($.datepicker.regional["ko"]);
		$("input[name='date_end']").datepicker("option", "dateFormat", "yy-mm-dd");
		
	});
</script>
