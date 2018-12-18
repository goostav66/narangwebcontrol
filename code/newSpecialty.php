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
<title>특산물 추가</title>
<style>
	.s_icon_img{ width: 150px; height: 150px; }
	.s_icon_file{ display: none; }
	
	.section_s_item{ width: 400px; overflow: auto; margin-bottom: 10px; }
	.section_s_item>div{ float: left; }
	.s_item_header{ width: 120px; margin: 6px 0; }
	.s_item_data{ width: 260px; margin-bottom: 6px; }
	.s_item_img{ width: 120px; height: 100px; }
	.s_item_file{ display: none; }
	.s_item_btn{ margin: 0 0 6px 6px; }
	.s_item_data input[type='text']{ padding: 6px; }
	.del_item_btn{ margin-top: 6px; }
</style>
</head>
	
<body>
	<h2 class="title_sub"> 특산물 추가 </h2>
	
    <form id= 'form_specialty_add' action='govern_exec.php' method="post" enctype="multipart/form-data">
    <div class="pop_btn_set">
        <input type="button" name="btn_rgst" value="저장 / 수정" class="edit_btn save_btn">
        <input type="button" name="btn_rgst" value="닫기" class="edit_btn close_btn">
    </div>  
    <div id="newSpecialty_wrap">
	
    	<input type='hidden' name='g_idx_spc' value='<?=$g_idx?>'>
    	<table class="list_data">
        	<tr class="pop_list_item">
            	<th>품목</th><td><input type="text" name="s_category"></td>
            </tr>
            <tr class="pop_list_item">
            	<th>사진</th>
                <td>
                	<img class="s_icon_img" src="../images/noimage.png">
                    <input type="file" name="s_icon_image" class="s_icon_file">
                    <button type="button" class="edit_btn s_icon_btn">사진 추가/변경</button>
                </td>
            </tr>
  			<tr>
            	<th>항목</th>
                <td>
                	<div class="s_appended_item"></div>
                	<button type="button" class="edit_btn s_category_btn">새 항목 추가</button>
                </td>
            </tr>
           
        </table>
       
    </div>
    </form>
</body>
</html>
