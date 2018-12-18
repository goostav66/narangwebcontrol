<?php 
if(session_id() == '') {
	session_start();
}
?>
<?php 
header("Content-Type: text/html;charset=UTF-8");
include_once $_SERVER['DOCUMENT_ROOT'].'/m/header.php';

if($_SESSION['auth'] == ""){
	echo "<script>
			//alert('로그인해주세요!');
			location.replace('".$path."/login_index.php');
		</script>";
}else{
	$auth = $_SESSION['auth'];
	$id = $_SESSION['id'];
	if($auth != 'manager'){
		$settings = "<input type='button' class='edit_btn' id='btn_settings' value='정보수정' style='padding:2px; font-size:9pt'>";
	}else{ $settings = ""; }
}
?>
<style>
.drop_item	{ top:9px !important; left:160px !important; border-radius: 3px 3px 3px 3px !important;}
.drop_item li {display:none;}
</style>
<script>
$( document ).ready(function() {
	$('.drop_item').css("display", "none");

	$('.droplist').hover(
		function(){
			$('.drop_item').css("display", "block");
			$('.drop_item li').css("display", "block");
		},
		function(){
			$('.drop_item').css("display", "none");
			$('.drop_item li').css("display", "none");
		}
	);
	
	$('#btn_settings').click(function(){
		var id = '<?=$id?>';
		
		if('branch'=='<?=$auth?>')
			window.open("code/branchEdit.php?id="+id,"정보 수정", "width=900, height=1000, scrollbars=yes, resizable=no");
		
	});
});
</script>
<!--<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>-->
<!--<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>-->
<script src="<?=$path?>/js/enterFunction.js"></script>


<body>
  <header style="color:#fff">
	<div id="logo"><img src="<?=$path?>/images/logo.png" alt="logo" /></div>
	
    <nav>
      <ul class="nav">
        <li><a href="<?=$path?>/index.php" class="icon home active"><span>Home</span></a></li>
		
		<?php
			if($auth == "manager" or $auth == "branch" or $auth == "agent"){
				echo "
				 <li class='dropdown'>
				 <a href='#'>코드관리</a>
				  <ul>";
			  
				if($auth == "manager"){
					echo "
					<li><a href='".$path."/code/user.php'>회원</a></li>
					<li><a href='".$path."/code/branchRegister.php'>지사</a></li>
					<li><a href='#'>대리점</a></li>
					<li><a href='".$path."/code/tagID.php'>태그 ID</a></li>";
				}
				if($auth == "manager" or $auth == "branch" or $auth == "agent"){
					echo "
					<li><a href='".$path."/code/shopRegister.php'>가맹점</a></li>
					";
				}
				echo "</ul></li>";
			} ?>

		<?php
		if($auth == "manager"){
		echo "
        <li class='dropdown'>
          <a href='".$path."/guest/guestList.php'>고객관리</a>
          <ul class='large'>
            <li><a href='".$path."/guest/guestList.php'>고객 리스트</a></li>
            <li><a href='".$path."/guest/msgManager.php'>푸쉬 발송</a></li>
            <li><a href='#'>발송 내역</a></li>
          </ul>
        </li>
		";
		} ?>
        <!-- <li class=""><a href="<?=$path?>/call/callManager.php">호출관리</a></li> -->
        <?php
			if($auth == "manager" or $auth == "branch" or $auth == "agent"){
				echo "<li><a href='".$path."/stats/stats.php'>통계현황</a></li>";
			}
			if($auth == "manager"){
				echo "<li class='dropdown'>";
				echo 	"<a href='".$path."/call/call_history.php'>호출기록</a>";
				echo "</li>";
				echo "<li class='dropdown'>";
				echo 	"<a href='#'>콘텐츠</a>";
				echo 	"<ul>";
				echo 		"<li class='droplist'>";
				echo 			"<a href='".$path."/code/shopPlus_stay.php'>Na랑 zzZ</a>";
				echo 			"<ul class='drop_item'>";
				echo 		"<li>";
				echo			"<a href='".$path."/code/shopPlus_stay.php'>관리</a>";
				echo 		"</li>";
				echo 		"<li><a href='#'>배달</a></li>";
				echo 		"<li><a href='#'>안마</a></li>";
				echo 	"</ul>";
				echo "</li>";
				echo "<li class='droplist'>";
				echo 	"<a href='#'>이벤트</a>";
				echo 	"<ul class='drop_item'>";
				echo 	"</ul>";
				echo "</li>";
				echo "<li class='droplist'>";
				echo	"<a href='".$path."/code/shopPlus_reserve.php'>가맹점 예약관리</a>";
				echo "</li>";
				echo "<li class='droplist'>";
				echo 	"<a href='".$path."/code/shopPlus_inCommerce.php'>광고업소 관리</a>";
				echo "</li>";
				echo "<li class='droplist'>";
				echo "<a href='".$path."/code/shopPlus_reserve.php'>외부업체 광고</a>";
				echo "</li>";
				echo "</ul></li>";
				
				echo "<li class='dropdown'>
				<a href='".$path."/test_sigun.php'>시/군(테스트)</a></li>";

				
			}
			
		
		?>
        
      </ul>
	 <h1 class="top_title">
		 <?php echo $_SESSION['name'];?>님 환영합니다.
         <?=$settings ?>
		 <input type="button" name="btn_logout" class="edit_btn" id="btn_logout" value="로그아웃" style="padding:2px; font-size:9pt" />
		 <a href="http://www.nfczone.co.kr/" target="_blank"><input type="button" name="btn_logout" class="edit_btn" value="홈페이지 바로가기" style="padding:2px; font-size:9pt" /></a>
	 </h1>
    </nav>
  </header>
