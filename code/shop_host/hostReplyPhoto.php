<?php session_start(); ?>
<?php 
header("Content-Type: text/html;charset=UTF-8");
include_once $_SERVER[DOCUMENT_ROOT].'/m/header.php';
include_once $_SERVER[DOCUMENT_ROOT].'/m/code/shop_exec.php';
?>
<?php
	$idx = $_GET['idx'];
	$result = viewReplyPhoto($connect, $idx);
 ?>
<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="<?=$path?>/js/shopFunction.js?ver=1"></script>
<style>
	img{ width: 100vw; }
</style>
<?php
	while($row = mysqli_fetch_assoc($result)){ ?>
	
    <img src="<?=$row['photo_url']?>">
<? 	}
?>