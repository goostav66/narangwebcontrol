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

<div class='wrap_incommerce_list'>
    <div class='inCommerce_btn_set'> 
        <button id="btn_regist_incommerce" class="edit_btn">신규 등록</button>
        
        <button id="btn_view_locationally" class="edit_btn">지역별 보기</button>
        <label class='panel_view_locationally' style='display: none;'>
            <select id="view_location_list_city">
		<? 
        for($x = 0; $x<mysqli_num_rows($cityList); $x++){
            $city = mysqli_fetch_assoc($cityList);
            echo "<option value='".$city['location_code']."'>".$city['city']."</option>";
        }
        ?>	
            </select>
            <select id="view_location_list_dist">
		<? 
            getLocationDistList($connect, 1000);//전국 (초기 설정)
        ?>
            </select>
        </label>
        <label>    
            <button class="order_date edit_btn" id="btn_order_incommerce_regdate">시작일순 보기</button>
            <input type="checkbox" name="regdate" value="DESC" checked>
        </label>
        <label>
            <button class="order_date edit_btn" id="btn_order_incommerce_expdate">만료일순 보기</button>
            <input type="checkbox" name='expdate' value="DESC">
        </label>
        <label>
            <button id="btn_view_all_incommerce" class="edit_btn">전체 보기</button>
            <input type="hidden" name="load_location_code" value=""/>
        </label>
    </div>
    
    <table class="table_incommerce_list list_data">
        <colgroup>
            <col style='width: 20%'>
            <col style='width: 10%'>
            <col style='width: 20%'>
            <col style='width: 10%'>
            <col style='width: 10%'>
            <col style='width: 30%'>
        </colgroup>
        <tr>
            <th>업소명</th><th>코드</th><th>주소(지역)</th><th>광고시작일</th><th>광고만료일</th><th>광고지역</th>
        </tr>
        <? 
            getCommerceList($connect, 'regdate', 'DESC');
        ?>
    </table>
</div>
<div class='background_incommerce'></div>
<div class='pop_state_incommerce'>
	<input type="hidden" id="selected_row_idx" value=""/>
	<div id='incommerce_shop_name'>(업소명)</div>
    <div class='pop_incommerce_control' id='incommerce_equal'>같은 지역 업소 보기</div>
    <div class='pop_incommerce_control' id='incommerce_modify'>정보 변경</div>
    <div class='pop_incommerce_control' id='incommerce_delete'>삭제</div>
</div>