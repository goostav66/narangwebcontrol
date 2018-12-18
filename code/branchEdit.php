<?php session_start(); ?>
<?php 
header("Content-Type: text/html;charset=UTF-8");
include_once $_SERVER[DOCUMENT_ROOT].'/m/header.php';
include_once $_SERVER[DOCUMENT_ROOT].'/m/code/branch_exec.php';
?>
<?php
	$id = $_GET['id'];
	$auth = $_SESSION['auth'];
	$branch_idx = $_GET['idx'];

	$row = getBranchInfo($connect, $branch_idx);
?>
<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="<?=$path?>/js/branchFunction.js?ver=1"></script>
<style>
	.pop_dist_shop{
		cursor: pointer;
	}
	.pop_dist_shop:hover{
		text-decoration: underline;
	}
</style>
<h2 class="title_sub"> 지사 정보 수정 </h2>

<div class="pop_btn_set">
	<input type="button" name="btn_rgst" value="NFC" class="edit_btn" id="NFCButton">
	<input type="submit" name="btn_rgst" value="저장 / 수정" class="edit_btn" id="editButton">
    <? if($auth=='manager'){?>
	<input type="submit" name="btn_rgst" value="지사삭제" class="edit_btn" id="removeButton"><? }?>
	<input type="button" name="btn_rgst" value="닫기" class="edit_btn" id="closeButton">
</div>


<table class="list_data">
	<colgroup>
		<col style="width:30%;">
		<col align="left" style="width:70%;" >
	</colgroup>
	<form method="post" id="branch_edit_form" name="branch_edit_form">
	<input type="hidden" name="edit_branch_idx" value="<?=$branch_idx?>"/>
	<tr class="pop_list_item">
		<th>코드</th>
		<td><input type="text" name="branch_code" class="input_text" value="<?=$row['branch_code']?>" readonly="readonly"></td>
	</tr>
	<tr class="pop_list_item">
		<th>아이디</th>
		<td><input type="text" name="branch_id" class="input_text" value="<?=$row['branch_id']?>" readonly="readonly"></td>
	</tr>
	<tr class="pop_list_item">
		<th>비밀번호</th>
		<td><input type="text" name="pw" class="input_text" value="<?=$row['pw']?>"></td>
	</tr>
	<tr class="pop_list_item">
		<th>지사</th>
		<td><input type="text" name="branch_name" class="input_text" value="<?=$row['branch_name']?>"></td>
	</tr>
	<tr class="pop_list_item">
		<th>대표자</th>
		<td><input type="text" name="ceo_name" class="input_text" value="<?=$row['branch_ceo_name']?>"><br>
        	대표자 연락처 <input type="text" name="ceo_phone" class="input_text" value="<?=$row['branch_ceo_phone']?>"></td>
	</tr>
	<tr class="pop_list_item">
		<th>관리자</th>
		<td><input type="text" name="manager_name" class="input_text" value="<?=$row['branch_manager_name']?>"><br>
        	관리자 연락처 <input type="text" name="manager_phone" class="input_text" value="<?=$row['branch_manager_phone']?>"><br>
            팩스 <input type="text" name="fax" class="input_text" value="<?=$row['branch_fax_num']?>"></td>
	</tr>
	<tr class="pop_list_item">
		<th>콜센터</th>
		<td><input type="text" name="call_center" class="input_text" value="<?=$row['call_center']?>"></td>
	</tr>
	<tr class="pop_list_item">
		<th>등록일</th>
		<td><input type="text" name="rgst_date" class="input_text" value="<?=$row['branch_rgst_date']?>" readonly="readonly">
		</td>
	</tr>
	<tr class="pop_list_item">
		<th>주소</th>
		<td><input type="text" name="addr" class="input_text" value="<?=$row['branch_addr']?>"></td>
	</tr>
	<tr class="pop_list_item">
		<th>계좌</th>
		<td>예금주 <input type="text" name="bank_name" class="input_text" value="<?=$row['branch_bank_name']?>"><br>
        	계좌번호 <input type="text" name="bank_num" class="input_text" value="<?=$row['branch_bank_num']?>">
        </td>
	</tr>
</form>

	<tr class="pop_list_item">
    	<th>관할 대리점</th>
		<td>
		<?php
            $agent_list = getAuthAgentList($connect, $branch_idx);
            while($agent = mysqli_fetch_assoc($agent_list)){?> 
     		<a href="#"><?=$agent['agent_name']?></a>   
        <? } ?>   
   	    </td>
    </tr>

    <tr class="pop_list_item">
		<th>관할 가맹점</th>
		<td>
        <?php 
			$shop_list = getAuthShopList($connect, $branch_idx);
			while($shop = mysqli_fetch_assoc($shop_list)){?>
            <a class="pop_dist_shop" id='<?=$shop['idx']?>'><?=$shop['shop_name'] ?></a>
		<? } ?>
        </td>
	</tr>
</table>
<?php
/* free result set */
mysqli_free_result($result);
?>

</body>
</html>