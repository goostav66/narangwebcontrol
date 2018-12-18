<?php session_start(); ?>
<?php
header("Content-Type: text/html;charset=UTF-8");
include_once $_SERVER['DOCUMENT_ROOT'].'/m/header.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/m/call/call_history_exec.php';
?>
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="<?=$path?>/js/callFunction.js?ver=1"></script>

<div class="search_pop">
    <div class="search_input">
        <input type="search" class="search_shop_text" placeholder="가맹점명, 주소, 코드 입력">
        <button class="edit_btn search_shop_btn">검색</button>
    </div>
    <div class="search_result">
        <table class="list_data history_search_shop" style="display: none">
            <colgroup>
                <col style="width:20%"/>
                <col style="width:70%"/>
                <col style="width:10%"/>
            </colgroup>
            <tr><th>가맹점</th><th>주소</th><th>코드</th></tr>
        </table>
	</div>
</div>
