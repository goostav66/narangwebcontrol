//commerceFunction.js

$(document).ready(function(){
	
	//광고 업소 신규 등록(팝업)
	$(document).on('click', '#btn_regist_incommerce', function(){
		var url = "inCommerce_regist.php";
		window.open(url, "광고업소 신규등록", "width=600, height=500, scrollbars=yes, resizable=no");
	});
	
	$(document).on('click', '#btn_regist_excommerce', function(){
		var url = "exCommerce_regist.php";
		window.open(url, "외부업체 광고등록", "width=600, height=600, scrollbars=yes, resizable=no")
	});
	
	//광고업소 - 정보변경 팝업
	$(document).on('click', '#incommerce_modify', function(){
		var sc_idx = $("#selected_row_idx").val();
		var url = "inCommerce_regist.php?idx="+sc_idx;
		window.open(url, "광고업소 정보변경", "width=600, height=500, scrollbars=yes, resizable=no");
		
	});
	
	//광고 업소 - 시작일/만료일순 보기
	$(document).on('click', '.inCommerce_btn_set .order_date', function(){
		var checkbox = $(this).next("input");
		var date = checkbox.attr('name');
		var order = "";
		
		checkbox.prop('checked', !checkbox.prop('checked'));
		if(checkbox.prop('checked'))
			order = "desc";
		
		var table = $(".list_data");
		var insert_item = $(".insert_item");
		
		var load_location_code = $("input[name='load_location_code']").val();
		
		$.ajax({
			url: 'shopPlus_commerce_exec.php',
			type: 'POST',
			data: { incommerce_date : date, incommerce_order : order, location_code : load_location_code },
			success: function(data){
				insert_item.remove();
				table.append(data);
			}
		})
		
	});
	
	//외부업체 광고 - 시작일/만료일순 보기
	$(document).on('click', '.exCommerce_btn_set .order_date', function(){
		var checkbox = $(this).next("input");
		var date = checkbox.attr('name');
		var order = "";
		
		checkbox.prop('checked', !checkbox.prop('checked'));
		if(checkbox.prop('checked'))
			order = "desc";
			
		var table = $(".list_data");
		var insert_item = $(".insert_item");

		$.ajax({
			url: 'shopPlus_commerce_exec.php',
			type: 'POST',
			data: { excommerce_date : date, excommerce_order : order },
			success: function(data){
				insert_item.remove();
				table.append(data);
			}
		})
	});	
	
	//광고 업소 - 같은 지역 업소 보기
	$(document).on('click', '#incommerce_equal', function(){
		var selected_row = getSelectedRow();
		var location_code = selected_row.find("input[name='s_location_code']").val();
		
		var table = $(".list_data");
		var insert_item = $(".insert_item");
		
		$("input[name='load_location_code']").val(location_code);
		
		$.ajax({
			url: 'shopPlus_commerce_exec.php',
			type: 'POST',
			data: { location_code : location_code , incommerce_date : 'regdate', incommerce_order : 'DESC' },
			success: function(data){
				insert_item.remove();
				table.append(data);
				$(".background_incommerce").click();
				$("#btn_view_all_incommerce").show();
			}
		})
		
	});
	
	//광고 업소 - 지역별 보기 리스트 토글
	$(document).on('click', '#btn_view_locationally', function(){
		$(".panel_view_locationally").toggle();
	});
	
	//전체 보기
	$(document).on('click', '#btn_view_all_incommerce', function(){
		location.reload();
	});
	
	//광고 업소 - 지역별 보기 
	$(document).on('change', '#view_location_list_city', function(){
		var code = $("#view_location_list_city option:selected").val();
	
		var list = $("#view_location_list_dist");
		$.ajax({
			url: 'shopPlus_commerce_exec.php',
			type: 'POST',
			data: { selected_city_location_code : code },
			success: function(data){
				list.empty();
				list.append(data);
				viewLocationallyByDist();
			}
		})			
	});
	$(document).on('change', '#view_location_list_dist', function(){
		viewLocationallyByDist();
	});	
	function viewLocationallyByDist(){
		var location_code = $("#view_location_list_dist").val();
		
		var table = $(".list_data");
		var insert_item = $(".insert_item");
		
		$("input[name='load_location_code']").val(location_code);
		
		$.ajax({
			url: 'shopPlus_commerce_exec.php',
			type: 'POST',
			data: { location_code : location_code , incommerce_date : 'regdate', incommerce_order : 'DESC' },
			success: function(data){
				insert_item.remove();
				table.append(data);
				$(".background_incommerce").click();
				$("#btn_view_all_incommerce").show();
			}
		})
	}
	$(document).on('change', '#view_location_list_city_excommerce', function(){
		var code = $("#view_location_list_city_excommerce option:selected").val();
	
		var list = $("#view_location_list_dist_excommerce");
		$.ajax({
			url: 'shopPlus_commerce_exec.php',
			type: 'POST',
			data: { selected_city_location_code : code },
			success: function(data){
				list.empty();
				list.append(data);
				viewExcommerceLocationallyByDist();
			}
		})		
	});

	$(document).on('change', '#view_location_list_dist_excommerce', function(){
		viewExcommerceLocationallyByDist();
	});

	function viewExcommerceLocationallyByDist(){
		var e_type = $("input[name='hosting']:checked").val();
		var location_code = $("#view_location_list_dist_excommerce").val();

		$.ajax({
			url: 'shopPlus_commerce_exec.php',
			type: 'POST',
			data: { selected_location_code_ex : location_code, e_type : e_type, date : 'e_regdate', order : '' },
			success: function(data){
				$(".table_excommerce_list .insert_item").remove();
				$(".table_excommerce_list").append(data);
			}
		})
	}

	//광고 업소 - 항목 삭제
	$(document).on('click', '#incommerce_delete', function(){
		if(!confirm('삭제하시겠습니까?'))
			return;
		
		var selected_row = getSelectedRow(); 
		
		$.ajax({
			url: 'shopPlus_commerce_exec.php',
			type: 'POST',
			data: { incommerce_delete_idx : $("#selected_row_idx").val() },
			success: function(){
				if(selected_row != null){
					selected_row.remove();
					$(".background_incommerce").click();
				}
			}
		})
	});
	
	//광고 업소 - 항목 선택시 관리창 토글
	$(document).on('click', '.row_incommerce', function(e){
		var selected_row = $(this);
		$(".row_incommerce").each(function(){
			$(this).css("background-color", "#fff");
		});
		selected_row.css("background-color", "#fdffc3");
		
		$(".background_incommerce").show();
		
		var popup = $(".pop_state_incommerce");
		$("#incommerce_shop_name").text(selected_row.find("td:first").text());
		$("#selected_row_idx").val(selected_row.find("input[name='idx']").val());
		popup.css({"top": e.pageY, "left": e.pageX});
		popup.show();
	});
	$(document).on('click', '.background_incommerce', function(){
		$(this).hide();
		$(".row_incommerce").each(function(){
			$(this).css("background-color", "#fff");
		});
		$("#selected_row_idx").val("");
		$(".pop_state_incommerce").hide();
	});
		
	//외부업체 광고 - 항목 선택시 관리창 토글
	$(document).on('click', '.row_excommerce', function(e){
		var selected_row = $(this);
		$(".row_excommerce").each(function(){
			$(this).css("background-color", "#fff");
		});
		selected_row.css("background-color", "#fdffc3");
		
		$(".background_excommerce").show();
		
		var popup = $(".pop_state_excommerce");
		$("#excommerce_ent_name").text(selected_row.find("td:first").text());
		$("#selected_row_idx").val(selected_row.find("input[name='idx']").val());
		popup.css({"top": e.pageY, "left": e.pageX});
		popup.show();
	});	
	$(document).on('click', '.background_excommerce', function(){
		$(this).hide();
		$(".row_excommerce").each(function(){
			$(this).css("background-color", "#fff");
		});
		$("#selected_row_idx").val("");
		$(".pop_state_excommerce").hide();
	});
		
	//외부업체 광고 - 광고 페이지 이동하기
	$(document).on('click', '#excommerce_confirm', function(){
		var selected_row = getSelectedRow();
		var page_url = selected_row.find("td:nth-child(5)").text();
		window.open(page_url);
		
	});

	//외부업체 광고 - 광고지역별 보기 : 드롭박스 토글
	$(document).on('click', '#btn_view_excommerce_locationally', function(){
		$(".panel_excommerce_location").toggle();
	});

	//외부업체 광고 - 항목 삭제
	$(document).on('click', '#excommerce_delete', function(){
		if(!confirm("삭제하시겠습니까?"))
			return;

		var e_idx = $("#selected_row_idx").val();
		var selected_row = getSelectedRow();
		$.ajax({
			url: 'shopPlus_commerce_exec.php',
			type: 'POST',
			data: {excommerce_delete_idx : e_idx},
			success: function(){
				if(selected_row != null){
					selected_row.remove();
					$(".background_incommerce").click();
				}
			}
		})
	});
	//목록에서 선택된 tr 가져오기
	function getSelectedRow(){
		var idx = $("#selected_row_idx").val(); 
		var selected_row; 
		$(".insert_item").each(function(){
			var tmp_idx = $(this).find("input[name='idx']").val();
			if(tmp_idx == idx)
				selected_row = $(this);
		});
		return selected_row;
	}

	//광고업소 신규등록 - 광고지역 선택 리스트 활성화
	$(document).on('click', '#btn_enable_location_list', function(){
		$(".table_result_search_shop").hide();
		$(".select_commerce_location_shop").show();
	});
	
	$(document).on('click', '#btn_enable_location_list', function(){
		$(".select_commerce_location_enterprise").show();
	});
	
	//광고업소 신규등록 - 시/도 선택시 해당 구/군 목록 나타내기
	$(document).on('change', '#commerce_location_list_city', function(){
		var location_code = $(this).val();
		var list_dist = $("#commerce_location_list_dist");
		
		$.ajax({
			url: 'shopPlus_inCommerce.php',
			type: 'POST',
			data: { selected_city_location_code : location_code },
			success: function(data){
				list_dist.empty();
				list_dist.append(data);
			}
		})
	});
		
	//광고업소 신규등록 - 업소 검색(업소명||코드)
	$(document).on('click', '#btn_search_incommerce_shop', function(e){
		var search_text = $("#text_search_incommerce_shop").val();
		var table = $(".table_result_search_shop");
		$.ajax({
			url: 'shopPlus_commerce_exec.php',
			type: 'POST',
			data: { search_shop_text : search_text },
			success: function(data){
				$(".select_commerce_location_shop").hide();
				$("#commerce_location_list_result").empty();
				table.find("tr:not(:eq(0))").each(function(){
					$(this).remove();
				});
				table.show();
				table.append(data);
			}
		})
	});
	
	//광고업소 신규등록 - 업소 검색 후 선택시 업소명 입력
	$(document).on('click', '.table_result_search_shop .insert_item', function(){
		var shop_name =	$(this).find("td:first").text();
		var shop_url = $(this).find("td:nth-child(2)").text();
		
		$("#text_search_incommerce_shop").val(shop_name);
		$("input[name='h_shop_url']").val(shop_url);
		$(".table_result_search_shop").hide();
		$("#commerce_location_list_result").empty();
		
		$.ajax({
			url: 'shopPlus_commerce_exec.php',
			type: 'POST',
			data: { selected_shop_url : shop_url },
			success: function(data){
				$("#commerce_location_list_result").append(data);	
				
			}
		})
	});
	$(document).on('keyup', '#text_search_incommerce_shop', function(e){
		if(e.keyCode == 13)
			$("#btn_search_incommerce_shop").click();
	});
	
	//외부 업체 광고 - 호스팅 방식 선택시, 사이트 주소/페이지명 입력칸 토글
	$(document).on('change', '.form_excommerce_regist input[name=hosting]:checked', function(){
		var hosting = $(this).val();
		if(hosting == '0'){//내부 호스팅 방식 선택시
			$(".excommerce_hosting_in").show();	
			$(".excommerce_hosting_ex").hide();
		}else if(hosting == '1'){//외부 호스팅 방식 선택시
			$(".excommerce_hosting_ex").show();
			$(".excommerce_hosting_in").hide();	
		}
	})
	$(document).on('change', "input[name='e_main_img']", function(){
		readURL(this, $(".preview_hosting_banner"));
	});

	//외부 업체 광고 - 정보변경창 팝업
	$(document).on('click', '#excommerce_modify', function(){
		var e_idx = $("#selected_row_idx").val();
		var url = "exCommerce_regist.php?idx="+e_idx;
		window.open(url, "외부업체광고 정보변경", "width=600, height=600, scrollbars=yes, resizable=no");
	});

	$(document).on('keyup', '#text_search_excommerce', function(e){
		if( 13 == e.keyCode )
			searchExcommerce();
	});

	$(document).on('click', '#btn_search_excommerce', function(){
		searchExcommerce();
	});

	//외부 업체 광고 - 검색
	function searchExcommerce(){
		var search_type = $("#select_search_excommerce option:selected").val();
		var search_text = $("#text_search_excommerce").val();

		$.ajax({
			url: 'shopPlus_commerce_exec.php',
			type: 'POST',
			data: { search_excommerce_column : search_type, search_excommerce_text : search_text },
			success: function(data){
				$(".table_excommerce_list .insert_item").remove();
				$(".table_excommerce_list").append(data);
			},
			error: function(error){
				alert("에러가 발생하였습니다.");
				console.log(error);
			}
		})
	}

	function readURL(input, img) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();
	
			reader.onload = function (e) {
				img.attr('src', e.target.result);
			}
	
			reader.readAsDataURL(input.files[0]);
		}
	}
});