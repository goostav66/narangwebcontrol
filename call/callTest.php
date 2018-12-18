<?php session_start(); ?>
<?php 
header("Content-Type: text/html;charset=UTF-8");
include_once  $_SERVER[DOCUMENT_ROOT].'/m/nav.php';
include_once  $_SERVER[DOCUMENT_ROOT].'/m/call/call_exec.php';
?>

<?php
	$auth = $_SESSION['auth'];
	$id = $_SESSION['id'];

	// 페이지네이션

	$num_rec_per_page=20;
	if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=1; }; 
	$start_from = ($page-1) * $num_rec_per_page; 

	
	$page_result = _ViewList($connect, $start_from, $num_rec_per_page, $auth, $id); 
	

	$total_records = _ViewListCount($connect, $auth, $id);
	$total_pages = ceil($total_records / $num_rec_per_page); 

?>

<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="<?=$path?>/js/callFunction.js"></script>
<script>
$( document ).ready(function() {
	
	var call = 1;
	var list_num = '<?php echo $total_records;?>';
	
	var auth = '<?=$auth?>';
	//alert(list_num);
	
	if (auth == "manager" || auth == "center"){
	
		$(function() {
			timer = setInterval( function () {
				console.log("call");
				//----------------------------------------------------------------------------------
				$.ajax ({
					url : 'call_exec.php',
					type:'post',
					data:{ call_refresh : call, old_list_num : list_num},
					contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
					dataType: 'html',
					cache : false,
					success : function (data) {
						
						if(data){
							
							//오디오 재생 코드
							var audio = new Audio('notification-sound.mp3');
							audio.play();

							call++;
							
						}else{
							return false;
						}
						
						setTimeout(function(){
										if(call >= 2) window.location.reload();
									}, 2000 )
		
					}
				});
				//----------------------------------------------------------------------------------
			}, 3000); // 3초에 한번씩 받아온다.

			function reload(){
				if(call >= 2) window.location.reload();
			}
		});
	}else {}

});
</script>

<h2 class="title_sub"> 호출관리 </h2>
<?php
		if($auth == "manager"){
			echo "
<form method='post' id='call_rgst_form' name='call_rgst_form'>
<input type='hidden' class='call_rgst' value='1' name='call_rgst'>			
<table class='list_data'>
	<colgroup>
		
		<col style='width:10%;'>
		<col style='width:10%;'>
		<col style='width:11%;'>
		<col style='width:15%;'>
		<col style='width:15%;'>
		<col style='width:3%;'>
		<col style='width:3%;'>
		<col style='width:5%;'>
		<col style='width:5%;'>
		<col style='width:5%;'>
		<col style='width:5%;'>
		<col style='width:5%;'>
		<col style='width:13%;'>
	</colgroup>
	<tr>
		
		<th>TEL</th>
		<th>고객 H.P</th>
		<th>호출시간</th>
		<th>출발지</th>
		<th>목적지</th>
		<th>경유지</th>
		<th>추가콜</th>
		<th>요금</th>
		<th>규정 요금</th>
		<th>상태</th>
		<th>지사</th>
		<th >대리점</th>
		<th class='last'>가맹점상호</th>
	</tr>
	<tr class='list_item'>
		
		
		<td> <input type='text' name='shop_tel' id='shop_tel' size='10' /></td>
		<td> <input type='text' name='hpno' id='hpno' size='10' /></td>
		<td> <input type='text' name='credate' id='credate' size='11' /></td>
		<td> <input type='text' name='current_position' id='current_position' size='15' /></td>
		<td> <input type='text' name='dst_position' id='dst_position' size='15' /></td>
		<td> <input type='text' name='mid_pass' id='mid_pass' size='3' /></td>
		<td> <input type='text' name='add_call' id='add_call' size='3' /></td>
		<td> <input type='text' name='price' id='price' size='5' /></td>
		<td> <input type='text' name='rule_price' id='rule_price' size='5' /></td>
		<td>
			<select class='state' name='state'>
				<option value='O'>접수 대기</option>
				<option value='B'>배정 중</option>
				<option value='S'>완료</option>
				<option value='C'>취소</option>
			</select>
		</td>
		<td>
			<select id='branch' name='branch'>
				<option value='0'>지사 선택</option>
				<option value='1'>1</option>
			</select>
		</td>
		<td>
			<select id='agent' name='agent'>
				<option value='0'>대리점 선택</option>
				<option value='1'>1</option>
			</select>
		</td>
		<td> <select id='shop_name' name='shop_name'>
				<option value='0'>가맹점 선택</option>
				<option value='1'>1</option>
			</select></td>
	</tr>
</table>
<div style='margin:0 auto; padding:10px; margin-top:-50px; min-width:500px; max-width:90%; text-align:right'>
	*가맹점 선택 준비 중* <input type='submit' name='btn_rgst' value='등록' class='edit_btn' id='rgstButton'>
</div>
</form>";
}
?>

