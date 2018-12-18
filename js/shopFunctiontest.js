$( document ).ready(function() {
	
	//---------------------------------------------------------- 가맹점 등록
	$(document).on('click', '#rgstButton', function () {
		
		var shop_len = $("#shop_name").val().length;
		var store_type = $("#store_type").val();
		var branch = $("#branch").val();
		var agent= $("#agent").val();
		var tel_len = $("#shop_tel").val().length;
		
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
			else if(tel_len == 0){
				alert("대표번호를 반드시 입력하세요.");
				return false;
			}
			else if (shop_len >= 1 && store_type != 0 && branch != 0 && agent != 0 && tel_len >= 1 ){
				var result = confirm("등록하시겠습니까?");

				if(result) {
				   var params = jQuery("#shop_rgst_form").serialize();
					$.ajax({
						url:'shopPlus_exec.php',
						type:'post',
						data:params,
						contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
						dataType: 'html',
						success:function(data){
							//alert(data);
							var obj =  $.parseJSON( data );
							var num = $("#insert_data tr").length;

							$("#insert_data tr:first").after(
								"<tr class='list_item list_click list_new'>"
									+"<input type='hidden' name='idx' id='shop_idx' value='"+obj[0].idx+"' />"
									+"<td>"+num+"</td>"
									+"<td>http://m.nfczone.co.kr/nfc_index.jsp?p="+obj[0].url+"</td>"
									+"<td>"+obj[0].shop_name+"</td>"
									+"<td>"+""+"</td>"
									+"<td>"+obj[0].branch_name+"</td>"
									+"<td>"+obj[0].agent_name+"</td>"
									+"<td>"+obj[0].ceo_name+"</td>"
									+"<td>"+obj[0].id+"</td>"
									+"<td>"+obj[0].shop_tel+"</td>"
									+"<td>"+obj[0].rgst_date+"</td>"
									+"<td>"+""+"</td>" //obj[0].call_num
								+"</tr>"
							);
							
						},
						error: function (request, status, error)
						{
							alert("error! please check console");
							console.log('code: '+request.status+"\n"+'message: '+request.responseText+"\n"+'error: '+error);
						}
					})
					return false;
				} else {
					return false;
				}
			}
		}
		
	});
	
	//---------------------------------------------------------- 가맹점 수정 팝업 띄우기
	$(document).on('click', '.list_click', function () {
		
		var idx = jQuery("#shop_idx", this).val();
		var isAgent = $("#isAgent").val();
		var id = $("#search_id").val();
		var type = $("#shop_type").val();

		var url = "";

		var s_type = $("#h_search_type").val();
		var s_text = $("#h_search_text").val();
		url = "shopPlus_edit.php?idx="
				+idx+"&isA="+isAgent+"&id="+id+"&s_type="+s_type+"&s_text="+s_text+"&type="+type;
		
		//alert("&s_type="+s_type+"&s_text="+s_text);
		window.open(url, "정보 수정", "width=900, height=1000, scrollbars=yes, resizable=no");
	});
	
	//---------------------------------------------------------- 대리점 이름 가져오기
	$(document).on('change', '#branch', function () {
		var isSelect = $( "#branch option:selected" ).val();
		var br_url = $( "#branch option:selected" ).val(); // 지사에서 선택된 url으로 대리점 선택
		var ag_select = $("#agent");

		var isAgent = $('#isAgent').val();

		if(isAgent == 'y'){
			var id = $('#search_id').val();
		}else {
			var id = '';
		}

		if(isSelect == "0"){ 
			ag_select.find('option').remove().end();
			ag_select.append("<option value='0'>"+"대리점 선택"+"</option>");
			return false;
		}else{
			$.ajax({
				url:'shopPlus_exec.php',
				type:'post',
				data:{branch_url : br_url, agent_id : id},
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
					url:'shopPlus_exec.php',
					type:'post',
					data:{ del_idx : idx},
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
				
				$.ajax({
					url:'shopPlus_exec.php',
					type:'post',
					data: params,
					contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
					dataType: 'html',
					success:function(data){
						
						if(data == "success"){
							alert("수정을 완료하였습니다.");
						}else{
							alert(data);
						}
					}
				})
				
				//기존 검색 유지를 위한 전송 
				var s_type_val = $("#h_search_type").val();
				var s_text_val = $("#h_search_text").val();
				
				setTimeout(function(){window.location.reload();},1000);
				setTimeout(function(){window.opener.parent.location.href('shopPlus_stay.php?s_type='+s_type_val+'&s_text='+s_text_val);},1000);
				
			} else {
				return false;
			}
		}
		
	});
	

	//---------------------------------------------------------- 가맹점 검색
	$(document).on('click', '#btn_search', function () {
		
		// 검색 타입 가져오기
		var search_div = $(this).parent();
		var select_type = search_div.children($('#search'));
		var optionSelected = $("option:selected", select_type);
		var valueSelected = select_type.val();
		
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
		$("#h_search_type").val(valueSelected);
		$("#h_search_text").val(valueSearch_text);

		$.ajax({
			url:'shopPlus_exec.php',
			type:'post',
			data:{ search_type : valueSelected, search_text : valueSearch_text, search_id : valueSearch_id,
					search_auth : valueSearch_auth},
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
								+"<td> http://m.nfczone.co.kr/nfc_index.jsp?p="+obj[i].url+"</td>"
								+"<td>"+obj[i].shop_name+"</td>"
								+"<td>"+obj[i].position+"</td>"
								+"<td>"+obj[i].branch_name+"</td>"
								+"<td>"+obj[i].agent_name+"</td>"
								+"<td>"+obj[i].ceo_name+"</td>"
								+"<td>"+obj[i].id+"</td>"
								+"<td>"+obj[i].shop_tel+"</td>"
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
	$(document).on('click', '#branch_name_edit', function () {
		
		var br_select = $("#branch_name_edit");
		var br_val = $("#branch_name_edit").val();
		var id = $("#h_id").val();
		
		$.ajax({
			url:'shopPlus_exec.php',
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
	});

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
				url:'shopPlus_exec.php',
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
	
	//---------------------------------------------------------- 가맹점 수정 > 상점 전경 사진 파일 등록
	$("#uploadimage_store").on('submit',(function(e) {
		
		e.preventDefault();

		$("#message").empty();
		$('#loading').show();

		$.ajax({
			url: "shopPlus_upload.php", // Url to which the request is send
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
								+"<span id='error_message'>jpeg, jpg, png, gif, bmp 파일만 업로드 가능합니다.</span>");
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
	
		var url = "shopPlus_priceRegister.php?idx="+idx+"&shop_name="+shop_name;

		window.open(url, "가맹점 메뉴 등록", "width=900, height=1000, scrollbars=yes, resizable=no");
	});

	//---------------------------------------------------------- 가맹점 수정 > 사진 등록팝업 띄우기
	$(document).on('click', '#PhotoListButton', function () {
		
		var idx = $("#h_idx").val();
		var shop_name = $("input[name=shop_name]").attr("value");
	
		var url = "shopPlus_photoRegister.php?idx="+idx+"&shop_name="+shop_name;

		window.open(url, "가맹점 사진 등록", "width=900, height=1000, scrollbars=yes, resizable=no");
	});

	//---------------------------------------------------------- 가맹점 수정 > 상세 사진 파일 등록
	$("#uploadimage").on('submit',(function(e) {
		
		e.preventDefault();

		$.ajax({
			url: "shopPlus_upload.php", // Url to which the request is send
			type: "POST",             // Type of request to be send, called as method
			data: new FormData(this), // Data sent to server, a set of key/value pairs (i.e. form fields and values)
			contentType: false,       // The content type used when sending data to the server.
			cache: false,             // To unable request pages to be cached
			processData:false,        // To send DOMDocument or non processed data file it is set to false
			success: function(data){   // A function to be called if request succeeds
				
				alert(data);
				window.history.go(0);
			
			}
		})
	}));
	//---------------------------------------------------------- 가맹점 수정 > 상세 사진 파일 전체삭제
	$(document).on('click', '#delete_all_image', function () {
		var result = confirm("전체 사진을 삭제하시겠습니까? 삭제 후에는 복구가 불가합니다.");

		if(result) {

			var shop_idx = $("#h_idx").val();

			$.ajax({
				url: "shopPlus_exec.php", 
				type: "POST",             
				data: { del_photo_all_idx : shop_idx},
				contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
				dataType: 'html',	
				success: function(data){   
					
					alert(data);
					window.history.go(0);
				
				}
			})
		}

	});
	//---------------------------------------------------------- 가맹점 수정 > 상세 사진 파일 선택삭제
	$(document).on('click', '#delete_image', function () {
		var result = confirm("선택한 사진을 삭제하시겠습니까? 삭제 후에는 복구가 불가합니다.");

		if(result) {
			var shop_idx = $("#h_idx").val();

			var arr = [];
			$.each($('input[name="photo_idx[]"]:checked'), function() {
			  var value = $(this).val()

			  arr.push(value);

			})

			//alert(arr);

			$.ajax({
				url: "shopPlus_exec.php", 
				type: "POST",             
				data: { del_photo_idx : shop_idx, photo_idx : arr},
				contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
				dataType: 'html',	
				success: function(data){   
					
					alert(data);
					window.history.go(0);
				
				}
			})
		}

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

	$(".shopPlus_stay_form").on('submit',(function(e) {

		e.preventDefault();
		
		var text = $(this).find($('textarea[name="info[]"]')).val();
		var stringByte = getByteLength(text);
		
		if(stringByte > 400){
			alert("정보란에 입력 가능한 글자수를 초과하였습니다.(400자)");
			return false;
		}

		var shop_menu_div = $(this).parent();
		shop_menu_div.css("background", "pink");
		
		$.ajax({
			url: "shopPlus_exec.php", // Url to which the request is send
			type: "POST",             // Type of request to be send, called as method
			data: new FormData(this), // Data sent to server, a set of key/value pairs (i.e. form fields and values)
			contentType: false,       // The content type used when sending data to the server.
			cache: false,             // To unable request pages to be cached
			processData:false,        // To send DOMDocument or non processed data file it is set to false
			success: function(data){   // A function to be called if request succeeds
				//alert(data);
				
				shop_menu_div.css("background", "#fff");
				if(data == 'success'){
					alert("가격정보가 성공적으로 등록되었습니다.");
					window.history.go(0);
				}else {
					alert("가격정보 등록에 실패하였습니다. 입력값에 '가 있다면 삭제하고 등록해주세요.");
					return false;
				}
				
			}
		})
		
		
	}));

	//---------------------------------------------------------- 가맹점 수정 > [대실]요일 항목 추가
	$(document).on('change', '#day_time', function () {
		
		var day_num = $('#day_time option:selected').val();
		var isChange = $('#isChange_time').val();

		if(day_num > 0){
		
			if(isChange == 0){

				$("#stay_time").html("");
				$("#stay_time").css("display","table");
				$("#stay_time").css("margin-top","10px");
				$("#stay_time").css("margin-bottom","50px");
				$("#stay_time").append("<tr></tr>");
				$("#stay_time tr").append("<th>객실타입▼/요일▶</th>");
				
				for(var i = 0; i < day_num ; i++){
					$("#stay_time tr").append("<th><input type='text' name='price_type2[]' class='input_text' size='10' required></th>");
				}

				$("#stay_time tr:last").after("<tr><th><input type='text' name='name[]' class='input_text' size='10' required></th></tr>");
				
				for(var i = 0; i < day_num ; i++){
					$("#stay_time tr:last").append(
						"<td>"
							+"가격: <input type='text' name='price[]' class='input_text' size='10' required><br/>"
							+"시간정보: <textarea rows='2' cols='15' name='info[]' class='input_text' ></textarea><br/>"
						+"</td>");
				}
				
				$("#add_room_time").css("display","inline-block");

				isChange = $('#isChange').val('1');
			
			}else if(isChange > 0 ){
				var result = confirm("변경하면 정보가 초기화 됩니다. 변경 하시겠습니까?");

				if(result) {
					$("#stay_time").html("");
					$("#stay_time").css("display","table");
					$("#stay_time").append("<tr></tr>");
					$("#stay_time tr").append("<th>객실타입▼/요일▶</th>");
					
					for(var i = 0; i < day_num ; i++){
						$("#stay_time tr").append("<th><input type='text' name='price_type2[]' class='input_text' size='10' required></th>");
					}

					$("#stay_time tr:last").after("<tr><th><input type='text' name='name[]' class='input_text' size='10' required></th></tr>");
					
					for(var i = 0; i < day_num ; i++){
						$("#stay_time tr:last").append(
							"<td>"
								+"가격: <input type='text' name='price[]' class='input_text' size='10' required><br/>"
								+"시간정보: <textarea rows='2' cols='15' name='info[]' class='input_text' ></textarea><br/>"
							+"</td>");
					}
					
					$("#add_room_time").css("display","inline-block");

				}
			}
		}else if(day_num == 0){
			$("#stay_time").html("");
			$("#add_room_time").css("display","none");
		}
	
	});
	//---------------------------------------------------------- 가맹점 수정 > 객실타입 항목 추가
	$(document).on('click', '#add_room_time', function () {

		var day_num = $('#day_time option:selected').val();

		$("#stay_time tr:last").after("<tr><th><input type='text' name='name[]' class='input_text' size='10' required></th></tr>");
		
		for(var i = 0; i < day_num ; i++){
			$("#stay_time tr:last").append(
				"<td>"
					+"가격: <input type='text' name='price[]' class='input_text' size='10' required><br/>"
					+"시간정보:<textarea rows='2' cols='20' name='info[]' class='input_text' ></textarea><br/>"
				+"</td>");
		}

	});

	//---------------------------------------------------------- 가맹점 수정 > [숙박]요일 항목 추가
	$(document).on('change', '#day_day', function () {
		
		var day_num = $('#day_day option:selected').val();
		var isChange = $('#isChange_day').val();

		if(day_num > 0){
		
			if(isChange == 0){

				$("#stay_day").html("");
				$("#stay_day").css("display","table");
				$("#stay_day").css("margin-top","10px");
				$("#stay_day").css("margin-bottom","50px");
				$("#stay_day").append("<tr></tr>");
				$("#stay_day tr").append("<th>객실타입▼/요일▶</th>");
				
				for(var i = 0; i < day_num ; i++){
					$("#stay_day tr").append("<th><input type='text' name='price_type2[]' class='input_text' size='10' required></th>");
				}

				$("#stay_day tr:last").after("<tr><th><input type='text' name='name[]' class='input_text' size='10' required></th></tr>");
				
				for(var i = 0; i < day_num ; i++){
					$("#stay_day tr:last").append(
						"<td>"
							+"가격: <input type='text' name='price[]' class='input_text' size='10' required><br/>"
							+"시간정보: <textarea rows='2' cols='15' name='info[]' class='input_text' ></textarea><br/>"
						+"</td>");
				}
				
				$("#add_room_day").css("display","inline-block");

				isChange = $('#isChange').val('1');
			
			}else if(isChange > 0){
				var result = confirm("변경하면 정보가 초기화 됩니다. 변경 하시겠습니까?");

				if(result) {
					$("#stay_day").html("");
					$("#stay_day").css("display","table");
					$("#stay_day").append("<tr></tr>");
					$("#stay_day tr").append("<th>객실타입▼/요일▶</th>");
					
					for(var i = 0; i < day_num ; i++){
						$("#stay_day tr").append("<th><input type='text' name='price_type2[]' class='input_text' size='10' required></th>");
					}

					$("#stay_day tr:last").after("<tr><th><input type='text' name='name[]' class='input_text' size='10' required></th></tr>");
					
					for(var i = 0; i < day_num ; i++){
						$("#stay_day tr:last").append(
							"<td>"
								+"가격: <input type='text' name='price[]' class='input_text' size='10' required><br/>"
								+"시간정보: <textarea rows='2' cols='15' name='info[]' class='input_text' ></textarea><br/>"
							+"</td>");
					}
					
					$("#add_room_day").css("display","inline-block");

				}
			}
		}else if(day_num == 0){
			$("#stay_day").html("");
			$("#add_room_day").css("display","none");
		}
	
	});
	//---------------------------------------------------------- 가맹점 수정 > 객실타입 항목 추가
	$(document).on('click', '#add_room_day', function () {

		var day_num = $('#day_day option:selected').val();

		$("#stay_day tr:last").after("<tr><th><input type='text' name='name[]' class='input_text' size='10' required></th></tr>");
		
		for(var i = 0; i < day_num ; i++){
			$("#stay_day tr:last").append(
				"<td>"
					+"가격: <input type='text' name='price[]' class='input_text' size='10' required><br/>"
					+"시간정보:<textarea rows='2' cols='20' name='info[]' class='input_text' ></textarea><br/>"
				+"</td>");
		}

	});


	
	//---------------------------------------------------------- 가맹점 수정 > 기존 메뉴 삭제
	$(document).on('click', '#btn_price_delete', function () {
		var result = confirm("삭제하시겠습니까?");

			if(result) {
	
				var menu_div = $(this).parent();
				var shop_idx = $('#h_idx').val();
				var price_type = $(this).siblings($('#h_type')).val();

				//alert(menu_div+"/"+shop_idx+"/"+price_type);
				
				$.ajax({
					url:'shopPlus_exec.php',
					type:'post',
					data:{ del_price_idx : shop_idx, del_price_type : price_type},
					contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
					dataType: 'html',
					success:function(data){
						if(data == "success"){
							alert("삭제를 완료하였습니다.");
							window.history.go(0);
						}else{
							alert(data);
						}
					}
				})
				
				menu_div.remove();
			}
			else{}
	});


});