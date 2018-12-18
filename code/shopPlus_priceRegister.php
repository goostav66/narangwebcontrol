<?php session_start(); ?>
<?php 
header("Content-Type: text/html;charset=UTF-8");
include_once $_SERVER[DOCUMENT_ROOT].'/m/header.php';
include_once $_SERVER[DOCUMENT_ROOT].'/m/code/shopPlus_exec.php';
?>
<?php

	$shop_idx = $_GET['idx'];
	$shop_name = $_GET['shop_name'];
	
	$price_type_result = getPrice_type($connect, $shop_idx);
	$price_type_rows = mysqli_num_rows($price_type_result); //price_type의 갯수 / price_type2가 null인 값은 제외
	
	$price_result = array();	
	$price_type_val = array();
	$price_type_this_val = array('1','2');
	
	$i = 0;
	while ($price_type = mysqli_fetch_assoc($price_type_result)) {
		$price_type_val[$i] =  $price_type['price_type'];
		
		$price_result[$price_type['price_type']] = array();
		$price_result[$price_type['price_type']] = getPrice($connect, $shop_idx, $price_type['price_type']);

		$i++;
	} // type 별로 price_result 배열에 저장
	
	$price_num_result = array();
	$price_num_rows = array();
	for($i = 0; $i < count($price_type_this_val); $i++){
		$price_num_result[$i] = getPrice_num($connect, $shop_idx,$price_type_this_val[$i]);
		$price_num_rows[$i] = mysqli_num_rows(($price_num_result[$i]));
	}
	


?>
<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="<?=$path?>/js/shopFunctiontest.js"></script>
<script>
$( document ).ready(function() {
	var isData = '<?=$price_type_rows?>';

	var ori_type =  $.parseJSON('<? echo json_encode($price_type_this_val); ?>');
	var type = $.parseJSON('<? echo json_encode($price_type_val); ?>');
	var diff = $(ori_type).not(type).get();
	
//	alert(ori_type+"/"+type+"/"+diff);

	if(diff.length == 0){
		for(var i = 0; i < ori_type.length ; i++){
			$('.stay_insert'+ori_type[i]).remove();
		}
	}else if(diff.length > 0){
		for(var i = 0; i < diff.length ; i++){
			$('.stay_edit'+diff[i]).remove();
		}
		for(var i = 0; i < type.length ; i++){
			$('.stay_insert'+type[i]).remove();
		}
	}


});
</script>
<h2 class="title_sub"> 가맹점 메뉴 리스트 </h2>

<div class="pop_btn_set">
	
	<input type="button" name="btn_rgst" value="닫기" class="edit_btn" id="closeButton" />
</div>

<table class="list_data">
	<colgroup>
		<col style="width:30%;">
		<col align="left" style="width:70%;" >
	</colgroup>
