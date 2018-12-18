<?php 
if(session_id() == '') {
	session_start();
}
?>
<?php 
header("Content-Type: text/html;charset=UTF-8");
include_once $_SERVER['DOCUMENT_ROOT'].'/m/nav.php';
?>
<div align='center'>
	<img src="images/main_bg.jpg" style="width:70%; max-width:1768px; min-width:900px" alt="" />
</div>
</body>
<style>
body {background:#fff}
</style>
</html>
