<?php session_start(); ?>
<?php
include_once $_SERVER[DOCUMENT_ROOT].'/m/data/DB_connect.php';

	/* check connection */
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}


	
	function ViewList($connect){

		$qry = "SELECT * FROM user ORDER BY (
				CASE auth
				WHEN 'manager' THEN 1
				WHEN 'branch' THEN 2
				WHEN 'agent' THEN 3
				ELSE 4
				END
			),
			NAME";
		
		/* Select queries return a resultset */
		if ($result = mysqli_query($connect, $qry)) {
			//printf("Select returned %d rows.\n", mysqli_num_rows($result));
		}else{
			 echo("Error description ViewList: " . mysqli_error($connect));
		}

		return $result;
	}

?>