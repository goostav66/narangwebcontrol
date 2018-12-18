
<?php
include_once $_SERVER[DOCUMENT_ROOT].'/m/data/DB_connect.php';

$upload_dir = $_SERVER['DOCUMENT_ROOT'] . "/m/code/upload";

//파일 업로드를 위해 메모리처리량 무제한으로 설정
ini_set('memory_limit','-1');

//error_reporting(E_ALL);
//ini_set("display_errors", 1);


if (is_dir($upload_dir) && is_writable($upload_dir)) {
  //writable
} else {
    echo 'Upload directory is not writable, or does not exist.';
}

if(isset($_FILES["file"]["type"]) && isset($_POST['shop_img_idx']))
{	
	$idx = $_POST['shop_img_idx'];

	$temporary = explode(".", $_FILES["file"]["name"]);
	$file_extension = end($temporary);

	$allowedTypes = array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF, IMAGETYPE_BMP);
	$detectedType = exif_imagetype($_FILES['file']['tmp_name']);
	if(in_array($detectedType, $allowedTypes))
	{

		if ($_FILES["file"]["error"] > 0)
		{
			echo "Return Code: " . $_FILES["file"]["error"] . "<br/><br/>";
		}
		else
		{	
			$sourcePath = $_FILES['file']['tmp_name']; // Storing source path of the file in a variable]

			if($_FILES["file"]["size"] >= 1000000){
				$sourcePath = ResizeImage($sourcePath, $file_extension);
			}
			if($sourcePath){
				//------upload/가맹점인덱스_날짜.확장자----------------------------------
				$targetPath = "upload/".$idx."_".date("YmdHis").".".$file_extension; 

				//-----파일 업로드-------------------------------------------------------
				$moved = move_uploaded_file($sourcePath, $targetPath) ; // Moving Uploaded file

				if( $moved ) {
					echo "이미지 등록에 성공하였습니다.";      
				} else {
					echo "Not uploaded because of error #".$_FILES["file"]["error"];
				}
				registerShopImage($connect, $targetPath, $idx);
			}else{
				echo "이미지 등록에 실패하였습니다.";   
			}
			
		}
	}
	else {
		echo "업로드 가능한 확장자가 아닙니다. 사진파일만 업로드 해주세요.";
	}
}


if(isset($_FILES["file_menu"]["type"]) && isset($_POST['shop_menu_idx']) && !isset($_POST['shop_menu_edit']))
{
	//echo " / menu / ";

	$shop_idx = $_POST['shop_menu_idx'];

	$temporary = explode(".", $_FILES["file_menu"]["name"]);
	$file_extension = end($temporary);

	$allowedTypes = array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF, IMAGETYPE_BMP);
	$detectedType = exif_imagetype($_FILES['file_menu']['tmp_name']);
	
	if(in_array($detectedType, $allowedTypes))
	{
		if ($_FILES["file_menu"]["error"] > 0)
		{
			echo "Return Code: " . $_FILES["file_menu"]["error"] . "<br/><br/>";
		}
		else
		{	
			$sourcePath = $_FILES["file_menu"]['tmp_name']; // Storing source path of the file in a variable

			if($_FILES["file_menu"]["size"] >= 1000000){
				$sourcePath = ResizeImage($sourcePath, $file_extension);
			}
			if($sourcePath){
				//------upload/가맹점인덱스_m_날짜.확장자----------------------------------
				$targetPath = "upload/".$shop_idx."_m_".date("YmdHis").".".$file_extension;  
				// Target path where file is to be stored
			
				$moved = move_uploaded_file($sourcePath,$targetPath) ; // Moving Uploaded file
				
				if( $moved ) {
					echo "메뉴 등록에 성공하였습니다.";      
				} else {
					echo "Not uploaded because of error menu #".$_FILES["file_menu"]["error"];
				}

				registerShopMenu($connect, $targetPath, $shop_idx);
			}else {
				echo "메뉴 등록에 실패하였습니다.";   
			}
		}
	}else{
		if(!in_array($detectedType, $allowedTypes)) {
			echo "이미지가 올바른 파일형식이 아닙니다. 메뉴정보만 등록되었습니다.";
		}else echo "메뉴 등록에 성공하였습니다.";
		// 이미지 없이 메뉴등록
		$targetPath = "";
		registerShopMenu($connect, $targetPath, $shop_idx);
	}
}

