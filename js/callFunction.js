$( document ).ready(function() {

	// 호출 등록 코드
	$(document).on('click', '#rgstButton', function () {

		var c_po = $("#current_position").val().length;
		var d_po = $("#dst_position").val().length;
		var branch = $("#branch").val();
		var agent = $("#agent").val();
		var shop = $("#shop_name").val();

		if(c_po == 0){
			alert("출발지를 반드시 입력하세요.");
			return false;
		}else if(d_po == 0){
			alert("도착지를 반드시 입력하세요.");
			return false;
		}
		/*
		else if(branch == 0){
			alert("지점을 반드시 선택하세요.");
			return false;
		}
		else if(agent == 0){
			alert("대리점을 반드시 선택하세요.");
			return false;
		}
		else if(shop == 0){
			alert("가맹점을 반드시 선택하세요.");
			return false;
		}
		*/
		else if (c_po >= 1 && d_po >= 1){ // && branch != 0 && agent != 0 && shop != 0
			var result = confirm("등록하시겠습니까?");

			if(result) {
			   var params = jQuery("#call_rgst_form").serialize();
				$.ajax({
					url:'call_exec.php',
					type:'post',
					data:params,
					cache:false,
					contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
					dataType: 'html',
					success:function(data){


						var obj =  $.parseJSON( data );
						var num = $("#insert_data tr").length;

						var state_param = obj[0].state;
						var class_name = "";
						var state_num = 0;
						var state_text = "";
						var state = [];

							if (state_param == "S") { // 완료
								class_name = "success";
								state_num = 1;
								state_text = "완료";
							}else if (state_param == "C") { // 최소
								class_name = "fail";
								state_num = 2;
								state_text = "취소";
							}
							else if (state_param == "O") { //접수 대기
								class_name = "";
								state_num = 0;
								state_text = "접수대기";
							}

							state[state_num] = "selected";

						/*

						$("#insert_data tr:first").after(
							"<tr class='list_item'>"
								+"<input type='hidden' name='idx' value='"+obj[0].idx+"' />"
								+"<td>"+num+"</td>"
								+"<td>"+obj[0].shop_name+"</td>"
								+"<td>"+obj[0].shop_tel+"</td>"
								+"<td>"+obj[0].hpno+"</td>"
								+"<td>"+obj[0].credate+"</td>"
								+"<td>"+obj[0].current_position+"</td>"
								+"<td>"+obj[0].dst_position+"</td>"
								+"<td>"+obj[0].mid_pass+"</td>"
								+"<td>"+obj[0].add_call+"</td>"
								+"<td>"+obj[0].price+"</td>"
								+"<td>"++"</td>" //규정요금
								+"<td>"
									+"<select class='state "+class_name+"' name='state'  id='select_state'>"
										+"<option value='O' '"+state[0]+"'>접수 대기</option>"
										+"<option value='S' '"+state[1]+"'>완료</option>"
										+"<option value='C' '"+state[2]+"'>취소</option>"
									+"</select>"
								+"</td>"
								+"<td>"+obj[0].branch_name+"</td>"
								+"<td>"+obj[0].agent_name+"</td>"
							+"</tr>"
						);
						*/
						window.location.reload();

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


	});


	// 호출관리 상태변경 코드
	$( ".select_state" ).change(function () {

		var result = confirm("상태를 변경하시겠습니까?");

		if(result){
			var item = "state";

			var optionSelected = $("option:selected", this);
			var valueSelected = this.value;

			var selectedParent = optionSelected.parent();
			selectedParent.parents("tr").addClass( "selected" );

			var a = $(this).parent();
			var b = a.parent();
			var selectedIDX = b.children($(".idx")).val();

			//YES : 변경된 값 서버전송
			$.ajax({
				url:'call_exec.php',
				type:'post',
				data:{ item_name : item, value : valueSelected, change_idx : selectedIDX},
				contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
				dataType: 'html',
				success:function(data){
					if(data == "success"){

						// 변경된 tr 색상 변경
						switch(valueSelected){
							case "O" : selectedParent.parents("tr").css("background", "#fff");
										selectedParent.parents("tr").children().children().css("background", "#fff");
										break;
							case "S" : selectedParent.parents("tr").css("background", "#62faff");
										selectedParent.parents("tr").children().children().css("background", "#62faff");
										break;
							case "B" : selectedParent.parents("tr").css("background", "#f7f078");
										selectedParent.parents("tr").children().children().css("background", "#f7f078");
										break;
							case "C" : selectedParent.parents("tr").css("background", "#ffa7c4");
										selectedParent.parents("tr").children().children().css("background", "#ffa7c4");
										break;
							case "D" : selectedParent.parents("tr").css("background", "#ffa7c4");
										selectedParent.parents("tr").children().children().css("background", "#ffa7c4");
										break;
							case "E" : selectedParent.parents("tr").css("background", "#ffa7c4");
										selectedParent.parents("tr").children().children().css("background", "#ffa7c4");
										break;
							case "F" : selectedParent.parents("tr").css("background", "#ffa7c4");
										selectedParent.parents("tr").children().children().css("background", "#ffa7c4");
										break;
						}
						$(this).blur();

					}else{
						alert(data);
					}
				}
			})
			return true;

		}else {
			//No : 기존 값 셋팅
			var originValue = $(this).siblings("input").val();

			$(this).val(originValue);
			$(this).blur();
			return true;
		}
	});

	// 호출관리 출발지 변경 코드
	$( ".btn_curr_position" ).click(function () {
			var result = confirm("등록하시겠습니까?");

			if(result) {
				var item = "current_position";
				var select = $(this).parent();
				var select_tr = select.parent();
				var v = $(this).siblings($(".input_curr_position")).val();
				var idx = select_tr.children($(".idx")).val();

				$.ajax({
					url:'call_exec.php',
					type:'post',
					data:{ item_name : item, value: v, change_idx : idx},
					contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
					dataType: 'html',
					success:function(data){
						if(data == "success"){
							select.children().css("background","red");
							setTimeout(function(){select.children().css("background","#fff");},500);
						}else{
							alert(data);
						}
					}
				})
			} else {
				return false;
			}
	});

	// 호출관리 목적지 변경 코드
	$(document).on('click', '.btn_position', function() {

			var result = confirm("등록하시겠습니까?");

			if(result) {
				var item = "dst_position";
				var select = $(this).parent();
				var select_tr = select.parent();
				var position = $(this).siblings($("textarea.input_position"));
				var idx = select_tr.children($(".idx")).val();

				// 주소 갯수를 저장하는 변수
				var input_num = 0;

				//유효한 주소 갯수
				position.each(function(){
					if(typeof($(this).attr('class')) === 'undefined'){}
					else{
						 input_num++;
					}
				});

				//주소를 저장하는 변수
				var v = "";

				//단일 주소 처리
				if(input_num == 1){

					v = position.val();
					var check_flag = v.indexOf('*');
					if(check_flag != -1){
						alert(" 주소에 *을 포함할 수 없습니다.");
						return false;
					}
				// 복수 주소 처리 '*' 로 구분
				}else if(input_num > 1){
					position.each(function(){
						if(typeof($(this).attr('class')) === 'undefined'){}
						else{
							v += $(this).val() + "*";
						}
					});
					v = v.slice(0,-1);
				}

				$.ajax({
					url:'call_exec.php',
					type:'post',
					data:{ item_name : item, value: v, change_idx : idx},
					contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
					dataType: 'html',
					success:function(data){
						if(data == "success"){
							select.children().css("background","red");
							setTimeout(function(){select.children().css("background","#fff");},500);
						}else{
							alert(data);
						}
					}
				})

			} else {
				return false;
			}
	});

	// 호출관리 금액 변경 코드
	$(document).on('click', '.btn_price', function() {

			var result = confirm("등록하시겠습니까?");

			if(result) {
				var item = "price";
				var select = $(this).parent();
				var select_tr = select.parent();
				var price = $(this).siblings($(".input_price"));
				var idx = select_tr.children($(".idx")).val();

				// 데이터 갯수를 저장하는 변수
				var input_num = 0;

				//유효한 데이터 갯수
				price.each(function(){
					if(typeof($(this).attr('class')) === 'undefined'){}
					else{
						 input_num++;
					}
				});

				//데이터를 저장하는 변수
				var v = "";

				//단일 처리
				if(input_num == 1){

					v = price.val();
					var check_flag = v.indexOf('*');
					if(check_flag != -1){
						alert(" 요금에 *을 포함할 수 없습니다.");
						return false;
					}
				// 복수 처리 '*' 로 구분
				}else if(input_num > 1){
					price.each(function(){
						if(typeof($(this).attr('class')) === 'undefined'){}
						else{
							v += $(this).val() + "*";
						}
					});
					v = v.slice(0,-1);
				}

				$.ajax({
					url:'call_exec.php',
					type:'post',
					data:{ item_name : item, value: v, change_idx : idx},
					contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
					dataType: 'html',
					success:function(data){
						if(data == "success"){
							select.children().css("background","red");
							setTimeout(function(){select.children().css("background","#fff");},500);
						}else{
							alert(data);
						}
					}
				})

			} else {
				return false;
			}
	});

	// 호출관리 경유지 변경 코드
	$( ".btn_mid_pass" ).click(function () {
			var result = confirm("등록하시겠습니까?");

			if(result) {
				var item = "mid_pass";
				var select = $(this).parent();
				var select_tr = select.parent();
				var v = $(this).siblings($(".input_mid_pass")).val();
				var idx = select_tr.children($(".idx")).val();

				$.ajax({
					url:'call_exec.php',
					type:'post',
					data:{ item_name : item, value: v, change_idx : idx},
					contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
					dataType: 'html',
					success:function(data){
						if(data == "success"){
							select.children().css("background","red");
							setTimeout(function(){select.children().css("background","#fff");},500);
						}else{
							alert(data);
						}
					}
				})
			} else {
				return false;
			}
	});

	// 호출관리 추가콜 변경 코드
	$( ".btn_add_call" ).click(function () {
			var result = confirm("등록하시겠습니까?");

			if(result) {
				var item = "add_call";
				var select = $(this).parent();
				var select_tr = select.parent();
				var v = $(this).siblings($(".input_add_call")).val();
				var idx = select_tr.children($(".idx")).val();

				var pos_td = select_tr.find($(".call_pos"));
				var price_td = select_tr.find($(".call_price"));

				var pos_input = select_tr.find($(".call_pos")).find($(".input_position"));
				var price_input = select_tr.find($(".call_price")).find($(".input_price"));
				var pos_input_val = select_tr.find($(".call_pos")).find($(".input_position")).val();
				var price_input_val = select_tr.find($(".call_price")).find($(".input_price")).val();

				//추가콜 저장하는 변수
				var add_call = parseInt(v);

				var pos_text = "";
				var pos_v = "";
				var price_text = "";
				var price_v = "";

				var pos_array = pos_text.split('*');
				var price_array = price_text.split('*');

				if(add_call == 0){

					pos_text = pos_input.val();
					price_text = price_input.val();
					var check_flag = pos_text.indexOf('*');
					if(check_flag != -1){
						alert(" 주소에 *을 포함할 수 없습니다.");
						return false;
					}
					check_flag = price_text.indexOf('*');
					if(check_flag != -1){
						alert(" 요금에 *을 포함할 수 없습니다.");
						return false;
					}

					pos_td.empty();
					price_td.empty();

					pos_td.html("<textarea rows='2' cols='30' name='input_position' class='input_position' style='display:block;' "
							+">"+pos_text
							+"</textarea><br/>"
							+"<input type='button' name='btn_position' class='btn_position' value='등록'/>");
					price_td.html("<input type='text' name='input_price' class='input_price' size='7' "
							+"value='"+price_text+"'><br/>"
							+"<input type='button' name='btn_price' class='btn_price' value='등록' />");

					$.ajax({
						url:'call_exec.php',
						type:'post',
						data:{ item_name : "dst_position", value: pos_text, change_idx : idx},
						contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
						dataType: 'html',
						success:function(data){
							if(data == "success"){
								select.children().css("background","red");
								setTimeout(function(){select.children().css("background","#fff");},500);
							}else{
								alert(data);
							}
						}
					})

					$.ajax({
						url:'call_exec.php',
						type:'post',
						data:{ item_name : "price", value: price_text, change_idx : idx},
						contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
						dataType: 'html',
						success:function(data){
							if(data == "success"){
								select.children().css("background","red");
								setTimeout(function(){select.children().css("background","#fff");},500);
							}else{
								alert(data);
							}
						}
					})


				}
				else if( add_call >= 1 && add_call <= 4){

					// 복수 주소 처리 '*' 로 구분
					if(add_call >= 1){
						pos_input.each(function(){
							if(typeof($(this).attr('class')) === 'undefined'){}
							else{
								pos_text += $(this).val() + "*";
							}
						});
						pos_text = pos_text.slice(0,-1);
						price_input.each(function(){
							if(typeof($(this).attr('class')) === 'undefined'){}
							else{
								price_text += $(this).val() + "*";
							}
						});
						price_text = price_text.slice(0,-1);


					}

					pos_array = pos_text.split('*');
					price_array = price_text.split('*');

					for(var i = 0; i < (add_call+1); i++){

						pos_v += pos_array[i] + "*";
						price_v += price_array[i] + "*";

					}
					pos_v = pos_v.slice(0,-1);
					price_v = price_v.slice(0,-1);

					pos_td.empty();
					price_td.empty();

					pos_td.html("<textarea rows='2' cols='30' name='input_position' class='input_position' style='display:block;' "
							+">"+pos_array[0]
							+"</textarea><br/>"
							+"<input type='button' name='btn_position' class='btn_position' value='등록'/>");
					price_td.html("<input type='text' name='input_price' class='input_price' size='7' "
							+"value='"+price_array[0]+"'><br/>"
							+"<input type='button' name='btn_price' class='btn_price' value='등록' />");

					pos_input = select_tr.find($(".call_pos")).find($(".input_position"));
					price_input = select_tr.find($(".call_price")).find($(".input_price"));

					pos_input.before("<span style='display:block; float:left'>주소1 : </span>");
					price_input.before("<span style='display:block; float:left'>주소1 요금 : </span>");


					var add_num = add_call;
					for(var i = 0; i < add_call; i++){
						--add_num;

						if (typeof pos_array[add_num+1]  !== 'undefined' && pos_array[add_num+1].length > 0) {
						}else{
							pos_array[add_num+1] = '';
							price_array[add_num+1] = '';
						}

						pos_input.after(
							"<br/>"
							+"<span style='display:block; float:left'>주소"+(add_num+2)+" : </span>"
							+"<textarea rows='2' cols='30' name='input_position' class='input_position' style='display:block;' >"
							+pos_array[add_num+1]
							+"</textarea>"
							);

						price_input.after(
							"<br/>"
							+"<span style='display:block; float:left'>주소"+(add_num+2)+" 요금 : </span>"
							+"<input type='text' name='input_price' class='input_price' size='7' value='"+price_array[add_num+1]+"'>"
							);
					}

					pos_v = "";
					price_v = "";

					pos_array = pos_text.split('*');
					price_array = price_text.split('*');

					for(var i = 0; i <= add_call; i++){
						if (typeof pos_array[i]  !== 'undefined' && pos_array[i].length > 0) {
						}else{
							pos_array[i] = "";
							price_array[i] = "";
						}
						pos_v += pos_array[i] + "*";
						price_v += price_array[i] + "*";

					}

					pos_v = pos_v.slice(0,-1);
					price_v = price_v.slice(0,-1);


					$.ajax({
						url:'call_exec.php',
						type:'post',
						data:{ item_name : "dst_position", value: pos_v, change_idx : idx},
						contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
						dataType: 'html',
						success:function(data){

							if(data == "success"){
								select.children().css("background","red");
								setTimeout(function(){select.children().css("background","#fff");},500);
							}else{
								alert(data);
							}
						}
					})

					$.ajax({
						url:'call_exec.php',
						type:'post',
						data:{ item_name : "price", value: price_v, change_idx : idx},
						contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
						dataType: 'html',
						success:function(data){

							if(data == "success"){
								select.children().css("background","red");
								setTimeout(function(){select.children().css("background","#fff");},500);
							}else{
								alert(data);
							}
						}
					})


				}else if (add_call > 4)
				{
					alert("최대 4콜까지 추가할 수 있습니다.");
					return false;
				}

				$.ajax({
					url:'call_exec.php',
					type:'post',
					data:{ item_name : item, value: v, change_idx : idx},
					contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
					dataType: 'html',
					success:function(data){

						if(data == "success"){
							select.children().css("background","red");
							setTimeout(function(){select.children().css("background","#fff");},500);
						}else{
							alert(data);
						}
					}
				})




			} else {
				return false;
			}
	});

	//---------------------------------------------------------- 호출정보 검색
	/*
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

		$.ajax({
			url:'call_exec.php',
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
								+"<td>"+obj[i].branch_name+"</td>"
								+"<td>"+obj[i].agent_name+"</td>"
								+"<td>"+obj[i].ceo_name+"</td>"
								+"<td>"+obj[i].id+"</td>"
								+"<td>"+obj[i].shop_tel+"</td>"
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
	*/
	
	
	function formatDate(date) {
		var d = new Date(date),
				month = '' + (d.getMonth() + 1),
				day = '' + d.getDate(),
				year = d.getFullYear();

		if (month.length < 2) month = '0' + month;
		if (day.length < 2) day = '0' + day;

		return [year, month, day].join('-');
	}
	

	
	//호출기록 날짜 초기값
	$(window).on('load', function(){
		var date = new Date();
		$("#history_date_end").val(formatDate(date));
		date.setDate(date.getDate()-1);
		$("#history_date_start").val(formatDate(date));
	});
	
	//호출기록 필터 - 어제
	$(document).on("click", "#select_yesterday_btn", function(){
		var date = new Date();
		$("#history_date_end").val(formatDate(date));

		date.setDate(date.getDate()-1);
		$("#history_date_start").val(formatDate(date));
		$("#filter_date_btn").click();
	});
	
	//호출기록 필터 - 일주일 전
	$(document).on("click", "#select_weekago_btn", function(){
		var date = new Date();
		$("#history_date_end").val(formatDate(date));

		date.setDate(date.getDate()-7);
		$("#history_date_start").val(formatDate(date));
		$("#filter_date_btn").click();
	});
	
	//호출기록 필터 - 한달 전
	$(document).on("click", "#select_monthago_btn", function(){
		var date = new Date();
		$("#history_date_end").val(formatDate(date));

		date.setMonth(date.getMonth()-1);
		$("#history_date_start").val(formatDate(date));
		
		$("#filter_date_btn").click();
	});

	//호출기록 필터 - 상태별로 보기
	$(document).on("change", ".select_status", function(){
		var status = $(this).val();
		if( status == 0 ){
			location.reload();
			return;
		}
		$.ajax({
			url: 'call_history_exec.php',
			type: 'POST',
			data: { select_status: status },
			success: function(data){
				$("tr.insert_item").remove();
				$("tr:first").after(data);
			}	
		})
	});

	//호출기록 가맹점 검색(엔터)
	$(document).on('keyup', '.search_shop_text', function(e){
		if(e.keyCode == 13){
			$(".search_shop_btn").click();
		}
	});
	//호출기록 가맹점 검색(클릭)
	$(document).on('click', '.search_shop_btn', function(){
		var text = $(".search_shop_text").val();
		window.resizeTo(650, 350);
		$.ajax({
			url: 'call_history_exec.php',
			type: 'POST',
			data: { search_shop_text : text },
			success: function(data){
				$("tr.insert_item").remove();
				$("tr:first").after(data);
				$(".history_search_shop").show();
				$(".search_input").css("margin-top", "25px");
			}
		})
	});
	//호출기록 가맹점 검색후 가맹점별로 보기
	$(document).on('click', '.history_search_shop .searchRs_row', function(){
		var code = $(this).find("td:last").text();
		
		var table_page = window.opener;
		$.ajax({
			url: 'call_history_exec.php',
			type: 'POST',
			data: { shop_code : code },
			success: function(data){		
				table_page.$("tr.insert_item").remove();
				table_page.$("tr:first").after(data);
				window.close();
			}
		})	
	});
	
	//호출기록 추가 - 주소 검색
	$(document).on('keyup', '.insert_shop', function(e){
		if(e.keyCode == 13)
			$("#shop_addr_btn").click();
	});
	$(document).on('click', '#shop_addr_btn', function(){
		var shop_name = $(".insert_shop").val();
		$.ajax({
			url: 'call_history_exec.php',
			type: 'POST',
			data: { shop_name : shop_name },
			success: function(data){
				$(".searchRs_row").remove();
				$(".insert_search_result tr:first").after(data);
				$(".shop_name_search_result").show();
			}
		})
	});
	
	//호출기록 추가 - 주소 입력
	$(document).on('click', '.insert_search_result .searchRs_row', function(){
		$(this).find("input[name='search_shop_radio']").prop("checked", true);

		var shop_name = $(this).children("td:eq(0)").text();
		var addr = $(this).children("td:eq(1)").text();
		
		$(".insert_shop").val(shop_name);
		$(".insert_addr").val(addr);
	
	});

	//호출기록 추가 - DB 항목 입력
	$(document).on('click', '#insert_history_btn', function(){
		var code = $("input[name='search_shop_radio']:checked").val();
		var datetime = $(".insert_date").val() +" "+$(".insert_time").val();
		console.log(datetime);
		$.ajax({
			url: 'call_history_exec.php',
			type: 'POST',
			data: { insert_url : code, date_time : datetime },
			success: function(){
				alert("추가가 완료되었습니다.");
				window.close();
				window.opener.location.reload();
			}
		})
	});	
	
	//호출기록 리스트 클릭시 체크박스 토글
	$(document).on('click', '.history_row', function(e){
		if(e.target.nodeName != "INPUT")
			var checkbox = $(this).find('.check_item').trigger('click');	
	});
	
	//호출기록 체크시 리스트 명암 토글
	$(document).on('change', '.check_item', function(){
		if(this.checked == true){
			$(this).closest(".history_row").css("background-color", "#f4d9d9");
		}else{
			$(this).closest(".history_row").css("background-color", "#fff");
		}
	});
	
	//호출기록 체크박스 모두 선택/해제
	$(document).on('click', '.check_all', function(){
		if(this.checked == true){
			$(".check_item").prop("checked", true);
			$(".history_row").css("background-color", "#f4d9d9");
		}else{
			$(".check_item").prop("checked", false);
			$(".history_row").css("background-color", "#fff");
		}
	})

	//호출기록 상태 변경
	$(document).on('click', '.call_method_btn', function(){
		var select = $('.select_item_condition').val();
		var items, status = 0;
		var btn = $(this).attr('id');
		
		if(select == 1)	items = $(".check_item:checked");

		if(btn == 'call_complete_btn') status = "2"; 
		else if(btn == 'call_cancel_btn') status = "3";
		else if(btn == 'call_waiting_btn') status = "1";
		
		if(status > 0){
			items.each(function(){
				update_call_status($(this).val(), status);
			});
		}
	});
		
	//호출기록 항목 삭제
	$(document).on('click', '#call_delete_btn', function(){
		var select = $('.select_item_condition').val();
		var items;

		if(select == 1) items = $(".check_item:checked");

		items.each(function(){
			var idx = $(this).val();
			$.ajax({
				url : 'call_history_exec.php',
				type : 'POST',
				data : {idx_del : idx},
				success : function(){
					location.reload();
				}
			})
		});
	});

	//호출기록 - 날짜별 조회
	$(document).on("click", "#filter_date_btn", function(){
		var date_start = $("#history_date_start").val();
		var date_end = $("#history_date_end").val();
		
		$.ajax({
			url: 'call_history_exec.php',
			type: 'POST',
			data: { date_start : date_start, date_end : date_end },
			success: function(data){
				$("tr.insert_item").remove();
				$("tr:first").after(data);
			}
		})
	});

	//호출기록 검색창 팝업
	$(document).on('click', '#search_shop_pop', function(){	
		window.open("search_shop_pop.php", "", "width=350,height=200,menubar=no,titlebar=no,status=no");
	});
	
	//호출기록 추가창 팝업
	$(document).on('click', '#call_insert_btn', function(){
		window.open("insert_history_pop.php", "", "width=650,height=400,menubar=no,titlebar=no,status=no");
	});

	//호출기록 상태변경
	function update_call_status(idx, status){
		$.ajax({
			url: 'call_history_exec.php',
			type: 'POST',
			data: { idx_call : idx, status : status },
			success: function(){
				location.reload();
			}
		})
	}
});
