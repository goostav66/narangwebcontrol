<!DOCTYPE html>
<html >
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Na랑 대리운전 관리자 페이지</title>
      <link rel="stylesheet" href="css/login-style.css?ver=1">
	  <link rel="icon" href="/m/favicon.ico" type="image/x-icon" />
	<!--	  <script type="text/javascript" src="http://code.jquery.com/jquery-1.7.1.min.js"></script> -->
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	  <script>

		$(document).ready(function() {
			$("#login").click(function() {
				console.log("login");
				var action = $("#login-form").attr('action');

				 if ($("#user_id").val().length == 0) {
					alert('아이디를 입력해 주세요.');
					$("#user_id").focus();
					return;
				 }
				 if ($("#user_id").val().length < 4 || $("#user_id").val().length > 12) {
					alert('사용자 아이디는 4~12자여야 합니다.');
					$("#user_id").focus();
					return;
				 }
				 if ($("#user_pw").val().length == 0) {
					alert('패스워드를 입력해 주세요.');
					$("#user_pw").focus();
					return;
				 }
				 if ($("#user_pw").val().length < 4 || $("#user_pw").val().length > 12) {
					alert('패스워드는 4~12자여야 합니다.');
					$("#user_pw").focus();
					return;
				 }

					var user_id = $("#user_id").val();
					var user_pw = $("#user_pw").val();
					
				$.ajax({
					type: "POST",
					url: action,
					data: "id="+user_id+"&pw="+user_pw,
					success: function(response) {
						console.log(response);
						var result = response.split(",");
						var name = result[1]; // 콤마 뒤의 이름값

						if(result[0] == 'success') {
							alert(result[1]+"님 환영합니다 !");
							location.href="index.php";
						}
						else {
							alert("아이디 또는 비밀번호가 잘못되었습니다.");	
						}
					}
				});
				return false;
			});
		});

   </script>
</head>

<body>
  <form name="login-form" id="login-form" class="login-form" action="/m/user/enter_exec.php" method="post">
	
		<div class="header">
		<h1>환영합니다.</h1>
		<span>아이디와 비밀번호를 입력해주세요.</span>
		</div>
	
		<div class="content">
		<input name="username" type="text" class="input username" placeholder="Username" id="user_id"/>
		<div class="user-icon"></div>
		<input name="password" type="password" class="input password" placeholder="Password" id="user_pw" 
		onKeyDown='if(event.keyCode==13) $("#login").click();'/>
		<div class="pass-icon"></div>		
		</div>

		<div class="footer">
		<a  class="button" id="login">Login</a>
		</div>
	
	</form>
  
  
</body>
</html>