if(isset($_FILES["file_menu"]["type"]) && isset($_POST['shop_menu_idx']) && isset($_POST['shop_menu_edit']))
{
	$shop_idx = $_POST['shop_menu_idx'];

	$temporary = explode(".", $_FILES["file_menu"]["name"]);
	$file_extension = end($temporary);

	$allowedTypes = array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF, IMAGETYPE_BMP);
	$detectedType = exif_imagetype($_FILES['file_menu']['tmp_name']);
	
	if(in_array($detectedType, $allowedTypes))
	{

		if ($_FILES["file_menu"]["error"] > 0)
		{
			echo "Return Code: " . $_FILES["file_menu"]["error"] . "<br/><br/>";
		}
		else
		{	
			$sourcePath = $_FILES["file_menu"]['tmp_name']; // Storing source path of the file in a variable
			
			if($_FILES["file_menu"]["size"] >= 1000000){
				$sourcePath = ResizeImage($sourcePath, $file_extension);
			}
			if($sourcePath){
				//------upload/가맹점인덱스_m_날짜.확장자----------------------------------
				$targetPath = "upload/".$shop_idx."_m_".date("YmdHis").".".$file_extension;  
				// Target path where file is to be stored
			
				$moved = move_uploaded_file($sourcePath,$targetPath) ; // Moving Uploaded file
				
				if( $moved ) {
					echo "메뉴 수정에 성공하였습니다.";      
				} else {
					echo "Not uploaded because of error menu #".$_FILES["file_menu"]["error"];
				}

				registerShopMenu($connect, $targetPath, $shop_idx);
			}else {
				echo "메뉴 수정에 실패하였습니다.";   
			}
		}
	}else{
		if(!in_array($detectedType, $allowedTypes)) {
			echo "이미지가 올바른 파일형식이 아닙니다. 메뉴정보만 수정되었습니다.";
		}else echo "메뉴가 수정되었습니다.";
		registerShopMenu($connect, $targetPath, $shop_idx);
	}
}

function registerShopImage($connect, $targetPath, $idx){
	$qry = "SELECT photo_url 
			FROM shop 
			WHERE idx = '$idx'";

	if ($result = mysqli_query($connect, $qry)) {
		//printf("Select returned %d rows.\n", mysqli_num_rows($result));
		$value = mysqli_fetch_object($result);
		

		$unlink_name = $value->photo_url;

		if(isset($unlink_name) && !empty($unlink_name)){
			if( strcmp( $unlink_name, 'upload/nostorephoto.jpg' ) == 0 ){
				
			}else if( strcmp( $unlink_name, 'upload/nostorephoto.jpg' ) != 0 ){
				
				unlink($unlink_name);
			}
		}else{
			//No file
		}
	}

	$qry2 = "UPDATE  `jnfc`.`shop` 
			SET `photo_url` = '$targetPath'
			WHERE  `shop`.`idx` = '$idx'";

	if ($connect->query($qry2) === TRUE) {
		//echo "success";
	}else{
		echo "실패";
	}
}

