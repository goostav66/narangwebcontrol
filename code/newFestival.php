<?php session_start(); ?>
<?php 
	header("Content-Type: text/html;charset=UTF-8");
	include_once $_SERVER[DOCUMENT_ROOT].'/m/header.php';
	include_once $_SERVER[DOCUMENT_ROOT].'/m/code/govern_exec.php';
	
	$g_idx = $_GET['idx'];
?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="<?=$path?>/js/governFunction.js?ver=1"></script>
<title>축제/행사 추가</title>

</head>
	
<body>
	<h2 class="title_sub"> 축제/행사 추가 </h2>
	
    <form id= 'form_festival_add' action='govern_exec.php' method="post" enctype="multipart/form-data">
    <div class="pop_btn_set">
            <input type="button" name="btn_rgst" value="저장 / 수정" class="edit_btn save_btn">
            <input type="button" name="btn_rgst" value="닫기" class="edit_btn close_btn">
    </div>  
    <div id="newFestival_wrap">
	
    	<input type='hidden' name='g_idx_fes' value='<?=$g_idx?>'>
    	<table class="list_data">
        	<tr class="pop_list_item">
            	<th>제목</th><td><input type="text" name="f_title"></td>
            </tr>
            <tr class="pop_list_item">
            	<th>사진</th>
                <td>
                	<div class="inputFile"></div>
                    <div class="inputImage"></div>
                	<button type="button" class="edit_btn" id="add_file">사진 추가</button>
                    <input type='hidden' name='arr_file_name' value=''>
                </td>
            </tr>
            <tr class="pop_list_item">
            	<th>부제목</th><td><input type="text" name="f_subtitle"></td>
            </tr>
            <tr class="pop_list_item">
            	<th>기간</th><td><input type="text" name="f_period"></td>
            </tr>
            <tr class="pop_list_item">
            	<th>내용</th><td style="border-right: 0px;"><div id="editor"></div></td>
                <input type='hidden' name='f_content' value=''>
            </tr>
            <tr class="pop_list_item">
            	<th>위치</th><td><input type="text" name="f_location"></td>
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
