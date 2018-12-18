<?php
header('Content-Type: text/html;charset=utf-8');
include_once $_SERVER[DOCUMENT_ROOT].'/m/data/DB_connect.php';

$data_dir = $_SERVER['DOCUMENT_ROOT'] . "/m/code/data";

//파일 업로드를 위해 메모리처리량 무제한으로 설정
ini_set('memory_limit','-1');

error_reporting(E_ALL);
ini_set("display_errors", 1);

function utf2euc($str) { return iconv("UTF-8","cp949//IGNORE", $str); }
function is_ie() {
	if(!isset($_SERVER['HTTP_USER_AGENT']))return false;
	if(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false) return true; // IE8
	if(strpos($_SERVER['HTTP_USER_AGENT'], 'Windows NT 6.1') !== false) return true; // IE11
	return false;
}

if (is_dir($data_dir) && is_writable($data_dir)) {
	
	$allowedTypes = array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF, IMAGETYPE_BMP);
	//$detectedType = exif_imagetype($_FILES['file']['tmp_name']);

	if (ob_get_level() == 0) ob_start(); 
	if ($handle = opendir($data_dir)) { 
		while (false !== ($file = readdir($handle))) { 
			if ($file != "." && $file != "..") { 
				
				//echo '/', iconv("UTF-8", "ISO-8859-1//IGNORE", $file), PHP_EOL; 
				//echo '/', iconv("UTF-8", "euc-kr", $file), PHP_EOL;
				echo 'success/', iconv("euc-kr", "UTF-8", $file), PHP_EOL;
				//echo '/ ', iconv("UTF-8","cp949//IGNORE", $file), PHP_EOL;
				echo '<br/>';

				
			}
		}
	}
	
	
} else {
    echo 'Data directory is not writable, or does not exist.';
	echo '<br/> Data directory : '.$data_dir;
}


/*
$sourcePath = $_FILES['file']['tmp_name']; // Storing source path of the file in a variable]

$sourcePath = loadImage($sourcePath, $file_extension);


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
	
	
	
function loadImage($sourcePath, $file_extension){

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
*/

?>