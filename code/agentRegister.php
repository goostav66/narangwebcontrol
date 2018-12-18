<?php session_start(); ?>
<?php
header("Content-Type: text/html;charset=UTF-8");
include_once $_SERVER[DOCUMENT_ROOT].'/m/nav.php';
include_once $_SERVER[DOCUMENT_ROOT].'/m/code/agent_exec.php';
?>
<?php
	$id = $_SESSION['id'];
	$agentList = getAgentList($connect, $id, $auth);
?>
<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="<?=$path?>/js/agentFunction.js?ver=1"></script>

<h2 class="title_sub"> 대리점 관리 </h2>

<div>
	<table class="list_data">
		<colgroup>
			<col style="width: 20%">
			<col style="width: 20%">
			<col style="width: 15%">
			<col style="width: 15%">
			<col style="width: 20%">
			<col style="width: 10%">
		</colgroup>
		<tr>
			<th>대리점명</th>
			<th>대리점코드</th>
			<th>관할 지사</th>
			<th>대표자</th>
			<th>대표자 연락처</th>
			<th class="last">등록</th>
		</tr>
		<tr>
			<td><input type="text"></td>
			<td><input type="text"><button class="edit_btn">중복 확인</button></td>
			<td>
				<select>
		<? 
			$branchList = getBranchList($connect, $id);
			while($branch = mysqli_fetch_assoc($branchList)){
				echo "<option value='".$branch['branch_idx']."'>".$branch['branch_name']."</option>";
			}
		?>		
				</select>
			</td>
			<td><input type="text"></td>
			<td><input type="text"></td>
			<td class="last"><button class="edit_btn">등록</button></td>
		</tr>
	</table>
</div>

<div class="list_line"></div>

<div>
	<table class="list_data">
		<colgroup>
			<col style="">

		</colgroup>
		<tr>
			<th>대리점코드</th>
			<th>대리점명</th>
			<th>관할지사</th>
			<th>대표자</th>
			<th>등록일</th>
		</tr>
	<?
		while($agent = mysqli_fetch_assoc($agentList)){ ?>
		<tr>
			<td><?=$agent['agent_code']?></td>
			<td><?=$agent['agent_name']?></td>
			<td><?=$agent['branch_name']?></td>
			<td><?=$agent['agent_ceo_name']?></td>
			<td><?=$agent['agent_rgst_date']?></td>
		</tr>
	<? } ?>	
	</table>
</div>