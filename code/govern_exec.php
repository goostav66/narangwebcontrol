<?php session_start(); ?>

<?php 
include_once $_SERVER[DOCUMENT_ROOT].'/m/data/DB_connect.php';
	if (mysqli_connect_errno()) {
			printf("Connect failed: %s\n", mysqli_connect_error());
			exit();
	}
	
	//새 축제/행사 추가
	if( isset($_POST['f_title']) && !empty($_POST['f_title']) && isset($_POST['g_idx_fes']) ){
		$g_idx = $_POST['g_idx_fes'];
		insertGovern_festival($connect, $g_idx);
	}
	
	//축제/행사 수정
	if( isset($_POST['f_title']) && !empty($_POST['f_title']) && isset($_POST['f_idx_fes']) ){
		$f_idx = $_POST['f_idx_fes'];
		modifyGovern_festival($connect, $f_idx);
	}
	//축제/행사 삭제	
	if( isset($_POST['delete_f_idx']) && !empty($_POST['delete_f_idx']) ){
		$f_idx = $_POST['delete_f_idx'];
		deleteGovern_festival($connect, $f_idx);
	}
	
	function getGovern($connect, $id){
		$sql = "SELECT g.* FROM govern AS g, user AS u WHERE u.id='$id' AND u.auth = 'govern' AND g.designation = u.name";
		$result = mysqli_query($connect, $sql);
		$govern_infor = mysqli_fetch_assoc($result);
		return $govern_infor;
	}
	
	function getGovern_menu_list($connect, $g_idx){
		$sql = "SELECT * FROM govern_menu_list WHERE g_idx = '$g_idx'";
		$result = mysqli_query($connect, $sql);
		$menu_list = mysqli_fetch_assoc($result);
		return $menu_list;
	}
	
	function getGovern_festival_list($connect, $g_idx){
		$sql = "SELECT * FROM govern_festival WHERE g_idx = '$g_idx' ORDER BY f_order";
		$result = mysqli_query($connect, $sql);
		return $result;
	}
	
	function getGovern_festival_infor($connect, $f_idx){
		$sql = "SELECT * FROM govern_festival WHERE f_idx = '$f_idx'";
		$result = mysqli_query($connect, $sql);
		$festival = mysqli_fetch_assoc($result);
		return $festival;
	}
	
	function getGovern_festival_images($connect, $f_idx){
		$sql = "SELECT * FROM govern_festival_images WHERE f_idx = '$f_idx' ORDER BY f_image_order";
		$result = mysqli_query($connect, $sql);
		return $result;
	}
	
	function getGovern_specialty_category($connect, $g_idx){
		$sql = "SELECT * FROM govern_specialty WHERE g_idx = '$g_idx' ORDER BY s_order";
		$result = mysqli_query($connect, $sql);
		return $result;
	}
	
	//축제/행사 추가
	function insertGovern_festival($connect, $g_idx){		
		$f_title = $_POST['f_title'];
		$f_subtitle = $_POST['f_subtitle'];
		$f_period = $_POST['f_period'];
		$f_content = $_POST['f_content'];
		$f_location = $_POST['f_location'];
		$last_order = 1;
		
		$sql = "SELECT MAX(f_order) AS max FROM govern_festival WHERE g_idx = '$g_idx'";
		$result = $connect->query($sql);
		
		if($row = mysqli_fetch_assoc($result)){
			$max = $row['max'];
			if($max != null && $max > 0)
				$last_order = ($max+1);
		}
		
		$sql = "INSERT INTO govern_festival (g_idx, f_title, f_subtitle, f_period, f_content, f_location, f_order)"
				."VALUES ('$g_idx', '$f_title', '$f_subtitle', '$f_period', '$f_content', '$f_location', '$last_order')";
				
		$connect->query($sql);
		$f_idx = $connect->insert_id;
		//사진 추가
		if( isset($_POST['arr_file_name']) && !empty($_POST['arr_file_name']) ){
			$arr_file_name = explode(",", $_POST['arr_file_name']);
			for($x = 0; $x < count($arr_file_name); $x++){
				$i = $arr_file_name[$x];
				$sourceFile = $_FILES["file_".$arr_file_name[$x]]["tmp_name"];
				if( isset($sourceFile) ){
					$tmp = $_FILES["file_".$arr_file_name[$x]]["name"];
					$ext = pathinfo($tmp, PATHINFO_EXTENSION);
					$targetPath = date("YmdHis")."_".$i.".".$ext;
					
					$moved = move_uploaded_file($sourceFile, "festival/".$targetPath);
					
					if( $moved ) {
						
						$f_image_url = "http://103.60.124.17/m/code/festival/".$targetPath;
						$f_image_order = $x+1;
						$sql = "INSERT INTO govern_festival_images (f_idx, f_image_url, f_image_order) VALUES ('$f_idx', '$f_image_url', '$f_image_order')";
						
						$connect->query($sql);
					}
				}
			}
			
		}
		$connect->close();
		echo "<script type='text/javascript'>window.close();window.opener.parent.location.reload();</script>";
	}
	
	//축제/행사 수정
	function modifyGovern_festival($connect, $f_idx){
		$f_title = $_POST['f_title'];
		$f_subtitle = $_POST['f_subtitle'];
		$f_period = $_POST['f_period'];
		$f_content = $_POST['f_content'];
		$f_location = $_POST['f_location'];
		
		$sql = "UPDATE govern_festival SET f_title='$f_title', f_subtitle='$f_subtitle', f_period='$f_period', f_content='$f_content', f_location='$f_location' WHERE f_idx = '$f_idx'";
		
		if($connect->query($sql) === TRUE){
			//사진 추가
			if( isset($_POST['arr_file_name']) && !empty($_POST['arr_file_name']) ){
				$arr_file_name = explode(",", $_POST['arr_file_name']);
				for($x = 0; $x < count($arr_file_name); $x++){
					$i = $arr_file_name[$x];
					$sourceFile = $_FILES["file_".$arr_file_name[$x]]["tmp_name"];
					if( isset($sourceFile) ){
						$tmp = $_FILES["file_".$arr_file_name[$x]]["name"];
						$ext = pathinfo($tmp, PATHINFO_EXTENSION);
						$targetPath = date("YmdHis")."_".$i.".".$ext;
						
						$moved = move_uploaded_file($sourceFile, "festival/".$targetPath);
						
						if( $moved ) {			
							$f_image_url = "http://103.60.124.17/m/code/festival/".$targetPath;
							$f_image_order = $x+1;
							$sql = "INSERT INTO govern_festival_images (f_idx, f_image_url, f_image_order) VALUES ('$f_idx', '$f_image_url', '$f_image_order')";
							
							$connect->query($sql);
						}
					}
				}
			}
			//사진 삭제
			if( isset($_POST['arr_file_idx']) && !empty($_POST['arr_file_idx']) ){
				$arr_file_idx = explode(",", $_POST['arr_file_idx']);
				for($x = 0; $x < count($arr_file_idx); $x++){
					$fi_idx = $arr_file_idx[$x];
					$sql = "SELECT f_image_url FROM govern_festival_images WHERE fi_idx = '$fi_idx'";
						
					$result = mysqli_query($connect, $sql);
					if($row = mysqli_fetch_assoc($result)){
						$f_image_url = $row['f_image_url'];
						$unlink_file = substr( $row['f_image_url'], stripos($row['f_image_url'], "festival/") );
						unlink($unlink_file);
					}
											
					$sql = "DELETE FROM govern_festival_images WHERE fi_idx = '$fi_idx'";
					$connect->query($sql);
				}
			}
		}
		$connect->close();
		echo "<script type='text/javascript'>window.close();window.opener.parent.location.reload();</script>";
	}
	
	
	//축제/행사 삭제
	function deleteGovern_festival($connect, $f_idx){
		$sql = "DELETE FROM govern_festival WHERE f_idx = '$f_idx'";
		
		if($connect->query($sql) === TRUE) {
			$sql = "SELECT * FROM govern_festival_images WHERE f_idx = '$f_idx'";
			$result = mysqli_query($connect, $sql);
			
			while($row = mysqli_fetch_assoc($result)){			
				$unlink_file = substr( $row['f_image_url'], stripos($row['f_image_url'], "festival/") );
				unlink($unlink_file);
			}
			
			mysqli_query($connect, "DELETE FROM govern_festival_images WHERE f_idx = '$f_idx'");
		}
		
		$connect->close();
	}
?>