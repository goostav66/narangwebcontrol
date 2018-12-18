<?php session_start(); ?>
<?php 
header("Content-Type: text/html;charset=UTF-8");
include_once $_SERVER['DOCUMENT_ROOT'].'/m/data/DB_connect.php';

$id = $_POST['id'];
$pw = $_POST['pw'];
$qry = "SELECT id, name, auth FROM user WHERE id='$id' AND pw='$pw'";

$res = mysqli_query($connect, $qry);
$num_row = mysqli_num_rows($res);
$row = mysqli_fetch_assoc($res);

if( $num_row == 1 ) {
	
	$_SESSION['id'] = $row['id'];
	$_SESSION['name'] = $row['name'];
	$_SESSION['auth'] = $row['auth'];
	echo 'success,'.$row['name'];
}else {
	//echo 'fail';
}

	if(isset($_POST['logout']) && !empty($_POST['logout'])){
		session_destroy( );
		mysqli_close($connect);
		echo "/m/login_index.php"; 
	}else{
		//echo "fail";
	}

?>
