<?php session_start(); ?>
<?php
include_once $_SERVER[DOCUMENT_ROOT].'/m/data/DB_connect.php';

	/* check connection */
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}

	# 지사 등록
	if(isset($_POST['new_branch_code']) && !empty($_POST['new_branch_code']) ){
		registerBranch($connect);
	}
	
	//지사 수정
	if(isset($_POST['edit_branch_idx']) && !empty($_POST['edit_branch_idx'])){
		$idx = $_POST['edit_branch_idx'];
		
		editBranch($connect, $idx);
	}
	
	//지사 삭제
	if(isset($_POST['del_branch_idx']) && !empty($_POST['del_branch_idx'])){
		$idx = $_POST['del_branch_idx'];
		//echo "success";
		deleteBranch($connect, $idx);
	}
	
	# 지사 목록 가져오기
	function getBranchList($connect){
		$qry = "SELECT * FROM branch ORDER BY branch_rgst_date DESC";
		$result = mysqli_query($connect, $qry);
		
		return $result;
	}

	# 지사 정보 가져오기
	function getBranchInfo($connect, $branch_idx){
		$sql = "SELECT b.*, u.pw FROM branch AS b, user AS u WHERE branch_idx = $branch_idx AND b.branch_id = u.id";
		$result = mysqli_query($connect, $sql);
		$row = mysqli_fetch_assoc($result);
		return $row;
	}
	
	# 지사 등록
	function registerBranch($connect){
		$code = $_POST['new_branch_code'];

		$sql = "SELECT branch_code FROM branch WHERE branch_code = '$code'";
		$result = mysqli_query($connect, $sql);
		if( mysqli_num_rows($result) > 0 ){
			echo "duplicate";
		
		}else{
			$id = 'nfc'.$code;
			$name = $_POST['branch_name'];
			$ceo_name = $_POST['branch_ceo_name'];
			$ceo_phone = $_POST['branch_ceo_phone'];
			$call_center = $_POST['call_center'];
			$sql = "INSERT branch (branch_code, branch_id, branch_name, branch_ceo_name, branch_ceo_phone, call_center) VALUES ('$code', '$id', '$name', '$ceo_name', '$ceo_phone', '$call_center')";

			if( mysqli_query($connect, $sql) ){

				$branch_idx = mysqli_insert_id($connect);

				$sql = "INSERT user (id, pw, auth, name, rgst_date) VALUES ('$id', '1234', 'branch', '$branch_name', CURRENT_TIMESTAMP)";
				if( mysqli_query($connect, $sql) ){

					$list_new = getBranchInfo($connect, $branch_idx);

					echo "<tr class='list_item list_click list_new'>";
					echo 	"<input type='hidden' name='branch_idx' value='".$branch_idx."'>";
					echo	"<td></td>";
					echo 	"<td>".$list_new['branch_code']."</td>";
					echo 	"<td>".$list_new['branch_name']."</td>";
					echo 	"<td>".$list_new['branch_id']."</td>";
					echo 	"<td>".$list_new['branch_ceo_name']."</td>";
					echo 	"<td></td>";
					echo 	"<td></td>";
					echo 	"<td>".$list_new['call_center']."</td>";
					echo 	"<td>".$list_new['branch_rgst_date']."</td>";
					echo "</tr>";
				}else{
					echo "error";
				}
			}
		}

	}

	# 관할 대리점 목록 가져오기
	function getAuthAgentList($connect, $branch_idx){
		$sql = "SELECT a.* FROM agent AS a 
				LEFT JOIN branch AS b ON a.branch_code = b.branch_code 
				WHERE branch_idx = $branch_idx";
	}
	
	# 관할 가맹점 목록 가져오기
	function getAuthShopList($connect, $branch_idx){
		$sql = "SELECT s.* FROM shop AS s 
				LEFT JOIN agent AS a ON a.agent_code = s.manager_code 
				LEFT JOIN branch AS b ON b.branch_code = a.branch_code OR b.branch_code = s.manager_code 
				WHERE branch_idx = $branch_idx 
				ORDER BY idx";

		$result = mysqli_query($connect, $sql);
		return $result;
	}	
	
	function editBranch($connect, $idx){
		
		$code = $_POST['branch_code'];
		$id = $_POST['branch_id'];
		$pw = $_POST['pw'];
		$branch_name = $_POST['branch_name'];
		$ceo_name = $_POST['ceo_name'];
		$ceo_phone = $_POST['ceo_phone'];
		$manager_name = $_POST['manager_name'];
		$manager_phone = $_POST['manager_phone'];
		$fax = $_POST['fax'];
		$call_center = $_POST['call_center'];
		$rgst_date = $_POST['rgst_date'];
		$addr = $_POST['addr'];
		$bank_name = $_POST['bank_name'];
		$bank_num = $_POST['bank_num'];

		$qry = "UPDATE branch SET branch_code = '$code', branch_id = '$id', branch_name = '$branch_name', branch_ceo_name = '$ceo_name', branch_ceo_phone = '$ceo_phone', branch_manager_name = '$manager_name', branch_manager_phone = '$manager_phone', branch_fax_num = '$fax', call_center = '$call_center', branch_rgst_date = '$rgst_date', branch_addr = '$addr', branch_bank_name = '$bank_name', branch_bank_num = '$bank_num'
		
		WHERE branch_idx = '$idx'";

		if ($connect->query($qry) === TRUE) {
			$qry2 = "UPDATE user SET pw = '$pw' WHERE id = '$id'";
			
			if($connect->query($qry2) === TRUE){
				echo "success";	
			}
		} else {
			echo "Error qry1: " . $qry . "<br>" . $connect->error;
		}
	}
	
	# 지사 삭제
	function deleteBranch($connect, $branch_idx){
		$sql = "SELECT b.branch_code, b.branch_id, a.agent_code, s.url 
				FROM branch AS b 
				LEFT JOIN agent as a ON a.branch_code = b.branch_code 
				LEFT JOIN shop as s ON s.manager_code = b.branch_code OR s.agent_idx = a.agent_idx 
				WHERE b.branch_idx = '$branch_idx' LIMIT 1";

		$result = mysqli_query($connect, $sql);
		$row = mysqli_fetch_assoc($result);
		
		if ( empty($row['agent_code']) && empty($row['url']) ){
			$branch_id = $row['branch_id'];
			$sql = "DELETE FROM branch WHERE branch_idx = $branch_idx";
			if(mysqli_query($connect, $sql)){
				$sql = "DELETE FROM user WHERE id = '$branch_id'";
				mysqli_query($connect, $sql);
				echo "success";
			}		
		}else{
			echo "등록된 대리점이 있어 삭제할 수 없습니다.";
		}

		
	}
?>