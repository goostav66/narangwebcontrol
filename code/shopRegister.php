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

	$auth = getAuth($connect, $id);
	$isAgent = 'n';

	$result = getShopList($connect, $auth, $id);
	$num = mysqli_num_rows($result) + 1;
	

	trimShopFile($connect);
?>

<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="<?=$path?>/js/shopFunction.js?ver=1"></script>
<script>
window.name = "register";
</script>

<h2 class="title_sub"> 가맹점관리 </h2>
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
		<th>가맹점 업소번호</th>
		<th class="last">등록</th>
	</tr>
	<tr class="list_item"><form method="post" id="form_shop_regist">
        <td><input type="text" name="shop_name"></td>
        <td>
            <select id="select_shop_type" name="shop_type">
                <option value="0">분류 선택</option>
                <option value="1">한식</option>
                <option value="2">일식</option>
                <option value="3">노래방</option>
                <option value="4">퓨전주점</option>
                <option value="5">유흥주점</option>
                <option value="6">기타</option>
            </select>
        </td>
        <td>
            <select id='select_branch' name='branch'>
                <option value='0'>지사 선택</option>";
	<?php
        $branchList = getBranchList($connect, $auth, $id);
        while($branch_opt = mysqli_fetch_assoc($branchList)){
            echo "<option value='".$branch_opt['branch_code']."'>".$branch_opt['branch_name']."</option>";
        } ?>
            </select>
        </td>
        <td>
            <input type="hidden" name="isAgent" id="isAgent" value="<?=$isAgent?>" />
            <select id="select_agent" name="agent">
                <option value="0">대리점 선택</option>
            </select>
        </td>
        <td> <input type="text" name="shop_ceo_name"> </td>
        <td> <input type="text" name="shop_phone"> </td>
        <td class="last">
            <button type="button" class="edit_btn" id="btn_regist_shop">등록</button>
        </td>
    </form></tr>
</table>

<div class="list_line"></div>
<div id="searchBar_hpno">
	<select id="search_type" name="search_type">
		<option value="shop_name">가맹점명</option>
		<option value="branch_name">지사</option>
		<option value="agent_name">대리점</option>
		<option value="shop_phone">가맹점 대표번호</option>
	</select>
	<input type="search" name="search_text" id="search_text">
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
	</colgroup>
	<tr>
		<th>No.</th>
		<th>고유 URL</th>
		<th>가맹점명</th>
		<th>주소</th>
		<th>지사</th>
		<th>대리점</th>
		<th>대표자</th>
		<th>가맹점 대표번호</th>
		<th class="last">등록일</th>
	</tr>

<?php
while ($row = mysqli_fetch_assoc($result)) {
	$num--;
?>
<tr class="list_item list_click insert_item">
	<input type="hidden" name="shop_idx" value="<?=$row['idx']?>">
	<td> <?=$num ?></td>
    <td> http://hanjicds001.gabia.io/index.jsp?p=<?=$row["url"]?></td>
	<td> <?=$row["shop_name"]?></td>
	<td> <?=$row["shop_addr"]?></td>
	<td> <?=$row["branch_name"]?></td>
	<td> <?=$row["agent_name"]?></td>
	<td> <?=$row["shop_ceo_name"]?></td>
	<td> <?=$row["shop_phone"]?></td>
	<td class="last"><?=$row["shop_rgst_date"]?></td>
</tr>

<?php
}
/* free result set */
mysqli_free_result($result);
?>

</table>


</body>
</html>