<input type="hidden" value="<?=$shop_idx?>" id="h_idx" />
	<tr class="pop_list_item">
		<th>가맹점명</th>
		<td><?=$shop_name?></td>
	</tr>
	<tr class="pop_list_item">
		<th>메뉴정보</th>
		<td>
			<div class="shop_menu stay_edit1">
				<?/* 삭제버튼은 두번째 메뉴 입력 항목부터
				<div><input type="button" name="btn_menu_delete" value="X" class="btn_menu_delete edit_btn"
							id="btn_menu_delete"></div>
				*/?>
				<input type="button" value="삭제" class="edit_btn" name="btn_price_delete" id="btn_price_delete" style="float: right; margin-bottom:10px" />
				<input type="hidden" value="1" id="h_type" />
				
				<? //메뉴판 입력폼 -- 폼 추가 액션 : shopFunction.js ?>
				<form class="shopPlus_stay_form" method="post" >
					<input type='hidden' name='shop_menu_idx' id='shop_menu_idx' value="<?=$shop_idx?>">
					<input type='hidden' name='price_type' id='price_type' value='1'>
					<input type='hidden' name='isChange_time' id='isChange_time' value='0'>
					<input type='hidden' name='edit_price' id='edit_price' value='1'>
					
					<div id="table_stay">
						<대실>
						<table id="stay_time" style="margin-bottom:50px">
							<? 
								$type2 = array();
								$name_num = array();
								$name = array();
								$price_val = array();
								$info = array();
								$idx = array();

								$i = 0;
								while ($price_num = mysqli_fetch_assoc($price_num_result[0])) {
									$type2[$i] = $price_num['price_type2'];
									$name_num[$i] = $price_num['name_num'];
									$i++;

									//echo $price_num['price_type2'].":".$price_num['name_num']."<br/>";
								}
								
								
								$i = 0;
								while ($price = mysqli_fetch_assoc($price_result[1])) {
									$name[$i] = $price['name'];
									$price_val[$i] = $price['price'];
									$info[$i] = $price['info'];
									$idx[$i] = $price['idx'];
									$i++;
								}
								
							?>
							<?	echo "<tr>
											<th>객실타입▼/요일▶</th>";
										 
								for($i = 0; $i < $price_num_rows[0]; $i++){
									echo "<th>".$type2[$i]."<input type='hidden' name='price_type2[]' class='input_text' size='10' value='".$type2[$i]."'></th>";	  
								}
								echo "</tr>";
								
								for($i = 0; $i < $name_num[0]; $i++){
									echo "<tr>";
									echo "<th>".$name[($price_num_rows[0]*$i)].
										"<input type='hidden' name='name[]' class='input_text' size='10' value='".$name[($price_num_rows[0]*$i)]."'></th>";
									for($j = 0; $j < $price_num_rows[0]; $j++){
										echo "<td><input type='text' name='price[]' class='input_text' size='10' value='".$price_val[$j+($price_num_rows[0]*$i)]."'>원";
										echo "<br/>";
										echo " <textarea rows='2' cols='15' name='info[]' class='input_text' >".$info[$j+($price_num_rows[0]*$i)]."</textarea>";
										echo "<input type='hidden' name='idx[]' value='".$idx[$j+($price_num_rows[0]*$i)]."'>";
										echo "</td>";	  
									}
									echo "</tr>";
								}	
								
							

								
							?>
						</table>

					</div>
				<input type="submit" value="메뉴저장" class="edit_btn" style="float: right; margin-top: -38px;" />
				
				</form>
					<? //메뉴판 입력폼 ?>
			</div>


			<div class="shop_menu  stay_edit2">
				<input type="button" value="삭제" class="edit_btn" name="btn_price_delete" id="btn_price_delete" style="float: right; margin-bottom:10px" />
				<input type="hidden" value="2" id="h_type" />

				<form class="shopPlus_stay_form" method="post" >
					<input type='hidden' name='shop_menu_idx' id='shop_menu_idx' value="<?=$shop_idx?>">
					<input type='hidden' name='price_type' id='price_type' value='2'>
					<input type='hidden' name='isChange_day' id='isChange_day' value='0'>
					<input type='hidden' name='edit_price' id='edit_price' value='1'>
					<div>
						<숙박>
						<table id="stay_time" style="margin-bottom:50px">
							<?	
								$type2 = array();
								$name_num = array();
								$name = array();
								$price_val = array();
								$info = array();
								$idx = array();
	
								$i = 0;
								while ($price_num = mysqli_fetch_assoc($price_num_result[1])) {
									$type2[$i] = $price_num['price_type2'];
									$name_num[$i] = $price_num['name_num'];
									$i++;

									//echo $price_num['price_type2'].":".$price_num['name_num']."<br/>";
								}

								$i = 0;
								while ($price = mysqli_fetch_assoc($price_result[2])) {
									$name[$i] = $price['name'];
									$price_val[$i] = $price['price'];
									$info[$i] = $price['info'];
									$idx[$i] = $price['idx'];
									$i++;

									//echo $price['name'].":".$price['price'].";".$price['info']."<br/>";
								}

								//print_r($price);
							?>
							<?	echo "<tr>
											<th>객실타입▼/요일▶</th>";
										 
								for($i = 0; $i < $price_num_rows[1]; $i++){
									echo "<th>".$type2[$i]."<input type='hidden' name='price_type2[]' class='input_text' size='10' value='".$type2[$i]."'></th>";	  
								}
								echo "</tr>";
								
								for($i = 0; $i < ($name_num[1]); $i++){
									echo "<tr>";
									echo "<th>".$name[($price_num_rows[1]*$i)].
										"<input type='hidden' name='name[]' class='input_text' size='10' value='".$name[($price_num_rows[1]*$i)]."'></th>";
									for($j = 0; $j < $price_num_rows[1]; $j++){
										echo "<td><input type='text' name='price[]' class='input_text' size='10' value='".$price_val[$j+($price_num_rows[1]*$i)]."'>원";
										echo "<br/>";
										echo " <textarea rows='2' cols='15' name='info[]' class='input_text' >".$info[$j+($price_num_rows[1]*$i)]."</textarea>";
										echo "<input type='hidden' name='idx[]' value='".$idx[$j+($price_num_rows[1]*$i)]."'>";
										echo "</td>";	  
									}
									echo "</tr>";
								}	
							?>
						</table>
					</div>
					<input type="submit" value="메뉴저장" class="edit_btn" style="float: right; margin-top: -38px;" />
				</form>
				<? //메뉴판 입력폼 ?>
			</div>


			<div class="shop_menu stay_insert1" >
				<?/* 삭제버튼은 두번째 메뉴 입력 항목부터
				<div><input type="button" name="btn_menu_delete" value="X" class="btn_menu_delete edit_btn"
							id="btn_menu_delete"></div>
				*/?>
				
				<? //메뉴판 입력폼 -- 폼 추가 액션 : shopFunction.js ?>
				<form class="shopPlus_stay_form" method="post" >
					<input type='hidden' name='shop_menu_idx' id='shop_menu_idx' value="<?=$shop_idx?>">
					<input type='hidden' name='price_type' id='price_type' value='1'>
					<input type='hidden' name='isChange_time' id='isChange_time' value='0'>
					<input type='hidden' name='insert_price' id='insert_price' value='1'>
					
					<div id="table_stay">
						<대실> <br/>
						요일 갯수 : <select id="day_time" name="day">
										<option value="0">선택</option>
										<option value="1">1</option>
										<option value="2">2</option>
										<option value="3">3</option>
										<option value="4">4</option>
									</select>
						<input type="button" value="객실타입추가" class="edit_btn" id="add_room_time" style="display:none"/>
						
						<br/>
						<table id="stay_time" style="display:none">
						</table>

					</div>
					<input type="submit" value="메뉴저장" class="edit_btn" style="float: right; margin-top: -38px;" />
					</form>
					<? //메뉴판 입력폼 ?>
				</div>
			<div class="shop_menu stay_insert2" >
				<form class="shopPlus_stay_form" method="post" >
					<input type='hidden' name='shop_menu_idx' id='shop_menu_idx' value="<?=$shop_idx?>">
					<input type='hidden' name='price_type' id='price_type' value='2'>
					<input type='hidden' name='isChange_day' id='isChange_day' value='0'>
					<input type='hidden' name='insert_price' id='insert_price' value='1'>
					<div>
						<숙박> <br/>
						요일 갯수 : <select id="day_day" name="day">
										<option value="0">선택</option>
										<option value="1">1</option>
										<option value="2">2</option>
										<option value="3">3</option>
										<option value="4">4</option>
									</select>
						<input type="button" value="객실타입추가" class="edit_btn" id="add_room_day" style="display:none"/>
						
						<br/>
						<table id="stay_day" style="display:none">
						</table>
					</div>
					<input type="submit" value="메뉴저장" class="edit_btn" style="float: right; margin-top: -38px;" />
				</form>
				<? //메뉴판 입력폼 ?>
			</div>

		</td>
	</tr>

</table>


</body>
</html>