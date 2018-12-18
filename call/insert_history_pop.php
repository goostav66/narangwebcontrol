<?php session_start(); ?>
<?php
header("Content-Type: text/html;charset=UTF-8");
include_once $_SERVER['DOCUMENT_ROOT'].'/m/header.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/m/call/call_history_exec.php';
?>
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="<?=$path?>/js/callFunction.js?ver=1"></script>

<div class="insert_pop">
	<div>
		입력한 항목은 완료 상태로 추가됩니다.
        <table class="list_data history_insert">
            <colgroup>
                <col style='width: 20%'>
                <col style='width: 50%'>
                <col style='width: 20%'>
                <col style='width: 10%'>
            </colgroup>    
            <tr>
            	<th>가맹점</th><th>주소(출발지)</th><th>호출일시</th><th>등록</th>
            </tr>
            <tr>
                <td><input type='search' class='insert_shop'><button type='button' class='edit_btn' id='shop_addr_btn'>가맹점 찾기</button></td>
                <td><textarea class='insert_addr'></textarea></td>
                <td><input type='text' class='insert_date datepicker' placeholder='날짜'><input type='text' class='insert_time timepicker' placeholder='시간 HH:mm'></td>
                <td><button class="edit_btn" id="insert_history_btn">입력</button></td>
            </tr>
   		</table>
        
    </div>
    <div class="shop_name_search_result" style="display: none;">
        일치하는 가맹점을 선택하면 주소가 자동으로 입력됩니다.
        <table class="list_data insert_search_result">
            <colgroup>
                <col style='width: 20%'>
                <col style='width: 70%'>
                <col style='width: 10%'>
            </colgroup>
            <tr><th>가맹점</th><th>주소</th><th>코드</th></tr>
        </table>
    </div>
</div>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="<?=$path?>/js/datepicker-ko.js"></script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
<script>
	$(document).ready(function(){
		$(".datepicker").datepicker($.datepicker.regional["ko"]);
		$(".datepicker").datepicker("option", "dateFormat", "yy-mm-dd");
		$(".timepicker").timepicker({
			timeFormat: 'HH:mm',
			dropdown: false});
	});
</script>