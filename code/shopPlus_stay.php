<?php session_start(); ?>
<?php 
header("Content-Type: text/html;charset=UTF-8");
include_once $_SERVER[DOCUMENT_ROOT].'/m/nav.php';
include_once $_SERVER[DOCUMENT_ROOT].'/m/code/shopPlus_exec.php';
?>

<?php
	$id = $_SESSION['id'];

	$s_type = $_GET['s_type'];
	$s_text = $_GET['s_text'];

	$isAgent = 'n';

	if(isset($s_type) && !empty($s_type) && isset($s_text) && !empty($s_text)){
		
		$result = searchShop_re($connect, $s_type, $s_text, $auth, $id);

		$branch = getBranch_M($connect);
		if ($auth == 'agent'){			
			$isAgent = 'y';
		}
	
		$num_rows = mysqli_num_rows($result);
		$num = $num_rows + 1; // 가맹점 수

	}else {
		
		$branch = getBranch($connect, $auth, $id);
		$result = ViewList($connect, $auth, $id);

		if ($auth == 'agent'){
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

<h2 class="title_sub"> Na랑 zzZ 관리 </h2>
<form method="post" id="shop_rgst_form" name="shop_rgst_form">
<table class="list_data">

	<tr>
		<th>가맹점명</th>
		<th>지사</th>
		<th>대리점</th>	
		<th>대표자</th>
		<th>가맹점 대표번호</th>
		<th class="last"> 등록</th>

	</tr>
	<tr class="list_item">
		<td> <input type="text" name="rgst_name" class="input_text" id="shop_name"> </td>
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
				<input type="hidden" name="shop_type" id="shop_type" value="1" />
		<td class="last"> 
			<input type="submit" name="btn_rgst" value="등록" class="edit_btn" id="rgstButton">
		</td>
		
	</tr>
</table>
</form>
<div class="list_line"></div>

<div id="searchBar_hpno">
* 검색기능 준비 중입니다.
	<select id="search" name="search">
		<option value="shop_name">가맹점명</option>
		<option value="branch_name">지사</option>
		<option value="agent_name">대리점</option>
		<option value="shop_tel">가맹점 대표번호</option>
	</select>
	<input type="text" name="search_text" id="search_text">
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
	<td class="last"> <?//=$row["cnt"]  ?></td>	
</tr>

<?php
}
/* free result set */
mysqli_free_result($result);
?>

</table>


</body>
</html>