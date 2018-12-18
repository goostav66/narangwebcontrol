<?php session_start(); ?>
<?php
header("Content-Type: text/html;charset=UTF-8");
include_once $_SERVER[DOCUMENT_ROOT].'/m/header.php';
include_once $_SERVER[DOCUMENT_ROOT].'/m/code/shop_host/shop_host_exec.php';
?>
<?php
	$url = $_GET['p'];
	$result = viewBoardList($connect, $url);
	$num = mysqli_num_rows($result);
?>
<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="<?=$path?>/js/shopHostFunction.js?ver=1"></script>
<h2 class="title_sub"> 주인장이야기 게시판 </h2>

<div class="pop_btn_set">
	<input type="button" name="btn_rgst" value="등록" class="edit_btn board_textEditor_btn">
	<input type="button" name="btn_rgst" value="닫기" class="edit_btn" id="closeButton">
</div>
<table class="list_data">
    <colgroup>
        <col style="width:10%">
        <col style="width:60%">
        <col style="width:10%">
    </colgroup>
    <tr>
        <th>No.</th>
        <th>등록일</th>
        <th class="last">편집</th>
    </tr>
<?php
	while($row = mysqli_fetch_assoc($result)){
	?>
    <tr>
        <td><?=$num?></td>
        <td><?=$row['regdate']?></td>
        <td class="last">
        	<input type='hidden' name='idx' value='<?=$row['idx'] ?>'/>
        	<button type="button" class="edit_btn board_textEditor_btn">수정</button>
          <button type="button" class="edit_btn board_delete_btn">삭제</button>
        </td>
    </tr>
<?	$num--;
	}
?>
</table>
