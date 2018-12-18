$( document ).ready(function() {
	$.datepicker.setDefaults({
        dateFormat: 'yy-mm-dd',
        prevText: '이전 달',
        nextText: '다음 달',
        monthNames: ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'],
        monthNamesShort: ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'],
        dayNames: ['일', '월', '화', '수', '목', '금', '토'],
        dayNamesShort: ['일', '월', '화', '수', '목', '금', '토'],
        dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
        showMonthAfterYear: true,
        yearSuffix: '년'
    });

	$( function() {
		$( "#date_before" ).datepicker();
		$( "#date_after" ).datepicker();

		$( "#date_before" ).datepicker( "option", "dateFormat", "yy-mm-dd");
		$( "#date_after" ).datepicker( "option", "dateFormat", "yy-mm-dd");
		
	});



	//---------------------------------------------------------- 대리점 이름 가져오기
	$( function() {
		var auth = $("#auth").val();
		//var ag_select = $("#agent");
	// 1) 지사권한
		if(auth == "branch"){
			$("#branch").css("display","none");
			$("#branch").change();
		}
	// 2) 매니저권한 (지사 선택 후)
		else if(auth == "agent"){
			$("#branch").css("display","none");
			$("#agent").css("display","none");
			$("#branch").change();
			$("#agent").change();
		}
	});

	$(document).on('change', '#branch', function () {
		var isSelect = $( "#branch option:selected" ).val();
		var br_url = $( "#branch option:selected" ).val(); // 지사에서 선택된 url으로 대리점 선택
		var ag_select = $("#agent");
		var shop_select = $("#shop");

		var isAgent = $('#isAgent').val();

		if(isAgent == 'y'){
			var id = $('#search_id').val();
		}else {
			var id = '';
		}

		if(isSelect == "0"){ 
			ag_select.find('option').remove().end();
			ag_select.append("<option value='0'>"+"대리점 선택"+"</option>");
			shop_select.find('option').remove().end();
			shop_select.append("<option value='0'>"+"가맹점 선택"+"</option>");
			return false;
		}else{
			$.ajax({
				url:'stats_exec.php',
				type:'post',
				data:{branch_url : br_url, agent_id : id},
				contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
				dataType: 'html',
				success:function(data){
					
					var str = data;
					var ag_info = str.split(","); // ag_info[0] = "대리점이름/대리점인덱스"	
					
					if(ag_info[0]){
						ag_select.find('option').remove().end();
						
						// 지사만 대리점 전체 검색 가능
						if(isAgent == 'n') ag_select.append("<option value='0'>"+"대리점 전체"+"</option>");

						for(var i = 0; i < ag_info.length; i++){
							var ag = ag_info[i].split("/"); // ag[0] = "대리점이름" , ag[1] = 대리점인덱스
							ag_select.append("<option value='"+ag[1]+"'>"+ag[0]+"</option>");
						}
						
						$("#agent").change();

					}else{
						alert("fail");
					}
				}
			})
		}
	});

	//---------------------------------------------------------- 가맹점 이름 가져오기
		$(document).on('change', '#agent', function () {
			var isSelect = $( "#agent option:selected" ).val();
			var ag_idx = $( "#agent option:selected" ).val(); // 지사에서 선택된 idx로 가맹점 선택
			var shop_select = $("#shop");

			if(isSelect == "0"){ 
				shop_select.find('option').remove().end();
				shop_select.append("<option value='0'>"+"가맹점 전체"+"</option>");
				return false;
			}else{
				$.ajax({
					url:'stats_exec.php',
					type:'post',
					data:{agent_idx : ag_idx},
					contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
					dataType: 'html',
					success:function(data){
					
						var str = data;
						var shop_info = str.split(","); // shop_info[0] = "가맹점이름/가맹점인덱스"	
						
						if(shop_info[0]){
							shop_select.find('option').remove().end();
							
							shop_select.append("<option value='0'>"+"가맹점 전체"+"</option>");

							for(var i = 0; i < shop_info.length; i++){
								var sh = shop_info[i].split(":/:"); // sh[0] = "가맹점이름" , sh[1] = 가맹점인덱스
								shop_select.append("<option value='"+sh[1]+"'>"+sh[0]+"</option>");
							}
						}else{
							alert("가맹점이 없습니다");
							shop_select.find('option').remove();
							shop_select.css("width","94px");
						}
					}
				})
			}
		});

	//---------------------------------------------------------- 기간별 검색
	$(document).on('click', '#btn_search_stats', function () {
		
		var branch = $("#branch").val();
		var agent= $("#agent").val();
		var shop= $("#shop").val();
		var before_len = $("#date_before").val().length;
		var after_len = $("#date_after").val().length;

		if(branch == 0){
			alert("지사를 반드시 선택하세요.");
			return false;
		}else if(agent == ''){
			alert("대리점을 반드시 선택하세요.");
			return false;
		}else if(shop == null){
			alert("등록된 가맹점이 없어서 검색이 불가합니다.");
			return false;
		}else if(before_len == 0){
			alert("시작 날짜를 반드시 년,월,일 모두 선택하세요.");
			return false;
		}else if(after_len == 0){
			alert("종료 날짜를 반드시 년,월,일 모두 선택하세요.");
			return false;
		}
		else if (before_len >= 1 && after_len >= 1 && branch != 0 && agent != ''  && shop != null ){
		
			   var params = jQuery("#date_search_form").serialize();
				$.ajax({
					url:'stats_exec.php',
					type:'post',
					data:params,
					contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
					dataType: 'html',
					success:function(data){
						
						if(data == '["empty"]' || data == '[]') {
							alert("검색결과가 없습니다.");
							$(".insert_item").remove();
							$(".insert_data tr:first").after(
									"<tr class='list_item insert_item'>"
										+"<td colspan='7'> 검색 결과가 없습니다.</td>"
									+"</tr>"
								);
						}else {
							
							var obj =  $.parseJSON( data );
							$(".insert_data").css("display","table");
							$(".list_all").toggle();
							$(".list_view").toggle();

							$(".insert_item").remove();
							
							var total_mo = 0;
							var total_app = 0;
							var total_all = 0;

							for (var i in obj) {
								
								var call_mo = parseInt(obj[i].s_cnt_mo) + parseInt(obj[i].s_add_sum_mo);
								var call_app = parseInt(obj[i].s_cnt_app) + parseInt(obj[i].s_add_sum_app);
								var total = call_mo+call_app;

								total_mo += call_mo;
								total_app += call_app;
								total_all += total;
								
								$(".insert_data tr:first").after(
									"<tr class='list_item insert_item'>"
										+"<td>"+obj[i].mydate+"</td>"
										+"<td>"+obj[i].branch_name+"</td>"
										+"<td>"+obj[i].agent_name+"</td>"
										+"<td>"+obj[i].shop_name+"</td>"
										+"<td>"+call_mo+"</td>"
										+"<td>"+call_app+"</td>"
										+"<td>"+total+"</td>"
									+"</tr>"
								);
							}
							
							$(".insert_data tr:first").after(
								"<tr class='list_item insert_item'>"
									+"<td colspan='4'>전체 합계</td>"
									+"<td>"+total_mo+"</td>"
									+"<td>"+total_app+"</td>"
									+"<td>"+total_all+"</td>"
								+"</tr>"
							);

							
						}
						
					},
					error: function (request, status, error)
					{
						alert("error! please check console");
						console.log('code: '+request.status+"\n"+'message: '+request.responseText+"\n"+'error: '+error);
					}
					
				})
		} // end else if
		
	});

	//---------------------------------------------------------- 전체보기
	$(document).on('click', '.list_view', function () {
		$(".list_all").toggle();
		$(".list_view").toggle();
	});
	
	//---------------------------------------------------------- [팝업] 기간별 검색
	$(document).on('click', '#btn_search', function () {
		
		var before_len = $("#date_before").val().length;
		var after_len = $("#date_after").val().length;
		var id = $("#search_id").val();
		var idx = $("#idx").val();
	
		if(before_len == 0){
			alert("시작 날짜를 반드시 년,월,일 모두 선택하세요.");
			return false;
		}else if(after_len == 0){
			alert("종료 날짜를 반드시 년,월,일 모두 선택하세요.");
			return false;
		}
		else if (before_len >= 1 && after_len >= 1 ){
			
			   var params = jQuery("#date_search_form").serialize();
				$.ajax({
					url:'stats_exec.php',
					type:'post',
					data:params,
					contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
					dataType: 'html',
					success:function(data){
						
						var obj =  $.parseJSON( data );
						var num = $(".lisr_data tr").length;

						$(".list_item").remove();
						$(".list_new").remove();
						$(".page").remove();

						obj.reverse();

						for (var i in obj) {
							
							//----------------
							var icon = "";
							if(obj[i].url == null || obj[i].url == '' || obj[i].url == undefined ){
								icon = "icon_call_01.png";
							}else icon = "icon_call_02.png";
							
							//----------------
							var state = "";
							var class_name = "";
							
							if(obj[i].state == "S") { 
								class_name = "success";
								state = "완료";
							}else if(obj[i].state == "C"){
								class_name = "fail";
								state = "취소";
							}else if(obj[i].state == "O"){
								class_name = "";
								state = "접수대기";
							}
							//----------------
							var h1 = obj[i].hpno.substr(0,3);
							var h2 = obj[i].hpno.substr(3,4);
							var h3 = obj[i].hpno.substr(7,4);
							var hpno = h1+"-"+h2+"-"+h3;
							
							var num = parseInt(i)+1;

							$(".list_data tr:first").after(
								"<tr class='list_item "+class_name+"'>"
									+"<td>"
										+num
										+"<img src='../images/"+icon+"' width='50px'/>"
									+"</td>"
									+"<td  style='font-weight:bolder'>"+hpno+"</td>"
									+"<td>"+obj[i].credate+"</td>"
									+"<td>"+obj[i].current_position+"</td>"
									+"<td>"+obj[i].dst_position+"</td>"
									+"<td>"+obj[i].mid_pass+"</td>"
									+"<td>"+obj[i].add_call+"</td>"
									+"<td>"+obj[i].price+"</td>"
									+"<td>"+"</td>"
									+"<td>"+state+"</td>"
								+"</tr>"
							);
						}
						
					},
					error: function (request, status, error)
					{
						alert("error! please check console");
						console.log('code: '+request.status+"\n"+'message: '+request.responseText+"\n"+'error: '+error);
					}
					
				})
		} // end else if
	});
	
	//---------------------------------------------------------- 가맹점 팝업 띄우기
	$(document).on('click', '.list_click', function () {
		
		var idx = jQuery("#shop_idx", this).val();
		var id = $("#search_id").val();
		var auth = $("#auth").val();
	
		var url = "";

		url = "statsSearch.php?idx="+idx+"&id="+id+"&auth="+auth;
		
		window.open(url, "가맹점 기간별 호출 검색", "width=900, height="+$(window).height()+", scrollbars=yes, resizable=no");
	});

	//---------------------------------------------------------- 전체보기
	$(document).on('click', '#btn_all', function () {
		window.location.reload();
	});

	//---------------------------------------------------------- 완료콜 검색
	$(document).on('click', '#btn_ok', function () {
		
		var idx = $("#idx").val();
		
		$.ajax({
			url:'stats_exec.php',
			type:'post',
			data:{ shop_idx : idx, state_search : "S" },
			contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
			dataType: 'html',
			success:function(data){
				
				var obj =  $.parseJSON( data );
				var num = $(".lisr_data tr").length;

				$(".list_item").remove();
				$(".list_new").remove();
				$(".page").remove();

				obj.reverse();

				for (var i in obj) {
					
					//----------------
					var icon = "";
					if(obj[i].url == null || obj[i].url == '' || obj[i].url == undefined ){
						icon = "icon_call_01.png";
					}else icon = "icon_call_02.png";
					
					//----------------
					var state = "";
					var class_name = "";
					
					if(obj[i].state == "S") { 
						class_name = "success";
						state = "완료";
					}else if(obj[i].state == "C"){
						class_name = "fail";
						state = "취소";
					}else if(obj[i].state == "O"){
						class_name = "";
						state = "접수대기";
					}
					//----------------
					var h1 = obj[i].hpno.substr(0,3);
					var h2 = obj[i].hpno.substr(3,4);
					var h3 = obj[i].hpno.substr(7,4);
					var hpno = h1+"-"+h2+"-"+h3;
					
					var num = parseInt(i)+1;

					$(".list_data tr:first").after(
						"<tr class='list_item "+class_name+"'>"
							+"<td>"
								+num
								+"<img src='../images/"+icon+"' width='50px'/>"
							+"</td>"
							+"<td  style='font-weight:bolder'>"+hpno+"</td>"
							+"<td>"+obj[i].credate+"</td>"
							+"<td>"+obj[i].current_position+"</td>"
							+"<td>"+obj[i].dst_position+"</td>"
							+"<td>"+obj[i].mid_pass+"</td>"
							+"<td>"+obj[i].add_call+"</td>"
							+"<td>"+obj[i].price+"</td>"
							+"<td>"+"</td>"
							+"<td>"+state+"</td>"
						+"</tr>"
					);
				}
				
			},
			error: function (request, status, error)
			{
				alert("error! please check console");
				console.log('code: '+request.status+"\n"+'message: '+request.responseText+"\n"+'error: '+error);
			}
			
		})
	});

});