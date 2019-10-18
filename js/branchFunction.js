$( document ).ready(function() {

	//---------------------------------------------------------- 지사 등록
	$(document).on('click', '#btn_regist_branch', function (e) {
		e.preventDefault();

		var url_len = $("input[name='new_branch_code']").val().length;
		var branch_name_len = $("input[name='branch_name']").val().length;
		var ceo_name_len = $("input[name='branch_ceo_name']").val().length;
		var ceo_phone_len= $("input[name='branch_ceo_phone']").val().length;
		var call_center_len = $("input[name='call_center']").val().length;
		
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

		if(exitEvent){
			return false; // event finish
		}else{
			if(url_len == 0){
				alert("지사코드를 반드시 입력하세요.");
				return false;
			}else if(branch_name_len == 0){
				alert("지사를 반드시 입력하세요.");
				return false;
			}else if(ceo_name_len == 0){
				alert("대표자를 반드시 입력하세요.");
				return false;
			}else if(ceo_phone_len == 0){
				alert("대표자 연락처를 반드시 입력하세요.");
				return false;
			}else if(call_center_len == 0){
				alert("콜센터 번호를 반드시 입력하세요.");
				return false;
			}
		
			var result = confirm("등록하시겠습니까?");

			if(result) {
			   var params = jQuery("#form_rgst_branch").serialize();
				$.ajax({
					url:'branch_exec.php',
					type:'POST',
					data:params,
					success:function(data){
					
						if(data.toLowerCase() == 'duplicate'){
							alert('이미 등록된 코드입니다.');
						}else if(data != 'error'){
							var num = $("#insert_data tr").length;
							$("#insert_data tr:first").after(data);
							$("#insert_data .list_item:first").find("td:first").text(num);
						}
					}
								
				})			
			}
		}	
	});
	
	//---------------------------------------------------------- 지사 수정 팝업 띄우기
	$(document).on('click', '.list_click', function () {
		var idx = $(this).find("input[name='branch_idx']").val();
		var url = "branchEdit.php?idx="+idx;
		
		window.open(url, "지사 정보 수정", "width=900, scrollbars=yes, resizable=no");
	});
	
	//---------------------------------------------------------- 지사 팝업 닫기
	$(document).on('click', '#closeButton', function () {
		window.close();
	});
	
	//---------------------------------------------------------- 지사 수정
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
				var params = jQuery("#branch_edit_form").serialize();
	
				$.ajax({
					url:'branch_exec.php',
					type:'post',
					data: params,
					success:function(data){
						if(data == "success"){
							alert("수정을 완료하였습니다.");

							window.opener.location.reload();
						
							
							
						}else{
							alert(data);
						}
					}
				})	

			} else {
				return false;
			}
		}
		
	});
	
	//---------------------------------------------------------- 지사 삭제
	$(document).on('click', '#removeButton', function () {
		
		var result = confirm("삭제하시겠습니까?");
			if(result) {
				var idx = $("input[name='edit_branch_idx']").val();
				$.ajax({
					url:'branch_exec.php',
					type:'post',
					data:{ del_branch_idx : idx},
					success:function(data){
						if(data == "success"){
							alert("삭제를 완료하였습니다.");
							setTimeout(function(){window.self.close();},1000);
							setTimeout(function(){window.opener.parent.location.reload();},1000);
						}else{
							alert(data);
						}
					}
				})		
			} 	
	});
	
	//---------------------------------------------------------- 관할 가맹점 이동
	$(".pop_dist_shop").click(function(){
			var url = 'shopEdit.php?idx='+$(this).attr('id');
			window.open(url, '관할 가맹점 수정', 'width=900, height=1000, scrollbars=yes, resizable=no');
	});
});