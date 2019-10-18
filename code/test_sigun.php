<?php session_start(); ?>
<?php 
header("Content-Type: text/html;charset=UTF-8");
include_once $_SERVER[DOCUMENT_ROOT].'/m/nav.php';
include_once $_SERVER[DOCUMENT_ROOT].'/m/code/govern_exec.php';

?>

<?php
	//	$id = $_SESSION['id'];
	$id='nfcyd';
	$govern_infor = getGovern($connect, $id);
?>
<style>
	.wrap{ margin: auto; width: 90%; }
	.govern_main { margin: 10px; color: #fff; font-weight: 700 }
	.govern_main div { border-radius: 3px; } 
	.menu_title { padding: 6px 12px; background-color: #759aaf; width: fit-content; font-size: 1.1rem; font-weight: bold; margin-bottom: 6px; cursor:default; }
	
	.govern_setting, .govern_festival, .govern_specialty, .menu_edit { display: inline-block; margin: 4px 8px 20px 0; padding: 4px 8px; background-color: #9cb6c5; }
	.govern_festival, .govern_specialty { cursor: grab; }
	.menu_edit{ background-color: #e03b2a !important; }
</style>

<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="<?=$path?>/js/governFunction.js"></script>

<div class="wrap">
	<iframe class="govern_iframe" src="http://m.nfczone.co.kr/gun/index.jsp?p=yd" scrolling="no"></iframe>
	<div class="govern_main">
	<?php 
	if($govern_infor){
		$g_idx = $govern_infor['idx'];
		$url = $govern_infor['url'];
		
		$menu_list = getGovern_menu_list($connect, $g_idx);
		
		$festival_list = getGovern_festival_list($connect, $g_idx);
		$specialty_category = getGovern_specialty_category($connect, $g_idx);
	?>
		<input type="hidden" id="g_idx" value="<?=$g_idx?>"/>
		<div class="menu_title">설정</div>	
		<div>
	        <div class="govern_setting" id="mod_logo_image">로고 이미지 변경</div> <div class="govern_setting" id="mod_back_image">배경화면 변경</div>
	    </div>
	<?php 
		if($menu_list['g_announce']){?>
		<div>
		    <div>소식</div>
		</div>
	<?	}?>
	    
	<?php 
		if($menu_list['g_donation']){?>
		<div>
		    <div>나눔</div>
		</div>
	<?	}?>
	 
	<?php 
		if($menu_list['g_tour']){?>
		<div>
		    <div>여행정보</div>
	        
		</div>
	<?	}?> 

	<?php 
		if($menu_list['g_festival']){?>
		<div class="menu_title">축제/행사</div>
	    <div>
		    <? while($festival = mysqli_fetch_assoc($festival_list)){?>
	    	<div class="govern_festival" id="<?=$festival['f_idx']?>_fidx"><?=$festival['f_title']?></div>
	        <? } ?>
	        <div class="menu_edit" id="edit_festival">추가</div>
		</div>
	<?	}?>     

	<?php 
		if($menu_list['g_specialty']){?>
		<div class="menu_title">특산물</div>
	    <div>
			<? while($s_category = mysqli_fetch_assoc($specialty_category)){?>
	        <div class="govern_specialty" id="<?=$s_category['s_idx']?>_sidx"><?=$s_category['s_category']?></div>	
			<? } ?>	   
	        <div class="menu_edit" id="edit_specialty">추가</div>
		</div>
	<?	}?> 
	    
	<?php 
		if($menu_list['g_amenity']){?>
		<div>
		    <div>편의시설</div>
		</div>
	<?	}?> 
	  
	<? } ?>
	</div>
</div>