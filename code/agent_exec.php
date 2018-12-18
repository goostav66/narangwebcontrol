<?php session_start(); ?>

<?php
	include_once $_SERVER[DOCUMENT_ROOT].'/m/data/DB_connect.php';

	/* check connection */
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}

	# 대리점 목록 가져오기
	function getAgentList($connect, $id, $auth){
		$sql = "SELECT auth FROM user WHERE id = '$id' ";

		$result = mysqli_query($connect, $sql);
		$row = mysqli_fetch_assoc($result);
		if( 'manager' == $auth ){//관리자일때 모든 대리점 리스트 불러오기
			$sql = "SELECT a.*, b.branch_name FROM agent AS a LEFT JOIN branch AS b ON b.branch_code = a.branch_code ORDER BY agent_idx";

			$result = mysqli_query($connect, $sql);
		}else if( 'branch' == $auth ){//지사일 때 관할 대리점 리스트 불러오기
			$sql = "SELECT a.*, b.branch_name FROM agent AS a LEFT JOIN branch AS b ON b.branch_code = a.branch_code 
					WHERE b.branch_id = '$id' ORDER BY branch_idx";
			$result = mysqli_query($connect, $sql);
		}

		return $result;
	}

	# 관할 지사 선택시 선택할 수 있는 지사 목록 가져오기
	function getBranchList($connect, $id, $auth){

		$result = mysqli_query($connect, $sql);
		$row = mysqli_fetch_assoc($result);
		if( 'manager' == $auth ){//관리자일때 모든 지사 목록 불러오기
			$sql = "SELECT branch_idx, branch_name FROM branch ORDER BY branch_name";
			$result = mysqli_query($connect, $sql);
		}else if( 'branch' == $auth ){//지사일 때는 해당 지사만 선택 가능
			$sql = "SELECT branch_idx, branch_name FROM branch WHERE branch_id = '$id'";
			$result = mysqli_query($connect, $sql);
		} 
		return $result;
	}
?>