<div class="list_line"></div>

<div id="searchBar_hpno">
* 호출 검색 준비 중 입니다.
	<select id="search" name="search">
		<option value="shop_name">가맹점명</option>
		<option value="shop_tel">가맹점 TEL</option>
		<option value="hpno">고객 H.P</option>
		<option value="current_position">출발지</option>
		<option value="dst_position">출발지</option>
		<option value="state">상태</option>
	</select>
	<input type="text" name="search_text" id="search_text">
	<input type="hidden" name="search_id" id="search_id" value="<?=$id?>">
	<input type="button" name="btn_search" value="검색" id="btn_search">
	<br/>
</div>

<div id="list_state">최신순 정렬 / <?php echo "전체 콜 수 : ".$total_records; ?></div>
<table class="list_data">
	<colgroup>
		<col style="width:3%;">
		<col style="width:10%;">
		
		<col style="width:10%;">
		<col style="width:11%;">
		<col style="width:15%;">
		<col style="width:15%;">
		<col style="width:3%;">
		<col style="width:3%;">
		<col style="width:5%;">
		<col style="width:5%;">
		<col style="width:5%;">
		<col style="width:5%;">
		<col style="width:5%;">
		<col style="width:10%;">
	</colgroup>
	<tr>
		<th>No.</th>
		<th>가맹점상호</th>
		
		<th>고객 H.P</th>
		<th>호출시간</th>
		<th>출발지</th>
		<th>목적지</th>
		<th>경유지</th>
		<th>추가콜</th>
		<th>요금</th>
		<th>규정 요금</th>
		<th>상태</th>
		<th>지사</th>
		<th>대리점</th>
		<th class="last">TEL</th>
	</tr>

<?php
// No. 
if ($page == 1) $num = $total_records + 1;
else			$num = ($total_records + 1) - (($page-1) * 20);

