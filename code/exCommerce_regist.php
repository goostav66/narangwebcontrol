<?php session_start(); ?>
<?php 
header("Content-Type: text/html;charset=UTF-8");
include_once $_SERVER[DOCUMENT_ROOT].'/m/header.php';
include_once $_SERVER[DOCUMENT_ROOT].'/m/code/shopPlus_commerce_exec.php';
?>

<?php 
	$cityList = getLocationCityList($connect);

	//수정 모드
	$e_idx = $_GET['idx'];
	if( $e_idx != null )
		$commerce_info = getExCommerceInfo($connect, $e_idx);

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
		

	<? if($e_idx != null) {?>
		//수정 모드 : 광고 시작일, 만료일 세팅
		$("input[name='date_commerce_reg']").val('<?=$commerce_info['e_regdate']?>');
		$("input[name='date_commerce_exp']").val('<?=$commerce_info['e_expdate']?>');
		
		if( 0 == <?=$commerce_info['e_type']?> ){//내부 호스팅
			$("input[name='hosting']:first").click();
			var inhosting_page = $("input[name='inhosting_page_url']");
			var page_url = "<?=$commerce_info['e_page_url']?>";

			inhosting_page.val( page_url );
			inhosting_page.prop("readonly", "readonly");

			$(".label_uploading_imgs").hide();
			$("#btn_clr_inhosting_page").prop("disabled", false);
			$("#btn_clr_inhosting_page").show();

			$("#btn_clr_inhosting_page").click(function(){
				if(!confirm("파일을 새로 작성하시겠습니까?\n기존의 파일은 삭제됩니다."))
					return;

				$(".label_uploading_imgs").show();
				$("input[name='inhosting_page_url']").val("");
				$("input[name='inhosting_page_url']").prop("readonly", false);
				$("input[name='inhosting_page_url']").focus();
				$("#btn_clr_inhosting_page").prop("disabled", true);
			});
		}else if( 1 == <?=$commerce_info['e_type']?> ){
			$("input[name='hosting']:last").click();
			var exhosting_page = $("input[name='exhosting_page_url']");
			exhosting_page.val("<?=$commerce_info['e_page_url']?>");
		}

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
		

		var arr_filename_rule = ['*', '/', '\\', ':', '?', '|', '>', '<', '"'];
		//외부업체광고 등록
		$(document).on('click', '#btn_submit_new_excommerce', function(){
			
			var excommerce_enterprise = $(".form_excommerce_regist input[name='enterprise']").val();
			var info = $(".form_excommerce_regist input[name='e_info']").val();
			
			var main_img = $(".form_excommerce_regist input[name='e_main_img']")[0].files[0];
			if(main_img == null){
				alert("배너 이미지를 등록해주세요.");
				return;
			}
			var e_type = $(".form_excommerce_regist input[name='hosting']:checked").val();
			
			var e_page_url = "";
			if(e_type == '0'){//내부 호스팅 선택시
				e_page_url = $(".form_excommerce_regist input[name='inhosting_page_url']").val();
			}else if (e_type == '1'){//외부 호스팅 선택시
				e_page_url = $(".form_excommerce_regist input[name='exhosting_page_url']").val();
			}

			var e_regdate = $(".form_excommerce_regist input[name='date_commerce_reg']").val();
			var e_expdate = $(".form_excommerce_regist input[name='date_commerce_exp']").val();
			
			//파일명 유효성 확인
			for(var x = 0; x < arr_filename_rule.length; x++){
				var tmp = e_page_url.indexOf(arr_filename_rule[x]); 
				if(tmp != -1){
					alert("페이지명에 포함할 수 없는 특수문자가 있습니다.");
					return false;	
				}
			}

			if( excommerce_enterprise == "" ){
				alert("업체명을 입력해주세요.");
				return;
			}else if( e_type == null || e_type == "" ){
				alert("호스팅 방식을 선택해주세요.");
				return;
			}else if( e_page_url == "" ){
				alert("사이트 주소(페이지명)를 확인해주세요.");
				return;
			}else if(e_regdate == "" || e_expdate == ""){
				alert("날짜를 입력해주세요.");
				return;
			}
			
			var location_code = [];
			$("#commerce_location_list_result option").each(function(){
				location_code.push($(this).val());
			});
			var location_code_arr = location_code.toString();
			
			var form = new FormData();
			
			form.append("excommerce_enterprise",excommerce_enterprise);
			form.append("e_info", info);
			form.append("e_main_img", main_img);
			form.append("e_type", e_type);
			form.append("e_page_url", e_page_url);
			form.append("e_regdate", e_regdate);
			form.append("e_expdate", e_expdate);
			form.append("location_code_arr", location_code_arr);
			
			if( e_type == '0' ){
				var inhosting_files = $(".excommerce_hosting_in .img_files");
					
				if(inhosting_files[0].files[0] == null){
					alert("이미지 파일을 등록해주세요.");
					return;
				}else{
					form.append("files_amount", inhosting_files.length);
					var idx = 0;
					inhosting_files.each(function(){
						form.append("file_"+idx, $(this)[0].files[0]);
						idx++;
					});
				}
			}
						 
			$.ajax({
				url: 'shopPlus_commerce_exec.php',
				type: 'POST',
				data: form,
				processData: false,
				contentType: false,
				success: function(data){
					alert(data);
					window.close();
					window.opener.location.reload();
				}
			})
		});
		
		//외부업체 광고 - 정보변경 (3차: 배너 이미지)
		$(document).on('click', '#btn_submit_mod_excommerce', function(){
			var e_idx = $("input[name='h_e_idx']").val();
			var e_enterprise = $("input[name='enterprise']").val();
			var e_info = $("input[name='e_info']").val();
			var e_main_img = $("input[name='e_main_img']")[0].files[0];
			var e_type = $(".form_excommerce_regist input[name='hosting']:checked").val();
			var e_regdate = $("input[name='date_commerce_reg']").val();
			var e_expdate = $("input[name='date_commerce_exp']").val();

			var location_code = [];

			$("#commerce_location_list_result option").each(function(){
				location_code.push($(this).val());
			});
			var location_code_arr = location_code.toString();

			var formData = new FormData();
			formData.append("submit_excommerce_idx", e_idx);
			formData.append("submit_excommerce_enterprise", e_enterprise);
			formData.append("submit_excommerce_info", e_info);
			formData.append("submit_excommerce_type", e_type);
			formData.append("submit_excommerce_regdate", e_regdate);
			formData.append("submit_excommerce_expdate", e_expdate);
			formData.append("location_code_arr", location_code_arr);
			if(e_main_img != null)
				formData.append("submit_excommerce_main_img", e_main_img);

			if( 0 == e_type ){//내부 호스팅일시 페이지명 변경 여부 체크 - 변경되면 페이지명, 이미지 파일 전송
				var page_url = $("input[name='inhosting_page_url']").val();
				formData.append("submit_excommerce_page_url", page_url);

				var flag = $("#btn_clr_inhosting_page").prop("disabled");
				if(flag){//disabled = true 일 때 페이지 변경됨	
					//파일명 유효성 확인
					for(var x = 0; x < arr_filename_rule.length; x++){
						var tmp = page_url.indexOf(arr_filename_rule[x]); 
						if(tmp != -1){
							alert("페이지명에 포함할 수 없는 특수문자가 있습니다.");
							return false;	
						}
					}

					var inhosting_files = $(".excommerce_hosting_in .img_files");
					if(inhosting_files[0].files[0] == null){
						alert("이미지 파일을 등록해주세요.");
						return;
					}else{
						formData.append("files_amount", inhosting_files.length);
						var idx = 0;
						inhosting_files.each(function(){
							formData.append("file_"+idx, $(this)[0].files[0]);
							idx++;
						});
					}
				}
			}else if( 1 == e_type ){
				var page_url = $("input[name='exhosting_page_url']").val();
				formData.append("submit_excommerce_page_url", page_url);
			}

			$.ajax({
				url: 'shopPlus_commerce_exec.php',
				type: 'POST',
				processData: false,
				contentType: false,
				data: formData,
				success: function(data){
					alert(data);
					window.close();
					window.opener.location.reload();
				}
			})
		});

		//내부호스팅 - 이미지 파일 추가	
		$(document).on('click', '#btn_add_img_file', function(){
			var index = $(".excommerce_hosting_in input[type='file']").length;
			var input_files = "<br><input type='file' name='inhosting_files_"+index+"' class='img_files'>";
			$(".excommerce_hosting_in input[type='file']:last").after(input_files);
		});
		

	});

