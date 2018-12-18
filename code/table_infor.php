<?php session_start(); ?>
<?php 
header("Content-Type: text/html;charset=UTF-8");
include_once $_SERVER[DOCUMENT_ROOT].'/m/header.php';
include_once $_SERVER[DOCUMENT_ROOT].'/m/code/shop_exec.php';
?>

<?php
	$idx = $_GET['idx'];
	
	$infor = ViewTableInfor($connect, $idx);
?>
<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="<?=$path?>/js/shopFunction.js?ver=1"></script>

<? 
	while($row = mysqli_fetch_assoc($infor)){?>
        <div>
        <?=$row['order_table']?>번 테이블<br>
        메뉴 :<?= GetMenuName($connect, $row['order_menu'])?><br>
        수량 : <?=$row['order_quant']?><br>
        가격 : <?=$row['order_total']?><br>
        일시 : <?=$row['order_date']?> <br><br>
        </div>
<?	}
?>
