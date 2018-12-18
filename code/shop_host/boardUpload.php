<?php

$upload_dir = $_SERVER['DOCUMENT_ROOT'] . "/m/code/shop_host/board";

ini_set('memory_limit','-1');

if (is_dir($upload_dir) && is_writable($upload_dir)) {
  //writable
} else {
    echo 'Upload directory is not writable, or does not exist.';
}

if(isset($_FILES["file_name"]["type"]))
{
	$tmp = $_FILES['file_name']['name'];
	$ext = pathinfo($tmp, PATHINFO_EXTENSION);

	$allowedTypes = array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF, IMAGETYPE_BMP);
	$detectedType = exif_imagetype($_FILES['file_name']['tmp_name']);
	if(in_array($detectedType, $allowedTypes))
	{
		if ($_FILES["file_name"]["error"] > 0)
		{
			echo "Return Code: " . $_FILES["file_name"]["error"] . "<br/><br/>";
		}
		else
		{
			$sourcePath = $_FILES['file_name']['tmp_name']; // Storing source path of the file in a variable]

			if($sourcePath){

				$targetPath = "board/".date("YmdHis").".".$ext;

				//-----파일 업로드-------------------------------------------------------
				$moved = move_uploaded_file($sourcePath, $targetPath) ; // Moving Uploaded file

				if( $moved ) {
					echo "http://103.60.124.17/m/code/shop_host/".$targetPath;      
				} else {
					echo "Not uploaded because of error #".$_FILES["file_name"]["error"];
				}
			}else{
				echo "이미지 등록에 실패하였습니다.";
			}

		}
	}
	else {
		echo "업로드 가능한 확장자가 아닙니다. 사진파일만 업로드 해주세요.";
	}
}
?>