</script>
    <div class="form_excommerce_regist">
        <div>
        <? if($e_idx != null ) { ?>
        	업체명 <input type="text" name="enterprise" value="<?=$commerce_info['e_enterprise']?>">
        	<input type="hidden" name="h_e_idx" value="<?=$e_idx?>"> 
        <? } else{ ?>
            업체명 <input type="text" name="enterprise">
        <? } ?>
        </div>
        <div>
        <? if($e_idx != null ) { ?>	
        	광고내용 <input type="text" name="e_info" value="<?=$commerce_info['e_info']?>">
        <? } else{ ?>
        	광고내용 <input type="text" name="e_info">
        <? } ?>	
        </div>
        <div>
        	배너이미지 
    	<? if( $e_idx != null) { ?>
    		<img src="<?=$commerce_info['e_main_img']?>" class="preview_hosting_banner"><br>
    		<input type="file" name="e_main_img"><br>배너 이미지 변경시만 파일을 선택해주세요.
    	<? } else{ ?>
    		<img src='' class="preview_hosting_banner"><br>
    		<input type="file" name="e_main_img">
    	<? } ?>
        </div>
        <div>
            호스팅 방식
            <input type="radio" name="hosting" value="0">내부
            <input type="radio" name="hosting" value="1">외부
            <div class="hosting_page_url">
                <label class="excommerce_hosting_in" style="display: none;">
                	페이지명(중복불가)
                    <input type="text" name="inhosting_page_url"/>
                    <button id="btn_clr_inhosting_page" class="edit_btn" style="display: none" disabled="disabled">파일 새로 만들기</button>
                    <br>
                    <div class="label_uploading_imgs">
	                    이미지 파일 업로드
	                    <input type="file" name="inhosting_files_0" class="img_files">
	                    <button id="btn_add_img_file" class="edit_btn">이미지 추가</button><br>
	                    여러 개의 이미지 업로드시 [이미지 추가] 버튼을 클릭하여 파일선택폼을 추가합니다.<br>
	                  	한번 등록된 페이지는 수정이 불가능하며 삭제 후 다시 업로드해야합니다.<br>
	                  	페이지명은 영문자와 특수문자로만 구성할 수 있습니다.(사용 불가 문자 : \, /, :, *, ?, ", <, >, |)<br>
	                  	페이지명은 확장자(.php)를 제외하고 입력해주세요.
                  	</div>
                </label>

                <label class='excommerce_hosting_ex' style="display: none">
                    사이트 주소
                    <input type="text" name="exhosting_page_url"/>
                </label>

            </div>
        </div>
        <div>
        	광고시작일 <input type="text" name="date_commerce_reg" readonly="readonly">
            광고만료일 <input type="text" name="date_commerce_exp" readonly="readonly">
       	</div>
        <div>
            광고 지역 <button id="btn_enable_location_list" class="edit_btn">지역 찾기</button>
        </div>
        <div align='center'>
        <? if($e_idx != null) { ?>
        	<button id="btn_submit_mod_excommerce" class="edit_btn">수정</button>
        <? } else {?>
            <button id="btn_submit_new_excommerce" class="edit_btn">등록</button>
        <? } ?>
        </div>
    </div>
    <div class="select_commerce_location_enterprise" style="display: none" align='center'>
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
    <?	getLocationDistList($connect, 1000);//전국 (초기 설정) ?>
            </select>
        </div>
        <div style="display: inline-block">
            <button id="btn_ins_location_dist" class="edit_btn">추가</button><br>
            <button id="btn_del_location_dist" class="edit_btn">제외</button>
        </div>
        <div>
            <select size="10" id="commerce_location_list_result">
	    <? if($e_idx != null) { 
	    	$commerceLocationList = getExCommerceLocation($connect, $e_idx);
	    	while($location = mysqli_fetch_assoc($commerceLocationList)){
	    		echo "<option value='".$location['e_location_code']."'>".$location['location_place']."</option>";
	    	}
	    } ?>   
            </select>  
        </div>
    </div>