function registerShopMenu($connect, $targetPath, $shop_idx){

	//echo " / menu / ";

	
	
	$menu_idx = $_POST['menu_idx'];
	$type = $_POST['menu_type'];
	$menu_name = $_POST['menu_name'];
	$info = $_POST['info'];
	
	if(isset($_POST['price']) && !empty($_POST['price'])) $price = $_POST['price'];
	else if(isset($_POST['price_s']) && !empty($_POST['price_s'])) $price = $_POST['price_s'];
	$price_m = $_POST['price_m'];
	$price_l = $_POST['price_l'];
	$photo_url = $targetPath;

	if(isset($menu_idx) && !empty($menu_idx)){
		
		//upload폴더에서 사진 삭제 후 정보수정
		if (!empty($targetPath) && isset($targetPath)){
			$qry = "SELECT photo_url, shop_idx, menu_name
				FROM menu
				WHERE idx = '$menu_idx'";

			if ($result = mysqli_query($connect, $qry)) {
				//printf("Select returned %d rows.\n", mysqli_num_rows($result));
				$value = mysqli_fetch_object($result);
				
				$unlink_name = $value->photo_url;

				if(isset($unlink_name) && !empty($unlink_name)){
					unlink($unlink_name);
				}else{
					//No file
				}
			}

			$qry2 = "UPDATE  `jnfc`.`menu` 
					SET 
					`photo_url` = '$targetPath',
					`type` = '$type',
					`menu_name` = '$menu_name',
					`info` = '$info',
					`price` = '$price',
					`price_m` = '$price_m',
					`price_l` = '$price_l'
					WHERE  `idx` = '$menu_idx'";
		
		// 사진을 제외한 나머지 정보 수정
		}else if (empty($targetPath) && !isset($targetPath)){
			$qry2 = "UPDATE  `jnfc`.`menu` 
					SET 
					`type` = '$type',
					`menu_name` = '$menu_name',
					`info` = '$info',
					`price` = '$price',
					`price_m` = '$price_m',
					`price_l` = '$price_l'
					WHERE  `idx` = '$menu_idx'";
		}

		if ($connect->query($qry2) === TRUE) {
			//echo "<br/>메뉴 정보 업데이트 완료";
		}else{
			echo "업데이트 실패";
		}

	}
	else if (!isset($menu_idx) && empty($menu_idx)){

		$qry = "INSERT INTO  `jnfc`.`menu` (
				`idx`,
				`shop_idx` ,
				`type`,
				`menu_name`,
				`info`,
				`price`,
				`price_m`,
				`price_l`,
				`photo_url`)
				VALUES (
				NULL, '$shop_idx', '$type', '$menu_name', '$info', '$price', '$price_m', '$price_l', '$photo_url')";

		if ($connect->query($qry) === TRUE) {
			//echo "<br/>메뉴 정보 생성 완료";
		}else{
			echo "생성 실패";
		}
	}
}

function ResizeImage($sourcePath, $file_extension){

	$error = false;

	if(@is_array(getimagesize($sourcePath))) {
		$target_width = 800; 
		$target_height = 600; 

		if (ob_get_level() == 0) ob_start(); 
		if ($handle = opendir('/tmp/')) { 
			while (false !== ($file = readdir($handle))) { 
				if ($file != "." && $file != "..") { 

					if($file_extension == "jpg" || $file_extension == "jpeg" || $file_extension == "JPG"){ 
						$tmp_image=imagecreatefromjpeg($sourcePath); 
					} 

					if($file_extension == "png" || $file_extension == "PNG" ) { 
						$tmp_image=imagecreatefrompng($sourcePath); 
					} 

					if($file_extension == "gif" || $file_extension == "GIF" ) { 
						$tmp_image=imagecreatefromgif($sourcePath); 
					} 

					if($file_extension == "bmp" || $file_extension == "BMP" ) { 
						$tmp_image=imagecreatefromgif($sourcePath); 
					} 

					$width = imagesx($tmp_image); 
					$height = imagesy($tmp_image); 

					//calculate the image ratio 
					$imgratio = ($width / $height); 

					if(isset($imgratio) && $imgratio != 0){
						
						if ($imgratio>1) { 
						  $new_width = $target_width; 
						  $new_height = ($target_width / $imgratio); 
						} else { 
						  $new_height = $target_height; 
						  $new_width = ($target_height * $imgratio); 
						} 

						$new_image = imagecreatetruecolor($new_width,$new_height); 
						ImageCopyResized($new_image, $tmp_image,0,0,0,0, $new_width, $new_height, $width, $height); 
						//Grab new image 
						imagejpeg($new_image, $sourcePath); 
						$image_buffer = ob_get_contents(); 
						ImageDestroy($new_image); 
						ImageDestroy($tmp_image); 
						
						ob_flush(); 
						flush(); 
						
					}else{
						echo "올바른 이미지 파일 형식이 아닙니다. ";
						$error = true;
					}
				}// end if ($file != "." && $file != "..") 
			} // end while
			closedir($handle); 
			ob_end_flush(); 
		} // end if ($handle = opendir('tmp/'))
		
	}else{
		echo "올바른 이미지 파일 형식이 아닙니다. ";
		$error = true;
	}

	if(!$error) return $sourcePath;
	else return false;
	
}

?>