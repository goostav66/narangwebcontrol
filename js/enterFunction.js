$( document ).ready(function() {

	$(document).on('click', '#btn_logout', function () {
	
		var result = confirm("로그아웃 하시겠습니까?");

			if(result) {
				
				$.ajax({
					url:'/m/user/enter_exec.php',
					type:'post',
					data:{ logout : 'logout' },
					contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
					dataType: 'html',
					success:function(data){
						
						location.replace(data);
					},
					error: function (request, status, error)
					{
						alert("error! please check console");
						console.log('code: '+request.status+"\n"+'message: '+request.responseText+"\n"+'error: '+error);
					}
				})
				
			} else {
				return false;
			}
		
	});

});