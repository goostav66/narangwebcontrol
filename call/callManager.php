<?php session_start(); ?>
<?php 
header("Content-Type: text/html;charset=UTF-8");
include_once  $_SERVER['DOCUMENT_ROOT'].'/m/nav.php';
include_once  $_SERVER['DOCUMENT_ROOT'].'/m/call/call_exec.php';
?>

<?php
	$auth = $_SESSION['auth'];
	$id = $_SESSION['id'];

	// 페이지네이션
	$num_rec_per_page = 20;
	if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=1; }; 
	$start_from = ($page-1) * $num_rec_per_page; 

	// $num_rec_per_page로 계산한 만큼 리스트 가져오기
	$page_result = _ViewList($connect, $start_from, $num_rec_per_page, $auth, $id); 
	
	// 총 리스트 갯수
	$total_records = _ViewListCount($connect, $auth, $id);
	$total_pages = ceil($total_records / $num_rec_per_page); 
	
	//---------------------호출등록을 위한 지사정보 가져오기
	if($auth == 'manager' || $auth == 'center'){
		$branch = getBranch($connect, $auth, $id);
		$isAgent = 'n';
	}

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

	var cancle = 1;
	var o_call_idx = "";
	
	if (auth == "manager" || auth == "center"){
	
		$.ajax ({
			url : 'call_exec.php',
			type:'post',
			data:{ call_O_check : 1},
			contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
			dataType: 'html',
			cache : false,
			success : function (data) {
				o_call_idx = data;
				//console.log("waiting-call original idx: "+data);
			}
		});
	
		$(function() {
			timer = setInterval( function () {
				console.log("call: "+call);
				console.log("list_num: "+list_num);
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
							//console.log("O");
							
							$.ajax ({
								url : 'call_exec.php',
								type:'post',
								data:{ call_cancle_idx : o_call_idx},
								contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
								dataType: 'html',
								cache : false,
								success : function (data) {
									//console.log(data);
									if(data == "y"){
										//cancle++;
										alert("고객이 취소한 콜이 있습니다.");
										window.location.reload();
									}else if (data == "n"){
										//console.log("no");
										return false;
									}				
								}
							});
						}
						
						setTimeout(function(){
										if(call >= 2) window.location.reload();										
									}, 2000 );
		
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
		if($auth == "manager" || $auth == "center"){
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
				<option value='C'>취소-고객취소</option>
				<option value='D'>취소-전화두절</option>
				<option value='E'>취소-기사부족</option>
				<option value='F'>취소-테스트콜</option>
			</select>
		</td>
		<td>
			<select id='branch' name='branch'>
				<option value='0'>지사 선택</option>";
				while ($br = mysqli_fetch_assoc($branch)) {
					echo "<option value='".$br['url']."'>".$br["branch_name"]."</option>";
				}
	echo	"</select>
		</td>
		<td>
			<select id='agent' name='agent'>
				<option value='0'>대리점 선택</option>
			</select>
		</td>
		<td> <select id='shop' name='shop'>
				<option value='0'>가맹점 선택</option>
			</select></td>
	</tr>
</table>
<div style='margin:0 auto; padding:10px; margin-top:-50px; min-width:500px; max-width:90%; text-align:right'>
	<input type='submit' name='btn_rgst' value='등록' class='edit_btn' id='rgstButton'>
</div>
</form>";
}
?>

<div class="list_line"></div>

<div id="searchBar_hpno">
	<select id="search" name="search">
		<option value="shop_name">가맹점상호</option>
		<option value="hpno">고객 H.P</option>
		<option value="current_position">출발지</option>
		<option value="dst_position">목적지</option>
		<option value="branch_name">지사</option>
		<option value="agent_name">대리점</option>

	</select>
	<input type="text" name="search_text" id="search_text">
	<input type="hidden" name="search_id" id="search_id" value="<?=$id?>">
	<input type="hidden" name="auth" id="auth" value="<?=$auth?>">
	<input type="button" name="btn_search" value="검색" id="btn_search">
	<br/>
</div>

<div id="list_state">최신순 정렬 / <?php echo "전체 콜 수 : ".$total_records; ?></div>
<table class="list_data insert_data">
	<colgroup>
		<col style="width:3%;">
		<col style="width:7%;">
		
		<col style="width:10%;">
		<? // 2017.09.14 ?>

		<col style="width:11%;">
		<col style="width:15%;">
		<col style="width:22%;">
		<col style="width:3%;">
		<col style="width:3%;">
		<col style="width:5%;">
		<col style="width:5%;">
		<col style="width:5%;">
		<col style="width:3%;">
		<col style="width:3%;">
		<col style="width:10%;">
	</colgroup>
	<tr>
		<th>No.</th>
		<th>가맹점상호</th>
		
		<th>고객 H.P</th>
		<? // 2017.09.14 ?>

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
	}else if ($state_param == "B") { // 배정 중
		$class = "connect";
		$state_num = 2;
		$state_text = "배정 중";
	}else if ($state_param == "C") { // 고객취소
		$class = "fail";
		$state_num = 3;
		$state_text = "취소-고객취소";
	}else if ($state_param == "D") { // 전화두절
		$class = "fail";
		$state_num = 4;
		$state_text = "취소-전화두절";
	}else if ($state_param == "E") { // 기사부족
		$class = "fail";
		$state_num = 5;
		$state_text = "취소-기사부족";
	}else if ($state_param == "F") { // 테스트
		$class = "fail";
		$state_num = 6;
		$state_text = "취소-테스트콜";
	}
	else if ($state_param == "O") { //접수 대기
		$class = "";
		$state_num = 0;
		$state_text = "접수대기";
	}
	
	$state = array("", "", "", "", "", "", "");
	$state[$state_num] = "selected";
	
	$hpno1 =  substr($row["hpno"], 0, 3);
	$hpno2 =  substr($row["hpno"], 3, 4);
	$hpno3 =  substr($row["hpno"], 7, 4);

	$hpno = $hpno1."-".$hpno2."-".$hpno3;
	
	if( isset($row["current_position"]) && !empty($row["current_position"]) ){
		$position = $row["current_position"];
	}
	else if($row["current_position"] == null or $row["current_position"] == ""){
		$position = $row["shop_addr"];
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
	<? // 2017.09.14 ?>

	<td><?echo date("Y-m-d H:i",strtotime($row["credate"]));?></td>
	<td>
		<?php // 출발지
			if($auth == "manager" or $auth == 'center'){
				echo "<textarea rows='2' cols='30' name='input_curr_position' class='input_curr_position'>"
					.$position." </textarea>"
					."<input type='button' name='btn_curr_position' class='btn_curr_position' value='등록' />";
			}else {echo $position;};
		?>
	</td>
	
	<td class="call_pos"> 
		<?php // 목적지
			if($auth == "manager" or $auth == 'center'){
				
				$flag = "*";
				$position = explode($flag, $row["dst_position"]);
					
				$add_call = $row["add_call"]+1;
				
				for($i = 0; $i < $add_call; $i++){
					if($add_call > 1 ){
						echo  "<span style='display:block; float:left'>주소".($i+1)." : </span>";
					}
					echo  " <textarea rows='2' cols='30' name='input_position' class='input_position' style='display:block;' >"
						.$position[$i]." </textarea><br/>";
				}
				echo "<input type='button' name='btn_position' class='btn_position' value='등록'/>";
			}else {echo $row["dst_position"];}
				
		?>
	</td>
	<td>
		<?php // 경유지
			if($auth == "manager" or $auth == 'center'){
				echo "<input type='text' name='input_mid_pass' class='input_mid_pass' size='2' value='".$row["mid_pass"]."'/>"
					."<input type='button' name='btn_mid_pass' class='btn_mid_pass' value='등록' />";
			}else {echo $row["mid_pass"];}
			
		?>
	</td>
	<td>
		<?php // 추가콜
			if($auth == "manager" or $auth == 'center'){
				echo "<input type='text' name='input_add_call class='input_add_call' size='2' value='".$row["add_call"]."'/>"
					."<input type='button' name='btn_add_call' class='btn_add_call' value='등록' />";
			}else {echo $row["add_call"];}
		?>
	
	</td>
	<td  class="call_price"> 
		&nbsp
		<?php // 요금
			if($auth == "manager" or $auth == 'center'){
				$flag = "*";
				$price = explode($flag, $row["price"]);

				for($i = 0; $i < $add_call; $i++){
					if($add_call > 1 ){
						echo  "<span style='display:block; float:left'>주소".($i+1)." 요금 : </span>";
					}
					echo "<input type='text' name='input_price' class='input_price' size='7' value='".$price[$i]."'/>";
				}
					echo "<input type='button' name='btn_price' class='btn_price' value='등록' />";
			}else {echo $row["price"];};
		?>
	</td>
	
	<td> <?//=$row["rule_price"] ?></td>
	<td> 

		<?php // 상태
		if($auth == "manager" or $auth == 'center'){
		echo "
		<select class='state select_state' name='state' >
			<option value='O' ".$state[0].">접수 대기</option>
			<option value='S' ".$state[1].">완료</option>
			<option value='B' ".$state[2].">배정 중</option>
			<option value='C' ".$state[3].">취소-고객취소</option>
			<option value='D' ".$state[4].">취소-전화두절</option>
			<option value='E' ".$state[5].">취소-기사부족</option>
			<option value='F' ".$state[6].">취소-테스트콜</option>
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
<a  href='callManager.php?page=1' class='page off'>◀</a>
<?php
for ($i=1; $i<=$total_pages; $i++) { 
	
	if($page == $i) $class = "on";
	else			$class = "off";
	
	if( ($i % 20) == 0 ) $br = "<br/>";
	else				 $br = "";

	?>
	<a href='callManager.php?page=<?=$i?>' class='<?=$class?>'><?=$i?></a> <?=$br?>
<?php } ?>
<a  href='callManager.php?page=<?=$total_pages?>' class='page off' >▶</a>
</div>

</body>
</html>
