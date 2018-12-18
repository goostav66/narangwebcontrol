<?php session_start(); ?>
<?php
header("Content-Type: text/html;charset=UTF-8");
include_once  $_SERVER['DOCUMENT_ROOT'].'/m/nav.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/m/call/call_history_exec.php';
?>
<?php
	$call_history = getCallListInit($connect);
?>
<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="<?=$path?>/js/callFunction.js?ver=1"></script>
<h2 class="title_sub">호출기록</h2>

    <div class="history_filter">
        <div class="history_filter_date">
        	<span>날짜별로 보기</span><br />
            <input type="text" class="datepicker" id="history_date_start" placeholder="시작날짜">
            <input type="text" class="datepicker" id="history_date_end" placeholder="종료날짜">
            <br />
            <button class="edit_btn" id="select_yesterday_btn">어제</button>
            <button class="edit_btn" id="select_weekago_btn">일주일</button>
            <button class="edit_btn" id="select_monthago_btn">한달</button>
            <button class="edit_btn" id="filter_date_btn">조회</button>
        </div>
        <div class="history_filter_shop">
        	<span>가맹점별로 보기</span><br />
            <button class="edit_btn" id="search_shop_pop">가맹점 검색</button>
        </div>
        <div class="history_filter_status">
        	<span>상태별로 보기</span><br />
            <select class="select_status">
                <option value="0">전체</option>
                <option value="1">대기</option>
                <option value="2">완료</option>
                <option value="3">취소</option>
            </select>
        </div>
        <div class="history_list_control">
        	<br />
            <select class="select_item_condition">
                <option value='1'>선택한 항목</option>
            </select>
            <button class="edit_btn call_method_btn" id="call_complete_btn">완료</button>
            <button class="edit_btn call_method_btn" id="call_cancel_btn">취소</button>
            <button class="edit_btn call_method_btn" id="call_waiting_btn">대기</button>
            <button class="edit_btn" id="call_delete_btn">삭제</button>
            
            <button class="edit_btn" id="call_insert_btn">추가</button>
    	</div>
    </div>
    
    
<table class="list_data" style="width: 70%">
	<colgroup>
    	<col style='width: 5%'>
        <col style='width: 25%'>
        <col style='width: 40%'>
        <col style='width: 20%'>
        <col style='width: 10%'>
    </colgroup>
	<tr>
   		<th><input type="checkbox" class="check_all"></th><th>가맹점</th><th>가맹점 주소(출발지)</th><th>호출일시</th><th>상태</th>
  	</tr>
 <? while($item = mysqli_fetch_assoc($call_history)){ ?>
 
        <tr class="insert_item history_row">
            <td><input type="checkbox" class="check_item" value="<?=$item['idx']?>"></td>
            <td><?=$item['shop_name']?></td>
            <td><?=$item['shop_addr']?></td>
            <td><?=$item['d_datetime']?></td>
            <td><?=$item['td_status']?></td>
        </tr>
 
	<? } ?>
</table>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="<?=$path?>/js/datepicker-ko.js"></script>
<script>
	$(document).ready(function(){
		$(".datepicker").datepicker($.datepicker.regional["ko"]);
		$(".datepicker").datepicker("option", "dateFormat", "yy-mm-dd");
	});
</script>
