<?php session_start(); ?>
<?php 
header("Content-Type: text/html;charset=UTF-8");
include_once $_SERVER[DOCUMENT_ROOT].'/m/header.php';
include_once $_SERVER[DOCUMENT_ROOT].'/m/code/shopPlus_commerce_exec.php';
?>
<?php 
	$sc_idx = $_GET['idx'];
	if($sc_idx != null)
		$commerce_info = getCommerceInfo($connect, $sc_idx);

	$cityList = getLocationCityList($connect);
?>
<script src="<?=$path?>/js/commerceFunction.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="<?=$path?>/js/datepicker-ko.js"></script>
<script>
	$(document).ready(function(){
		$("input[name='date_commerce_reg']").datepicker($.datepicker.regional["ko"]);
		$("input[name='date_commerce_reg']").datepicker("option", "dateFormat", "yy-mm-dd");
	
		$("input[name='date_commerce_exp']").datepicker($.datepicker.regional["ko"]);
		$("input[name='date_commerce_exp']").datepicker("option", "dateFormat", "yy-mm-dd");

	<? if($sc_idx != null) {?>
		$("input[name='date_commerce_reg']").val('<?=$commerce_info['regdate']?>');
		$("input[name='date_commerce_exp']").val('<?=$commerce_info['expdate']?>');
	<? } ?>
	
		//광고 지역 추가
		$("#btn_ins_location_dist").click(function(e){
			e.preventDefault();
			var code = $("#commerce_location_list_dist option:selected").val();
			var flag = true;
			//중복확인
			$("#commerce_location_list_result option").each(function(){
				if($(this).val() == code){
					alert("이미 등록된 지역입니다.");
					flag = false;
					return false;
				}
			});
			if(flag){
				$.ajax({
					url: 'shopPlus_commerce_exec.php',
					type: 'POST',
					data: { selected_location_code : code },
					success: function(place){
						var option = "<option value='"+code+"'>"+place+"</option>";
						$("#commerce_location_list_result").append(option);
					}
				})
			}
		});
		
		//광고 지역 삭제
		$("#btn_del_location_dist").click(function(e){
			e.preventDefault();
			var select_item = $("#commerce_location_list_result option:selected");
			select_item.remove();
		});
		
		//광고업소 신규등록 - 광고업소 추가
		$(document).on('click', '#btn_submit_new_incommerce', function(e){
			var url = $("input[name='h_shop_url']").val();
			var regdate = $("input[name='date_commerce_reg']").val();
			var expdate = $("input[name='date_commerce_exp']").val();
			var location_code = [];
			
			if($("#text_search_incommerce_shop").val() == ""){
				alert("업소명을 확인해주세요.");
				return;
			}else if(regdate == "" || expdate == ""){
				alert("날짜를 확인해주세요.");
				return;
			}else if(url == ""){
				alert("업소명을 검색하여 선택해주세요.");
				return;
			}
			
			$("#commerce_location_list_result option").each(function(){
				location_code.push($(this).val());
			});
			var location_code_arr = location_code.toString();
			$.ajax({
				url: 'shopPlus_commerce_exec.php',
				type: 'POST',
				data: { submit_incommerce_url : url, submit_incommerce_regdate : regdate, submit_incommerce_expdate : expdate, location_code_arr : location_code_arr },
				success: function(){
					alert("등록이 완료되었습니다.");
					window.close();
					window.opener.location.reload();
				}	
			})
		});
		
		//광고업소 - 정보 변경
		$(document).on('click', '#btn_submit_mod_incommerce', function(){
			var idx = $("input[name='h_sc_idx']").val();
			var regdate = $("input[name='date_commerce_reg']").val();
			var expdate = $("input[name='date_commerce_exp']").val();
			var location_code = [];
			
			$("#commerce_location_list_result option").each(function(){
				location_code.push($(this).val());
			});
			var location_code_arr = location_code.toString();
			$.ajax({
				url: 'shopPlus_commerce_exec.php',
				type: 'POST',
				data: { submit_incommerce_idx : idx, submit_incommerce_regdate : regdate, submit_incommerce_expdate : expdate, location_code_arr : location_code_arr } ,
				success: function(){
					alert("수정이 완료되었습니다.");
					window.close();
					window.opener.location.reload();
				}
			})
		});	
	});
</script>
<div class="form_incommerce_regist">
	<div>
		업소명
    <? if($sc_idx != null) { ?>
    	<input type="text" value="<?=$commerce_info['shop_name']?>" readonly="readonly"/>
        <input type="hidden" name="h_sc_idx" value="<?=$sc_idx?>" />
    <? } else{?>
        <input id="text_search_incommerce_shop" type="text" placeholder="업소명 혹은 코드를 입력하세요">
    	<input type="hidden" name="h_shop_url" value=""/>
     	<button id="btn_search_incommerce_shop" class="edit_btn">검색</button>
    <? } ?>
    </div>
    <div>
    	광고 개시일 <input type="text" name="date_commerce_reg" readonly="readonly"> 
        광고 만료일 <input type="text" name="date_commerce_exp" readonly="readonly">
	</div>
    <div>
    	광고 지역 <button id="btn_enable_location_list" class="edit_btn">지역 찾기</button>
    </div>
    <div align='center'>
    <? if($sc_idx != null){?>
    	<button id="btn_submit_mod_incommerce" class="edit_btn">수정</button>
   	<? } else{ ?>
   		<button id="btn_submit_new_incommerce" class="edit_btn">등록</button>
    <? } ?>
    </div>
</div>
<div class="layout_result_search">
	<table class="table_result_search_shop list_data" style="display: none">
    	<colgroup>
        	<col style="width: 30%"/>
            <col style="width: 10%"/>
            <col style="width: 60%"/>
        </colgroup>
    	<tr>
        	<th>업소명</th><th>코드</th><th>주소</th>
        </tr>
   	</table>
    
    <div class="select_commerce_location_shop" style="display: none" align='center'>
    	<div>
            <select size="10" id="commerce_location_list_city">
    <?	for($x = 0; $x<mysqli_num_rows($cityList); $x++){
            $city = mysqli_fetch_assoc($cityList);
            
            $selected = "";
            if($x == 0) $selected = "selected";
            
            echo "<option value='".$city['location_code']."' ".$selected.">".$city['city']."</option>";
        }		
    ?> 
            </select>
		</div>
        <div>
            <select size="10" id="commerce_location_list_dist">
    <?	getLocationDistList($connect, 1000);//서울 (초기 설정) ?>
            </select>
        </div>
        <div style="display: inline-block">
            <button id="btn_ins_location_dist" class="edit_btn">추가</button><br />
            <button id="btn_del_location_dist" class="edit_btn">제외</button>
        </div>
        <div>
            <select size="10" id="commerce_location_list_result">
    <? if($sc_idx != null){
			$commerceLocationList = getCommerceLocation($connect, $sc_idx);
			while($location = mysqli_fetch_assoc($commerceLocationList)){
				echo "<option value='".$location['location_code']."'>".$location['location_place']."</option>";
			}
		}
	?>        
            </select>  
        </div>
    </div>
</div>