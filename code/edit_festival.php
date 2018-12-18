<?php session_start(); ?>
<?php 
	header("Content-Type: text/html;charset=UTF-8");
	include_once $_SERVER[DOCUMENT_ROOT].'/m/header.php';
	include_once $_SERVER[DOCUMENT_ROOT].'/m/code/govern_exec.php';
	
	$g_idx = $_GET['idx'];
	$festival_list = getGovern_festival_list($connect, $g_idx);
?>
<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="<?=$path?>/js/governFunction.js?ver=1"></script>

<h2 class="title_sub"> 축제 / 행사 </h2>
<div align="center">
	<input type="hidden" id="g_idx" value="<?=$g_idx?>"/>
<?php  
	while($festival = mysqli_fetch_assoc($festival_list)){ ?>
	<div class="box_festival">
    	<div class="box_remove">
        	<img src="../images/icon_x2.png">
        </div>
        <input type='hidden' class='f_idx' value='<?=$festival['f_idx']?>'/>
    	<span><?=$festival['f_title']?></span>
    </div>
    
<?	} ?>
	<div class="box_add_festival">
    	<div class="icon_add"><img src="../images/add.png"/></div>
    </div>
</div>
