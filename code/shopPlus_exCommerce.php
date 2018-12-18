<?php session_start(); ?>

<?php 
header("Content-Type: text/html;charset=UTF-8");
include_once $_SERVER[DOCUMENT_ROOT].'/m/nav.php';
include_once $_SERVER[DOCUMENT_ROOT].'/m/code/shopPlus_commerce_exec.php'; 
?>

<?php
    $cityList = getLocationCityList($connect);
?>

<script src="<?=$path?>/js/commerceFunction.js"></script>

<div class="wrap_excommerce_list">
	<div class="exCommerce_btn_set">
        <div>
    	   <button class="edit_btn" id="btn_regist_excommerce">신규 등록</button>
        </div>

        <div>
	        <button class="order_date edit_btn">시작일순 보기</button>
            <input type="checkbox" name="e_regdate" value="DESC"/>
        </div>
        
        <div>
        	<button class="order_date edit_btn">만료일순 보기</button>
            <input type="checkbox" name="e_expdate" value="DESC"/>
        </div>
        
        <!-- <div class="panel_radio_hosting">호스팅
        	<input type="radio" name="hosting" value="0">내부
            <input type="radio" name="hosting" value="1">외부
        </div> -->
        
        <div>
        	<button id="btn_view_excommerce_locationally" class="edit_btn">광고 지역</button>
            <div class="panel_excommerce_location" style="display: none">
                <select id="view_location_list_city_excommerce">
            <? for($x = 0; $x<mysqli_num_rows($cityList); $x++){
                $city = mysqli_fetch_assoc($cityList);

                $selected = "";
                if($x == 0) $selected = "selected";

                echo "<option value='".$city['location_code']."' ".$selected.">".$city['city']."</option>";
            } ?>    
                </select>
                <select id="view_location_list_dist_excommerce">
            <? 
                getLocationDistList($connect, 1000);//전국 (초기 설정)
            ?>        
                </select>
            </div>
        </div>
        
        <div>
            <button id="btn_view_all_excommerce" class="edit_btn" onclick="location.reload();">전체 보기</button>
        </div>

        <div class="panel_search_excommerce">
            <select id="select_search_excommerce">
                <option value="e_enterprise">업체명</option>
                <option value="e_info">광고내용</option>
                <option value="e_page_url">페이지</option>
            </select>
            <input type='search' id="text_search_excommerce">
            <button id="btn_search_excommerce" class="edit_btn">검색</button>
        </div>
    </div>
    
    <table class="table_excommerce_list list_data">
    	<colgroup>
        	<col style="width:20%">
            <col style="width:20%">
            <col style="width:10%">
            <col style="width:20%">
            <col style="width:10%">
            <col style="width:10%">
            <col style="width:10%">
        </colgroup>
        <tr><th>업체</th><th>광고내용</th><th>호스팅</th><th>페이지</th><th>광고시작일</th><th>광고만료일</th><th>광고지역</th></tr>
        <? 
			getExCommerceList($connect, 'e_regdate', 'DESC');
		?>
    </table>
</div>
<div class='background_excommerce'></div>
<div class='pop_state_excommerce'>
	<input type="hidden" id="selected_row_idx" value=""/>
	<div id='excommerce_ent_name'>(업소명)</div>
    <div class='pop_excommerce_control' id='excommerce_confirm'>광고페이지 이동</div>
    <div class='pop_excommerce_control' id='excommerce_modify'>정보 변경</div>
    <div class='pop_excommerce_control' id='excommerce_delete'>삭제</div>
</div>