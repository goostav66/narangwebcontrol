$( document ).ready(function() {
	
	$(".li_1").css("background-color", "#299f6e");
	$(".li_1").css("color", "#fff");

	$("li").click(function(){
		
	var index = $("li").index(this);
	
	//alert(index);

	switch(index){
					case 0 : 
							$("li").css("background-color", "#fff");
							$("li").css("color", "#000");
							$(".li_1").css("background-color", "#299f6e");
							$(".li_1").css("color", "#fff");
							
							$("#searchBar_date").css("display","none");
							$("#searchBar_hpno").css("display","none");
							
							break;
					case 1 : 
							$("li").css("background-color", "#fff");
							$("li").css("color", "#000");
							$(".li_2").css("background-color", "#299f6e");
							$(".li_2").css("color", "#fff");
							
							$("#searchBar_hpno").css("display","block");
							$("#searchBar_date").css("display","none");

							break;
					case 2 : 
							$("li").css("background-color", "#fff");
							$("li").css("color", "#000");
							$(".li_3").css("background-color", "#299f6e");
							$(".li_3").css("color", "#fff");
							
							$("#searchBar_date").css("display","block");
							$("#searchBar_hpno").css("display","none");

							break;
					}		
	});

$(document).on('click', '.li_1', function () {
	$.ajax({
		url:'search.php',
		type:'post',
		success:function(data){
			var obj =  $.parseJSON( data );
			obj_all_size = obj.length;

			//alert("SUCCESS");
			//console.log(obj_all_size);
			
			$(".list_item").remove();
			$(".list_s_item").remove();

			for (var i in obj) 
			{
				$("#list_data").append(
					"<tr class='list_s_item'>"
						+"<td><input type='checkbox' name='idx[]' value="+obj[i].idx+"></td>"
						+"<td>"+obj[i].idx+"</td>"
						+"<td>"+obj[i].hpno+"</td>"
						+"<td>"+obj[i].credate+"</td>"
						+"<td>"+obj[i].token.substring( 0,30 )+" . . .</td>"
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

$(document).on('click', '#btn_hpno', function () {
	
	var hp = $("#search_hpno").val();

	//console.log(hp);

	$.ajax({
		url:'search.php',
		type:'post',
		data:{hpno : hp},
		success:function(data){
			var obj =  $.parseJSON( data );

			$("#s_search").text("검색결과 : "+obj.length+" / ");
			$(".list_item").remove();
			$(".list_s_item").remove();
		
			for (var i in obj) 
			{
				$("#list_data").append(
					"<tr class='list_s_item'>"
						+"<td><input type='checkbox' name='idx[]' value="+obj[i].idx+"></td>"
						+"<td>"+obj[i].idx+"</td>"
						+"<td>"+obj[i].hpno+"</td>"
						+"<td>"+obj[i].credate+"</td>"
						+"<td>"+obj[i].token.substring( 0,30 )+" . . .</td>"
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

$(document).on('click', '#btn_date', function () {
	
	var credate = $("#search_date").val();

	//console.log(credate);

	$.ajax({
		url:'search.php',
		type:'post',
		data:{cdate : credate},
		success:function(data){
			var obj =  $.parseJSON( data );

			//alert("SUCCESS");
			//console.log(data);
			
			$("#s_search").text("검색결과 : "+obj.length+" / ");
			$(".list_item").remove();
			$(".list_s_item").remove();

			for (var i in obj) 
			{
				$("#list_data").append(
					"<tr class='list_s_item'>"
						+"<td><input type='checkbox' name='idx[]' value="+obj[i].idx+"></td>"
						+"<td>"+obj[i].idx+"</td>"
						+"<td>"+obj[i].hpno+"</td>"
						+"<td>"+obj[i].credate+"</td>"
						+"<td>"+obj[i].token.substring( 0,30 )+" . . .</td>"
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

var isAllCheck = 0;

$(document).on('click', '#btn_all', function () {

	
	if(isAllCheck == 0){
		$('input:checkbox[name="idx[]"]').each(function() {
			isAllCheck = 1;
			this.checked = true; //checked 처리
			if(this.checked){//checked 처리된 항목의 값
				//this.checked = false; 
			}
		});
	}else {
		$('input:checkbox[name="idx[]"]').each(function() {
			isAllCheck = 0;
			this.checked = false; 
		});
	}

});
$(document).on('click', '#submitButton', function () {

	var checked = $('input[name="idx[]"]:checked').length;
	var msg_len = $("#fcm_message").val().length;

	if(checked == 0){
		alert("전송 상대를 1명 이상 선택하세요.");
		return false;
	}else if (checked >= 1 && msg_len == 0){
		alert("메세지를 입력하세요.");
		return false;
	}
	else if (checked == 0 && msg_len == 0){
		alert("메세지를 입력하세요.");
		return false;
	}
	else if (checked >= 1 && msg_len > 140){
		alert("sms 최대 길이를 초과하였습니다.");
		return false;
	}
	else if (checked >= 1 && msg_len <= 140){
		alert(checked+"명에게 메세지를 보냅니다.");

		var params = jQuery("#fcm_form").serialize();
		$.ajax({
			url:'guest_exec.php',
			type:'post',
			data:params,
			contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
			dataType: 'html',
			success:function(data){
				if(data){
					alert(data);
				}
			}
		})
		$("input:checkbox[name='idx[]']").prop("checked",false);
		
	}


});

});

