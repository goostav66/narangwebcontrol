$( document ).ready(function() {

	//---------------------------------------------------------- 가맹점 등록
	$(document).on('click', '#btn_regist_shop', function (e) {
		e.preventDefault();
		var shop_len = $("input[name='new_shop_name']").val().length;
		var store_type = $("select[name='shop_type']").val();
		var branch = $("#select_branch").val();
		var agent= $("#agent").val();
		var ph_len = $("input[name='shop_phone']").val().length;

		var exitEvent = false;
		var check_flag = 0;
		$('input[type=text]').each(function(){
			check_flag = $(this).val().indexOf("'");
			if(check_flag != -1){
				alert("입력란에 ' 을 포함할 수 없습니다.");
				exitEvent = true;
				return false; // each finish
			}
			check_flag = $(this).val().indexOf("/");
			if(check_flag != -1){
				alert("입력란에 / 을 포함할 수 없습니다.");
				exitEvent = true;
				return false; // each finish
			}
		})

		if(!exitEvent){
			if(shop_len == 0){
				alert("가맹점명을 반드시 입력하세요.");
				return false;
			}else if(store_type == 0){
				alert("업소분류를 반드시 선택하세요.");
				return false;
			}else if(branch == 0){
				alert("지사를 반드시 선택하세요.");
				return false;
			}else if(agent == 0){
				alert("대리점을 반드시 선택하세요.");
				return false;
			}
			else if(ph_len == 0){
				alert("휴대폰 번호를 반드시 입력하세요.");
				return false;
			}
			else if (shop_len >= 1 && store_type != 0 && branch != 0 && agent != 0 ){
				var result = confirm("등록하시겠습니까?");
				
				if(result){
					var params = $("#form_shop_regist").serialize();
					var num = $("#insert_data tr").length;
					$.ajax({
						url : 'shop_exec.php',
						type : 'POST',
						data : params,
						success: function(data){
							$("#insert_data tr:first").after(data);
							$(".list_item:first").find("td:first").text(num);
						}
					})
				
				}
			}
		}	
	});
	
	//---------------------------------------------------------- 가맹점 신규 > 지사 선택시 대리점 목록 가져오기
	$(document).on('change', '#select_branch', function(){
		var br_url = $("#select_branch").val();
		
		if( '0' == br_url )
			return;
		
		$.ajax({
			url: 'shop_exec.php',
			type: 'POST',
			data: { select_branch_url : br_url },
			success: function(data){
				$("#select_agent option:not(:first)").remove();
				$("#select_agent").append(data);	
			}
		})
		
	});

	//---------------------------------------------------------- 가맹점 수정 팝업 띄우기
	$(document).on('click', '.list_click', function () {

		var idx = $(this).find("input[name='shop_idx']").val();
		var isAgent = $("#isAgent").val();
		var id = $("#search_id").val();

		var s_type_len = $("#h_search_type").val().length;
		var s_text_len = $("#h_search_text").val().length;

		var url = "";

		var s_type = $("#h_search_type").val();
		var s_text = $("#h_search_text").val();
		url = "shopEdit.php?idx="+idx+"&isA="+isAgent+"&id="+id+"&s_type="+s_type+"&s_text="+s_text;


		window.open(url, "가맹점 정보 수정", "width=900, height=1000, scrollbars=yes, resizable=no");
	});

	//---------------------------------------------------------- 가맹점 팝업 닫기
	$(document).on('click', '#closeButton', function () {
		window.close();
	});

	//---------------------------------------------------------- 가맹점 삭제
	$(document).on('click', '#removeButton', function () {

		var result = confirm("삭제하시겠습니까?");

		if(result) {
			var idx = $("#h_idx").val();
			$.ajax({
				url:'shop_exec.php',
				type:'post',
				data:{ del_idx : idx },
				contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
				dataType: 'html',
				success:function(data){
					if(data == "success"){
						alert("삭제를 완료하였습니다.");
					}else{
						alert(data);
					}
				}
			})
			setTimeout(function(){window.self.close();},1000);
			setTimeout(function(){window.opener.parent.location.reload();},1000);
		} else {
			return false;
		}

	});


	//---------------------------------------------------------- 상점 전경 이미지 삭제
	var del_shop_idx = [];
	$(document).on('click', '.image_select', function(){
		var result = confirm("이미지를 삭제하시겠습니까?");

		if(result){
			$(this).hide();
			del_shop_idx.push($(this).attr('id'));

		}
	});
	//---------------------------------------------------------- 가맹점 수정
	$(document).on('click', '#editButton', function () {
		var exitEvent = false;

		var check_flag = 0;
		$('input[type=text]').each(function(){
			check_flag = $(this).val().indexOf("'");
			if(check_flag != -1){
				alert("입력란에 ' 을 포함할 수 없습니다.");
				exitEvent = true;
				return false; // each finish
			}
		})

		if(exitEvent){
			return false; // event finish
		}else{
		var result = confirm("수정하시겠습니까?");

			if(result) {
				var params = jQuery("#shop_edit_form").serialize();
				var idx = $("#h_idx").val();

				for(var i=0; i<del_shop_idx.length; i++){

				$.ajax({
					url: 'shop_exec.php',
					type: 'post',
					data: {del_idx_shop : del_shop_idx[i]},
					contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
					dataType: 'html',
					success:function(){
						if(data == "success"){
						}else{
							alert(data);
						}
					}
				})
				}

				$.ajax({
					url:'shop_exec.php',
					type:'post',
					data: params,
					contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
					dataType: 'html',
					success:function(){
						alert("수정을 완료하였습니다.");
						window.opener.location.reload();
					}
				})

				//기존 검색 유지를 위한 전송
				var s_type_val = $("#h_search_type").val();
				var s_text_val = $("#h_search_text").val();

				setTimeout(function(){window.location.reload();},1000);
				setTimeout(function(){window.opener.parent.location.href('shopRegister.php?s_type='+s_type_val+'&s_text='+s_text_val);},1000);

			} else {
				return false;
			}
		}

	});


	//---------------------------------------------------------- 가맹점 검색
	$(document).on('click', '#btn_search', function () {


		//검색타입 가져오기
		var valueSearch_type = $('#search_type option:selected').val();

		// 검색 단어 가져오기
		var text = $('#search_text');
		var valueSearch_text = text.val();

		// 아이디 가져오기
		var id = $('#search_id');
		var valueSearch_id = id.val();

		// 권한 가져오기
		var auth = $('#auth');
		var valueSearch_auth = auth.val();

		//가맹점 팝업 종료 후 기존 검색결과를 표출하기 위해 검색정보 기록
		$("#h_search_type").val(valueSearch_type);
		$("#h_search_text").val(valueSearch_text);

		$.ajax({
			url:'shop_exec.php',
			type:'post',
			data:{ search_type : valueSearch_type, search_text : valueSearch_text, search_id : valueSearch_id,
					search_auth : valueSearch_auth },
			contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
			dataType: 'html',
			success:function(data){

				if(data == '0') alert("검색결과가 없습니다.");
				else {
					var obj =  $.parseJSON( data );
					var num = 1;
					$(".insert_item").remove();
					$(".list_new").remove();



					for (var i in obj) {
						$("#insert_data tr:first").after(
							"<tr class='list_item list_click list_new insert_item'>"
								+"<input type='hidden' name='idx' id='shop_idx' value='"+obj[i].idx+"' />"
								+"<td>"+(num++)+"</td>"
								+"<td> http://hanjicds001.gabia.io/index.jsp?p="+obj[i].url+"</td>"
								+"<td>"+obj[i].shop_name+"</td>"
								+"<td>"+obj[i].shop_addr+"</td>"
								+"<td>"+obj[i].branch_name+"</td>"
								+"<td>"+obj[i].agent_name+"</td>"
								+"<td>"+obj[i].shop_ceo_name+"</td>"
								+"<td>"+obj[i].id+"</td>"
								+"<td>"+obj[i].shop_phone+"</td>"
								+"<td>"+obj[i].rgst_date+"</td>"
								+"<td>"+obj[i].cnt+"</td>" //obj[0].call_num
							+"</tr>"
						);
					}
				}
			}
		})
	});

	//---------------------------------------------------------- 가맹점 수정 > 지사 이름리스트 가져오기
	/*$(document).on('click', '#branch_name_edit', function () {

		var br_select = $("#branch_name_edit");
		var br_val = $("#branch_name_edit").val();
		var id = $("#h_id").val();

		$.ajax({
			url:'shop_exec.php',
			type:'post',
			data: { get_branch_edit : "1", user_id : id },
			contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
			dataType: 'html',
			success:function(data){

				var str = data;
				var br_info = str.split(","); // br_info[0] = "지사이름/지사인덱스"

				if(br_info[0]){
					br_select.find('option').remove().end();
					for(var i = 0; i < br_info.length; i++){
						var br = br_info[i].split("/"); // br[0] = "지사이름" , br[1] = 지사인덱스, br[2] = 지사 url
						br_select.append("<option value='"+br[2]+"'>"+br[0]+"</option>");
					}
					br_select.val(br_val);
				}else if(str == "NoPermission"){
					return false;
				}

			}
		})
	});*/

	//---------------------------------------------------------- 가맹점 수정 > 지사 선택 후 대리점 이름 가져오기
	$(document).on('change', '#branch_name_edit', function () {
		var isSelect = $( "#branch_name_edit option:selected" ).val();
		var br_url = $( "#branch_name_edit option:selected" ).val(); // 지사에서 선택된 이름값으로 대리점 선택
		var ag_select = $("#agent_name_edit");

		var isAgent = $('#isAgent').val();

		if(isAgent == 'y'){
			var id = $('#h_id').val();
		}else {
			var id = '';
		}

		if(isSelect == "0"){
			ag_select.find('option').remove().end();
			ag_select.append("<option value='0'>"+"대리점 선택"+"</option>");
			return false;
		}else{
			$.ajax({
				url:'shop_exec.php',
				type:'post',
				data:{ branch_url : br_url, agent_id : id},
				contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
				dataType: 'html',
				success:function(data){
					var str = data;
					var ag_info = str.split(","); // ag_info[0] = "대리점이름/대리점인덱스"

					if(ag_info[0]){
						ag_select.find('option').remove().end();
						for(var i = 0; i < ag_info.length; i++){
							var ag = ag_info[i].split("/"); // ag[0] = "대리점이름" , ag[1] = 대리점인덱스
							ag_select.append("<option value='"+ag[1]+"'>"+ag[0]+"</option>");
						}
					}else{
						alert("fail");
					}
				}
			})
		}
	});

	//---------------------------------------------------------- 가맹점 수정 > 시도 선택 후 구군 목록 가져오기
	$(document).on('change', '.list_city', function(){
		var city = this.value;	
		var list_dist = $(".list_dist");

		
		$.ajax({
			url: 'shop_exec.php',
			type: 'POST',
			data: {location_city_code : city},
			success: function(data){
				list_dist.empty();
				list_dist.append(data);
			},
			error: function(request,status,error){
	  	  		console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
			}

		})
	});

	//---------------------------------------------------------- 가맹점 수정 > 비밀번호 확인
	$(document).on('click', '#btn_reveal_password', function(e){
		e.preventDefault();
	
		var input = $(this).prev();
		if(input.attr('type') == 'text'){
			input.attr('type', 'password');
		}else if(input.attr('type') == 'password'){
			input.attr('type', 'text');
		}
	});

	//---------------------------------------------------------- 가맹점 수정 > 글자수 제한
	
	$(document).on('keyup', 'div .input_text', function(e){
		var length = $(this).val().length;
		$(this).next().text( length+"/"+100);
	});
	$(document).on('focus', 'div .input_text', function(e){
		var length = $(this).val().length;
		$(this).next().text(length+"/"+100);
	});
	$(document).on('blur', 'div .input_text', function(e){
		$(this).next().text("");
	});

	//---------------------------------------------------------- 가맹점 수정 > 상점 전경 사진 파일 등록
	$("#uploadimage_store").on('submit',(function(e) {

		e.preventDefault();

		$("#message").empty();
		$('#loading').show();

		$.ajax({
			url: "shopUpload.php", // Url to which the request is send
			type: "POST",             // Type of request to be send, called as method
			data: new FormData(this), // Data sent to server, a set of key/value pairs (i.e. form fields and values)
			contentType: false,       // The content type used when sending data to the server.
			cache: false,             // To unable request pages to be cached
			processData:false,        // To send DOMDocument or non processed data file it is set to false
			success: function(data){   // A function to be called if request succeeds

				$('#loading').hide();
				$("#message").html(data);
			}
		})
		setTimeout(function(){window.location.reload();},1000);
	}));


	// Function to preview image after validation
	$(function() {
	$("#file").change(function() {
		$("#message").empty(); // To remove the previous error message
			var file = this.files[0];
			var imagefile = file.type;
			var match= ["image/jpeg","image/png","image/jpg", "image/gif", "image/bmp"];
			if(!((imagefile==match[0]) || (imagefile==match[1]) || (imagefile==match[2])
					|| (imagefile==match[3]) || (imagefile==match[4])))
			{
				$('#previewing').attr('src','noimage.png');
				$("#message").html("<p id='error'>업로드 가능한 이미지 파일이 아닙니다.</p>"
								+"<span id='error_message'>jpeg, jpg, png, gif, bmp파일만 업로드 가능합니다.</span>");
				return false;
			}
			else
			{
				var reader = new FileReader();
				reader.onload = imageIsLoaded;
				reader.readAsDataURL(this.files[0]);
			}
		});
	});

	function imageIsLoaded(e) {
		$("#file").css("color","green");
		$('#image_preview').css("display", "block");
		$('#previewing').attr('src', e.target.result);
		$('#previewing').attr('width', '300px');
	};

	//---------------------------------------------------------- 가맹점 수정 > 메뉴 등록팝업 띄우기
	$(document).on('click', '#menuListButton', function () {

		var idx = $("#h_idx").val();
		var shop_name = $("input[name=shop_name]").attr("value");

		var url = "shopMenuRegister.php?idx="+idx+"&shop_name="+shop_name;

		window.open(url, "가맹점 메뉴 등록", "width=900, height=1000, scrollbars=yes, resizable=no");
	});

	//---------------------------------------------------------- 가맹점 수정 > 메뉴 저장 (등록,수정)
	// byte 계산 로직
	function getByteLength(s){
		var b = 0;
		var i = 0;
		var c = 0;
		for(b = i = 0; c = s.charCodeAt(i++); b += c >> 11 ? 3 : c >> 7 ? 2 : 1);
		return b;
	}

	$('textarea').keyup(function(){ //남은 글자수 구하기
		var inputLength = getByteLength($(this).val());

		if(inputLength > 400){
			$(this).parent().find($('.letter')).css("color","red");
		}
		$(this).parent().find($('.letter')).html(inputLength);

	});

	$(".uploadimage").on('submit',(function(e) {

		e.preventDefault();

		var text = $(this).find($("textarea[name=menu_infor]")).val();
		var stringByte = getByteLength(text);

		if(stringByte > 400){
			alert("정보란에 입력 가능한 글자수를 초과하였습니다.(400자)");
			return false;
		}

		var shop_menu_div = $(this).parent();
		shop_menu_div.css("background", "pink");

		$.ajax({
			url: "shopUpload.php", // Url to which the request is send
			type: "POST",             // Type of request to be send, called as method
			data: new FormData(this), // Data sent to server, a set of key/value pairs (i.e. form fields and values)
			contentType: false,       // The content type used when sending data to the server.
			cache: false,             // To unable request pages to be cached
			processData:false,        // To send DOMDocument or non processed data file it is set to false
			success: function(data){   // A function to be called if request succeeds
				alert(data);
				shop_menu_div.css("background", "#fff");
				window.history.go(0);
			}
		})


	}));

	// Function to preview image after validation
	$(".file_menu").change(function(e) {
		var this_div = $(this).parent();
		var this_form = this_div.parent();

		var shop_img_div = this_form.children($('.shopmenu_img'));
		var img_div = shop_img_div.children($('.image_preview_menu'));

		jQuery(this).children($(".message_menu")).empty(); // To remove the previous error message

		var file = this.files[0];
		var imagefile = file.type;
		var match= ["image/jpeg","image/png","image/jpg", "image/gif", "image/bmp"];
		if(!((imagefile==match[0]) || (imagefile==match[1]) || (imagefile==match[2])
					|| (imagefile==match[3]) || (imagefile==match[4])))
		{
			jQuery(this).children($('.previewing_menu')).attr('src','noimage.png');
			jQuery(this).children($(".message_menu")).html("<p id='error'>업로드 가능한 이미지 파일이 아닙니다.</p>"
								+"<span id='error_message'>jpeg, jpg, png, gif, bmp 파일만 업로드 가능합니다.</span>");
			return false;
		}
		else
		{
			var reader = new FileReader();
			reader.target_elem = img_div.children('.previewing_menu');
			reader.onload =  function (e) {
			   // Attach the preview
				$(this).css("color","green");
				//img_div.css("display", "block");
				$(reader.target_elem).attr('src', e.target.result);
				$(reader.target_elem).attr('width', '200px');
			};
			reader.onerror = function(e) {
				alert("shopFunction.js error: " + e.target.error.code);
			};
			reader.readAsDataURL(this.files[0]);
		}

	});

	//---------------------------------------------------------- 가맹점 수정 > 기존 메뉴 삭제
	$(document).on('click', '#btn_menu_remove', function () {
		var result = confirm("삭제하시겠습니까?");

			if(result) {

				var btn_div = $(this).parent();

				var menu_div = btn_div.parent(".shop_menu");
				var menu_idx = $(this).next().val();

				//alert(menu_idx);

				$.ajax({
					url:'shop_exec.php',
					type:'post',
					data:{ del_menu_idx : menu_idx },
					contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
					dataType: 'html',
					success:function(data){
						if(data == "success"){
							alert("삭제를 완료하였습니다.");
						}else{
							alert(data);
						}
					}
				})

				menu_div.remove();
			}
			else{}
	});

	//---------------------------------------------------------- 가맹점 수정 > 메뉴 항목 추가
	$(document).on('click', '#menuButton', function () {
		alert("항목추가 보완 중 입니다.");
		/*
		var last_div = $(".shop_menu:last-child");
		var idx = $("#h_idx").val();
		var del_btn = "<input type='button' name='btn_menu_delete' "
						+"value='X' class='btn_menu_delete edit_btn' id='btn_menu_delete'/>";

		last_div.after("<div class='shop_menu'>"
				+"<div>"
					+del_btn
				+"</div>"
				+"<form class='uploadimage' method='post' enctype='multipart/form-data'>"
					+"<div class='shopmenu_img' >"
						+"<div class='image_preview_menu'>"
							+"<img class='previewing_menu' src='upload/noimage.png' />"
						+"</div>"
						+"<input type='file' name='file_menu' class='file_menu' />"
					+"</div>"
					+"<div>type : <select id='menu_type' name='menu_type'>"
								  +"<option value='0'>전체메뉴</option>"
								  +"<option value='1'>점심특선</option>"
								  +"<option value='2'>기타</option>"
								+"</select>"
						+"이름 : <input type='text' name='menu_name' class='input_text' size='10' required>"
						+"<span class='price'>"
								+" 가격 : <input type='text' name='price' class='input_text' size='10' >"
								+" <input type='button' value='대/중/소 전환' class='price_btn' "
								+"id='btn_menu_price' name='btn_menu_price' /><br/>  "
						+"</span>"

						+"<span class='price_sml' style='display:none'>"
								+" 대 : <input type='text' name='price_l' class='input_text' size='10' >"
								+" 중 : <input type='text' name='price_m' class='input_text' size='10' >"
								+" 소 : <input type='text' name='price_s' class='input_text' size='10' >"
								+" <input type='button' value='단일가격 전환' class='price_btn' "
								+"id='btn_menu_price' name='btn_menu_price' /><br/> "
						+"</span>"
						+"정보 : <input type='text' name='info' class='input_text' size='60' > "
								+"<input type='submit' value='메뉴저장' class='edit_btn' name='btn_menu_edit' />"
								+"<input type='hidden' name='shop_menu_idx' id='shop_menu_idx' value='"+idx+"'>"
					+"</div>"
				+"</form>"
			+"</div>");
			*/

	});

	//---------------------------------------------------------- 가맹점 수정 > 메뉴 가격 대/중/소 변환
	$(document).on('click', '.price_btn', function () {

		var price_div = $(this).parent().parent(); // input > span > div

		var n = price_div.find($('span')).length;

		price_div.find($('span')).toggle();

		$(this).siblings($('.price > input[type=text]')).val('');
		$(this).siblings($('.price_sml > input[type=text]')).val('');

	});


	//---------------------------------------------------------- 가맹점 수정 > 메뉴 항목 삭제
	$(document).on('click', '#btn_menu_delete', function () {

		var btn_div = $(this).parent();

		var menu_div = btn_div.parent(".shop_menu");

		menu_div.remove();
	});



	//---------------------------------------------------------- 가맹점 수정 > 주인장 이야기 항목 팝업
	$(document).on('click', '#boardListButton', function () {
		var p = $("input[name='edit_url']").val();
		var url = "shop_host/shopHostBoard.php?p="+p;

		window.open(url, "게시판", "width=900, height=1000, scrollbars=yes, resizable=no");
	});

	$(document).on('click', '#popupListButton', function(){
		var p = $("input[name='edit_url']").val();
		var url = "shop_host/shopHostPopup.php?p="+p;

		window.open(url, "이벤트관리", "width=670, height=900, scrollbars=yes, resizable=no");
	});

	$(document).on('click', '#saleListButton', function () {
		var p = $("input[name='edit_url']").val();
		var url = "shop_host/shopHostSale.php?p="+p;

		window.open(url, "번개할인", "width=500, height=500, scrollbars=yes, resizable=no");
	});
	$(document).on('click', '#replyListButton', function(){
		var p = $("input[name='edit_url']").val();
		var url = "shop_host/shopHostReply.php?p="+p;

		window.open(url, "손님이야기", "width=670, height=900, scrollbars=yes, resizable=no");
	});
	$(document).on('click', '#tagListButton', function(){
		var idx = $("#h_idx").val();
		var url = "shop_host/shopHostTag.php?idx="+idx;

		window.open(url, "검색어관리", "width=600, height=500, scrollbars=yes, resizable=no");
	});
});