while ($row = mysqli_fetch_assoc($page_result)) {
	$num--;
	
	// 상태
	$state_param = $row['state'];

	if ($state_param == "S") { // 완료
		$class = "success";
		$state_num = 1;
		$state_text = "완료";
	}else if ($state_param == "C") { // 최소
		$class = "fail";
		$state_num = 2;
		$state_text = "취소";
	}
	else if ($state_param == "O") { //접수 대기
		$class = "";
		$state_num = 0;
		$state_text = "접수대기";
	}
	
	$state = array("", "", "");
	$state[$state_num] = "selected";
	
	$hpno1 =  substr($row["hpno"], 0, 3);
	$hpno2 =  substr($row["hpno"], 3, 4);
	$hpno3 =  substr($row["hpno"], 7, 4);

	$hpno = $hpno1."-".$hpno2."-".$hpno3;
	
	if( isset($row["current_position"]) && !empty($row["current_position"]) ){
		$position = $row["current_position"];
	}
	else if($row["current_position"] == null or $row["current_position"] == ""){
		$position = $row["position"];
	}
	
	//어플과 모바일웹 구분하는 아이콘
	$icon = "";
	if ( isset($row['url']) && !empty($row["url"]) ){
		$icon = "icon_call_02.png";
	}else {$icon = "icon_call_01.png";}

		

?>
<tr class="list_item <?=$class?>">
	<input type="hidden" class="idx" value="<?=$row['idx']?>">
	<td> <?=$num ?><img src="../images/<?=$icon?>" width="50px"/></td>
	<td> <?=$row["shop_name"] ?> </td>
	
	<td style="font-weight:bolder"> <?= $hpno ?></td>
	<td> <?=$row["credate"] ?></td>
	<td>
		<?php 
			if($auth == "manager" or $auth == 'center'){
				echo "<textarea rows='2' cols='30' name='input_curr_position' class='input_curr_position'> $position </textarea>"
					."<input type='button' name='btn_curr_position' class='btn_curr_position' value='등록' />";
			}else {echo $position;};
		?>
	</td>
	
	<td> 
		<?php 
			if($auth == "manager" or $auth == 'center'){
				echo "<input type='text' name='input_position' class='input_position' size='30' value='".$row["dst_position"]."' /> "
					."<input type='button' name='btn_position' class='btn_position' value='등록' />";
			}else {echo $row["dst_position"];};
		?>
	</td>
	<td>
		<?php 
			if($auth == "manager" or $auth == 'center'){
				echo "<input type='text' name='input_mid_pass' class='input_mid_pass' size='2' value='".$row["mid_pass"]."'/>"
					."<input type='button' name='btn_mid_pass' class='btn_mid_pass' value='등록' />";
			}else {echo $row["mid_pass"];}
			
		?>
	</td>
	<td>
		<?php 
			if($auth == "manager" or $auth == 'center'){
				echo "<input type='text' name='input_add_call class='input_add_call' size='2' value='".$row["add_call"]."'/>"
					."<input type='button' name='btn_add_call' class='btn_add_call' value='등록' />";
			}else {echo $row["add_call"];}
			
		?>
	
	</td>
	<td> 
		<?php 
			if($auth == "manager" or $auth == 'center'){
				echo "<input type='text' name='input_price' class='input_price' size='7' value='".$row["price"]."'/>"
					."<input type='button' name='btn_price' class='btn_price' value='등록' />";
			}else {echo $row["price"];};
		?>
	</td>
	
	<td> <?=$row["rule_price"] ?></td>
	<td> 

		<?php
		if($auth == "manager" or $auth == 'center'){
		echo "
		<select class='state select_state' name='state' >
			<option value='O' ".$state[0].">접수 대기</option>
			<option value='S' ".$state[1].">완료</option>
			<option value='C' ".$state[2].">취소</option>
		</select>
		";
		}else if ($auth == "branch" || $auth == "agent"){
			echo $state_text;
		} ?>

		<input type="hidden" class="state" value="<?=$row['state']?>">
	</td>
	
	<td> <?=$row["branch_name"] ?></td>
	<td > <?=$row["agent_name"] ?></td>
	<td class="last"> <?=$row["shop_tel"] ?> </td>
	
</tr>

<?php
}
/* free result set */
mysqli_free_result($page_result);
?>

</table>

<div class="page">
<a  href='callManager.php?page=1'> ◀처음으로 </a>
<?php
for ($i=1; $i<=$total_pages; $i++) { 
	
	if($page == $i) $class = "on";
	else			$class = "off";
	

	if( ($i % 20) == 0 ) $br = "<br/>";
	else				 $br = "";

	?>
	<a href='callManager.php?page=<?=$i?>' class='<?=$class?>'><?=$i?></a> <?=$br?>
<?php } ?>
<a  href='callManager.php?page=<?=$total_pages?>' class='page' > 마지막으로▶ </a>
</div>

</body>
</html>
