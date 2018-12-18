<?php session_start(); ?>
<?php
header("Content-Type: text/html;charset=UTF-8");
include_once $_SERVER[DOCUMENT_ROOT].'/m/header.php';
include_once $_SERVER[DOCUMENT_ROOT].'/m/code/shop_host/shop_host_exec.php';
?>
<?php
	$url = $_GET['p'];
	$result = viewReplyList($connect, $url);
	
 ?>
<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="<?=$path?>/js/shopHostFunction.js?ver=1"></script>

<h2 class="title_sub"> 손님이야기 조회/삭제 </h2>

<div class="pop_btn_set">
	<input type="submit" name="btn_rgst" value="선택 삭제" class="edit_btn" id="removeButton_reply">
	<input type="button" name="btn_rgst" value="닫기" class="edit_btn" id="closeButton">
</div>

<div class="reply_list">
<?php
	while($row = mysqli_fetch_assoc($result)){
		$reply_idx = $row['idx'];
		$photoes = viewReplyPhoto($connect, $reply_idx);
?>
	<div class="layout_reply">
    	<div class="reply_comment limitedHeight">
        	<span><?=$row['credate']?></span><br />
			<span><?=$row['msg']?></span>
        </div>	
        <div class="reply_photo">
        <? while($photo = mysqli_fetch_assoc($photoes)){?>
      		<img src='<?=$photo['photo_url']?>'>
        <? } ?>
        </div>
        <div class="reply_delete">       
        	<img src='<?=$path?>/images/icon_x.png' id='<?=$reply_idx?>'/>
        </div>
    </div>
<? } ?>
</div>

