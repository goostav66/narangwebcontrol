<?php session_start(); ?>
<?php 
header("Content-Type: text/html;charset=UTF-8");
include_once $_SERVER[DOCUMENT_ROOT].'/m/nav.php';
include_once $_SERVER[DOCUMENT_ROOT].'/m/code/shop_exec.php';
?>

<?php

	$id = $_SESSION['id'];

	$s_type = $_GET['s_type'];
	$s_text = $_GET['s_text'];

	$isAgent = 'n';

	if(isset($s_type) && !empty($s_type) && isset($s_text) && !empty($s_text)){
		
		$result = searchShop_re($connect, $s_type, $s_text, $auth, $id);

		if($auth == 'manager'){
			$branch = getBranch_M($connect);
			
		}elseif ($auth == 'branch'){
			$branch = getBranch($connect, $auth, $id);
			
		}elseif ($auth == 'agent'){
			$branch = getBranch($connect, $auth, $id);
			
			$isAgent = 'y';
		}

		
		$num_rows = mysqli_num_rows($result);
		$num = $num_rows + 1; // 가맹점 수

	}else {
	
	
	
		if($auth == 'manager'){
			$branch = getBranch_M($connect);
			$result = ViewList_M($connect);
		}elseif ($auth == 'branch'){
			$branch = getBranch($connect, $auth, $id);
			$result = ViewList($connect, $auth, $id);
		}elseif ($auth == 'agent'){
			$branch = getBranch($connect, $auth, $id);
			$result = ViewList($connect, $auth, $id);
			
			$isAgent = 'y';
		}

		
		$num_rows = mysqli_num_rows($result);
		$num = $num_rows + 1; // 가맹점 수
	}
?>

<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="<?=$path?>/js/shopFunctiontest.js"></script>
<script>
window.name = "register";
</script>

<h2 class="title_sub"> 가맹점관리 </h2>
<form method="post" id="shop_rgst_form" name="shop_rgst_form">
<table class="list_data">
	<colgroup>
		<col style="width:20%;">
		<col style="width:10%;">
		<col style="width:10%;">
		<col style="width:10%;">
		<col style="width:15%;">
		<col style="width:15%;">
		<col style="width:10%;">
	</colgroup>
	<tr>
		<th>가맹점명</th>
		<th>업소분류</th>
		<th>지사</th>
		<th>대리점</th>
		<th>대표자</th>
		<th>가맹점 대표번호</th>
		<th>가맹점 H.P</th>
		<th class="last"> 등록</th>

	</tr>
	<tr class="list_item">
		<td> <input type="text" name="rgst_name" class="input_text" id="shop_name"> </td>
		<td>
			<select id="store_type" name="store_type">
				<option value="0">분류 선택</option>
				<option value="1">노래방</option>
				<option value="2">한식/일식</option>
				<option value="3">퓨전주점</option>
				<option value="4">유흥주점</option>
				<option value="5">기타</option>
			</select>
		</td>
		<td>  
			<select id='branch' name='branch'>
				<option value='0'>지사 선택</option>";
			<?php 
				while ($br = mysqli_fetch_assoc($branch)) {
					if($auth == "manager" or $auth == "branch"){
						echo "<option value='".$br['url']."'>".$br["branch_name"]."</option>";
					}else if($auth == "agent"){
						echo "<option value='".$br['br_url']."'>".$br["branch_name"]."</option>";
					}
				}
			?>
			</select>
		</td>
		<td>
			<input type="hidden" name="isAgent" id="isAgent" value="<?=$isAgent?>" />
			<select id="agent" name="agent">
				<option value="0">대리점 선택</option>
			</select>
		</td>

		<td> <input type="text" name="ceo_name" class="input_text"> </td>
		<td> <input type="text" name="shop_tel" class="input_text" id="shop_tel"> </td>
		<td> <input type="text" name="shop_phone" class="input_text"> </td>
		<td class="last"> 
			<input type="submit" name="btn_rgst" value="등록" class="edit_btn" id="rgstButton">
		</td>
		
	</tr>
</table>

<div class="list_line"></div>
<div id="searchBar_hpno">
	<select id="search" name="search">
	<?
		$s_type_array = array("","","","");
		switch($s_type){
			case 'shop_name' : $s_type_array[0] = "selected='seleced'"; break;
			case 'branch_name' : $s_type_array[1] = "selected='seleced'"; break;
			case 'agent_name' : $s_type_array[2] = "selected='seleced'"; break;
			case 'shop_tel' : $s_type_array[3] = "selected='seleced'"; break;
		}
	?>
		<option value="shop_name" <?=$s_type_array[0]?>>가맹점명</option>
		<option value="branch_name" <?=$s_type_array[1]?>>지사</option>
		<option value="agent_name" <?=$s_type_array[2]?>>대리점</option>
		<option value="shop_tel" <?=$s_type_array[3]?>>가맹점 대표번호</option>
	</select>
	<input type="text" name="search_text" id="search_text" value="<?=$s_text?>">
	<input type="hidden" name="auth" id="auth" value="<?=$auth?>">
	<input type="hidden" name="search_id" id="search_id" value="<?=$id?>">
	<input type="button" name="btn_search" value="검색" id="btn_search">

	<input type="hidden" name="h_search_type" id="h_search_type">
	<input type="hidden" name="h_search_text" id="h_search_text">

	<br/>
</div>

<div id="list_state"> <span id="s_search"></span><?php echo "전체 : ".$num_rows; ?></div>
<table class="list_data" id="insert_data">
	<colgroup>
		<col style="width:3%;">
		<col style="width:10%;">
		<col style="width:10%;">
		<col style="width:22%;">
		<col style="width:8%;">
		<col style="width:8%;">
		<col style="width:5%;">
		<col style="width:10%;">
		<col style="width:8%;">
		<col style="width:10%;">
		<col style="width:5%;">
	</colgroup>
	<tr>
		<th>No.</th>
		<th>고유 URL</th>
		<th>가맹점명</th>
		<th>주소</th>
		<th>지사</th>
		<th>대리점</th>
		<th>대표자</th>
		<th>대리점 관리자 ID</th>
		<th>가맹점 대표번호</th>
		<th>등록일</th>
		<th class="last">콜수</th>
	</tr>

<?php
while ($row = mysqli_fetch_assoc($result)) {
	$num--;


?>
<tr class="list_item list_click insert_item">
	<input type="hidden" name="idx" id="shop_idx" value="<?=$row["idx"] ?>" />
	<td> <?=$num ?></td>
	<td> http://m.nfczone.co.kr/nfc_index.jsp?p=<?=$row["url"] ?></td>
	<td> <?=$row["shop_name"] ?></td>
	<td> <?=$row["position"]  ?></td>
	<td> <?=$row["branch_name"] ?></td>
	<td> <?=$row["agent_name"]?></td>
	<td> <?=$row["ceo_name"]  ?></td>
	<td> <?=$row["id"]  ?></td>
	<td> <?=$row["shop_tel"]  ?></td>
	<td> <?=$row["rgst_date"]  ?></td>
	<td class="last"> <?=$row["cnt"]  ?></td>	
</tr>

<?php
}
/* free result set */
mysqli_free_result($result);
?>

</table>
</form>

</body>
</html>