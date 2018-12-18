<?php session_start(); ?>
<?php
header("Content-Type: text/html;charset=UTF-8");
include_once $_SERVER[DOCUMENT_ROOT].'/m/header.php';
include_once $_SERVER[DOCUMENT_ROOT].'/m/code/shop_host/shop_host_exec.php';
?>
<?php
	$url = $_GET['p'];
	$result = viewEventList($connect, $url);
	$img_path = "http://hanjicds001.gabia.io/images/sample_img/";
?>

<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="<?=$path?>/js/shopHostFunction.js?ver=1"></script>
<h2 class="title_sub"> 이벤트 등록/수정 </h2>

<div class="pop_btn_set">
	<input type="button" value="등록" class="edit_btn event_textEditor_btn">
    <input type="button" value="닫기" class="edit_btn" id="closeButton">
</div>
<div class="section_event">
<?php
	while ( $row = mysqli_fetch_assoc($result) ) {
		if( $row['isFloating'] != 0)
			$chk_res="표시중";
		else
			$chk_res="해제";
?>
    <div class="layout_event" align="center">
    	<input type="hidden" name="idx" value="<?=$row['idx']?>" />
        <div class="event_message" style="background-image: url('<?=$img_path.$row['background_img']?>')">
            <span><?=$row['message']?></span>
        </div>
        <div class="event_duration">
        	<span>기간 : <?=$row['date_start']?> - <?=$row['date_end']?></span>
        </div>
        <div class="event_float">
           <span><?=$chk_res?></span>
        </div>
        <div class="event_button"> 
           <button type="button" class="edit_btn event_textEditor_btn">수정</button>
           <button type="button" class="edit_btn event_delete_btn">삭제</button>
        </div>
    </div>
<? } ?>
</div>
