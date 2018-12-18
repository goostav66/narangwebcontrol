<?php session_start(); ?>
<?php 
	header("Content-Type: text/html;charset=UTF-8");
	include_once $_SERVER[DOCUMENT_ROOT].'/m/header.php';
	include_once $_SERVER[DOCUMENT_ROOT].'/m/code/govern_exec.php';
	
	$f_idx = $_GET['f_idx'];
	$festival = getGovern_festival_infor($connect, $f_idx);
	$image_list = getGovern_festival_images($connect, $f_idx);
?>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="<?=$path?>/js/governFunction.js?ver=1"></script>
<title>축제/행사 수정</title>
<style>
	.modifyImage img{  }
</style>
</head>
<body>
	<h2 class="title_sub"> 축제/행사 수정 </h2>
   	
	<form id= 'form_festival_mod' action='govern_exec.php' method="post" enctype="multipart/form-data">
		<div class="pop_btn_set">
            <input type="submit" name="btn_rgst" value="저장 / 수정" class="edit_btn modify_btn">
            <input type="submit" name="btn_rgst" value="삭제" class="edit_btn remove_btn">
            <input type="button" name="btn_rgst" value="닫기" class="edit_btn close_btn">
        </div>                
        <div id="newFestival_wrap">
        	<input type="hidden" name="f_idx_fes" value="<?=$f_idx?>">
            <table class="list_data">
                <tr class="pop_list_item">
                    <th>제목</th><td><input type="text" name="f_title" value="<?=$festival['f_title']?>"></td>
                </tr>
                <tr class="pop_list_item">
                    <th>사진</th>
                    <td>
                        <div class="modifyImage">
                        <? while($row = mysqli_fetch_assoc($image_list)){ ?>
                        	<div class='cell_img' id="<?=$row['fi_idx'] ?>_fiidx"><div class='box_remove'><img src='http://na.nfczone.co.kr/m/images/icon_x2.png'></div>
                            <img style='width: 150px; height: 93px; float: left;' src='<?=$row['f_image_url']?>'></div>
                        <? } ?>                    
                        </div>
                        <div class="inputFile"></div>
                        <div class="inputImage"></div>
                        <button type="button" class="edit_btn" id="add_file">사진 추가</button>
                        <input type='hidden' name='arr_file_idx' value=''>
                        <input type='hidden' name='arr_file_name' value=''>
                    </td>
                </tr>
                <tr class="pop_list_item">
                    <th>부제목</th><td><input type="text" name="f_subtitle" value="<?=$festival['f_subtitle']?>"></td>
                </tr>
                <tr class="pop_list_item">
                    <th>기간</th><td><input type="text" name="f_period" value="<?=$festival['f_period']?>"></td>
                </tr>
                <tr class="pop_list_item">
                    <th>내용</th><td style="border-right: 0px;"><div id="editor"><?=$festival['f_content']?></div></td>
                    <input type='hidden' name='f_content' value=''>
                </tr>
                <tr class="pop_list_item">
                    <th>위치</th><td><input type="text" name="f_location" value="<?=$festival['f_location']?>"></td>
                </tr>
               
            </table>    
        </div>
        
    </form>
	<script src="<?=$path?>/js/quill.min.js?ver=1"></script>
    <link href="<?=$path?>/js/quill.snow.css?ver=1" rel="stylesheet">
    
    <script>
        $(document).ready(function(){
            var toolbarOptions = [
                ['bold', 'italic', 'underline', 'strike'], 
                [{ 'color': [] }, { 'background': [] }]         // dropdown with defaults from theme
        
            ];
            var quill = new Quill('#editor', {
                modules:{ toolbar: toolbarOptions },
                theme: 'snow'
            });
        });
    </script>
</body>
</html>