<?php session_start(); ?>

<?php
include_once $_SERVER[DOCUMENT_ROOT].'/m/data/DB_connect.php';

	//게시판 - 등록
	if(isset($_POST['new_board_url']) && !empty($_POST['new_board_url']) ){
		$url = $_POST['new_board_url'];
		insertHostBoard($connect, $url);
	}

	//게시판 - 수정
	if(isset($_POST['host_board_idx']) && !empty($_POST['host_board_idx']) ){
		$idx = $_POST['host_board_idx'];
		editHostBoard($connect, $idx);
	}

	//게시판 - 삭제
	if(isset($_POST['del_board_idx']) && !empty($_POST['del_board_idx']) ){
		$idx = $_POST['del_board_idx'];
		delHostBoard($connect, $idx);
	}

	//이벤트 - 등록
	if(isset($_POST['host_popup_url']) && !empty($_POST['host_popup_url']) ){
		$url = $_POST['host_popup_url'];
		insertEvent($connect, $url);
	}

	//이벤트 - 수정
	if(isset($_POST['host_popup_idx']) && !empty($_POST['host_popup_idx']) ){
		$idx = $_POST['host_popup_idx'];
		modifyEvent($connect, $idx);
	}else{
		//echo fail;
	}

	//이벤트 - 삭제
	if(isset($_POST['popup_delete_idx']) && !empty($_POST['popup_delete_idx']) ){
		$idx = $_POST['popup_delete_idx'];
		deleteEvent($connect, $idx);
	}else{
		//echo fail;
	}
	
	//손님이야기 - 삭제
	if( isset($_POST['reply_delete_idx']) && !empty($_POST['reply_delete_idx']) ){
		$reply_idx = $_POST['reply_delete_idx'];
		deleteReply($connect, $reply_idx);
	}
	
	//검색어 - 등록
	if( isset($_POST['tag_shop_idx']) && !empty($_POST['tag_shop_idx']) ){
		$shop_idx = $_POST['tag_shop_idx'];
		insertKeyword($connect, $shop_idx);
	} 
	
	//검색어 - 삭제
	if( isset($_POST['tag_word_idx']) && !empty($_POST['tag_word_idx']) ){
		$idx = (int)$_POST['tag_word_idx'];
		deleteKeyword($connect, $idx);
	} 

  //----------------------------------------------게시물 목록 가져오기
  function viewBoardList($connect, $url){
    $qry = "SELECT * FROM shop_board WHERE url = '$url' ORDER BY regdate DESC ";

    if($result = mysqli_query($connect, $qry)){
      //printf("Select returned %d rows.\n", mysqli_num_rows($result));
    }else{
      echo("Error description: " . mysqli_error($connect));
    }
    return $result;
  }

  //----------------------------------------------게시물 가져오기
	function viewBoard($connect, $idx){
		$qry = "SELECT * FROM shop_board WHERE idx = '$idx' ";

		$result = mysqli_query($connect, $qry);
		return mysqli_fetch_assoc($result);
	}
  //----------------------------------------- 게시물 등록
 	function insertHostBoard($connect, $url){
    $content = $_POST['content'];

    $qry = "INSERT INTO shop_board (url, content, regdate) VALUES ('$url', '$content', NOW())";

    if($connect->query($qry) === TRUE){
      echo "success";
    }else {
      echo "Error qry: " . $qry . "<br>" . $connect->error;
    }
  }

  //-----------------------------------------게시물 수정
  function editHostBoard($connect, $idx){
    $content = $_POST['content'];

    $qry = "UPDATE shop_board SET content = '$content' WHERE idx = '$idx'";

    if($connect->query($qry) === TRUE){
      echo "success";
    }else {
      echo "Error qry: " . $qry . "<br>" . $connect->error;
    }
  }

  //------------------------------------------게시물 삭제
  function delHostBoard($connect, $idx){
    $qry = "DELETE FROM shop_board WHERE idx = '$idx'";

    if($connect->query($qry) === TRUE){
      echo "success";
    }else{
      echo "Error qry: " . $qry . "<br>" . $connect->error;
    }
  }

  //----------------------------------------------이벤트 목록 가져오기
  function viewEventList($connect, $url){
    $qry = "SELECT * FROM shop_event WHERE url = '$url' ORDER BY date_start DESC ";

   	$result = mysqli_query($connect, $qry);
	
    return $result;
  }

  //----------------------------------------------이벤트 가져오기
  function getEvent($connect, $idx){
    $qry = "SELECT * FROM shop_event WHERE idx = '$idx' ";

    $result = mysqli_query($connect, $qry); 
	
    return mysqli_fetch_assoc($result);
  }

  //----------------------------------------------이벤트 등록
  function insertEvent($connect, $url){
    $background_img = $_POST['background_img'];
    $message = $_POST['message'];
    $start_date = $_POST['date_start'];
    $end_date = $_POST['date_end'];

    $qry = "INSERT INTO shop_event (url, background_img, message, date_start, date_end, isFloating)
        VALUES ('$url', '$background_img', '$message', '$date_start', '$date_end', 1)";
	mysqli_query($connect, $qry);
	
  }

  //----------------------------------------------이벤트 수정
  function modifyEvent($connect, $idx){
    $message = $_POST['message'];
    $background_img = $_POST['background_img'];
    $start_date = $_POST['date_start'];
    $end_date = $_POST['date_end'];
    $isChecked = $_POST['isFloating'];

    $qry = "UPDATE shop_event SET message = '$message', background_img = '$background_img',
        date_start = '$date_start', date_end = '$date_end', isFloating = '$isFloating' WHERE idx = '$idx'";

    if ($connect->query($qry) === TRUE) {
      echo "success";
    } else {
      echo "Error qry: " . $qry . "<br>" . $connect->error;
    }
  }

  //----------------------------------------------이벤트 삭제
  function deleteEvent($connect, $idx){
    $qry = "DELETE FROM shop_event WHERE idx = '$idx'";

    if ($connect->query($qry) === TRUE){
      echo "success";
    } else{
      echo "Error qry: " . $qry . "<br>" . $connect->error;
    }

  }
	//----------------------------------------------번개할인 가져오기
	function viewHostSale($connect, $url){
		$qry = "SELECT * FROM shop_sale WHERE url = '$url'";
		$result = mysqli_query($connect, $qry);
		
		return mysqli_fetch_assoc($result);
	}
	
	//----------------------------------------------리뷰 리스트 가져오기
	function viewReplyList($connect, $url){
		$sql = "SELECT * FROM reply WHERE url = '$url' ORDER BY credate DESC";
		$result = mysqli_query($connect, $sql);

		return $result;
	}
	
	//----------------------------------------------리뷰 사진 가져오기
	function viewReplyPhoto($connect, $reply_idx){
		$sql = "SELECT * FROM reply_photo WHERE reply_idx = '$reply_idx' ORDER BY idx";
		$result = mysqli_query($connect, $sql);
		return $result;
	}
	
	//----------------------------------------------리뷰 삭제 : 1)reply에서 삭제, 2)파일시스템 삭제, 3) reply_photo에서 삭제
	function deleteReply($connect, $reply_idx){
		$sql = "DELETE FROM reply WHERE idx = $reply_idx";
		
		if( $connect->query($sql) === TRUE ){
			$sql = "DELETE FROM reply_photo WHERE reply_idx = '$reply_idx'";
			mysqli_query($connect, $sql);
		}
	}	

	//----------------------------------------------검색어 리스트 가져오기
	function viewKeywordList($connect, $idx){
		$qry = "SELECT * FROM shop_tag WHERE shop_idx = '$idx'";
		$result = mysqli_query($connect, $qry);
		
		return $result;	
	}
	//----------------------------------------------검색어 등록
	function insertKeyword($connect, $shop_idx){
		$stmt = $connect->prepare("INSERT INTO shop_tag (shop_idx, tag) VALUES (?, ?)");
		$stmt->bind_param("is", $shop_idx, $_POST['keyword']);
		$stmt->execute();
		
		$last_id = mysqli_insert_id($connect);
		echo $last_id;
		
		$stmt->close();
		$connect->close();
	}
	
	//----------------------------------------------검색어 삭제
	function deleteKeyword($connect, $idx){
		$sql = "DELETE FROM shop_tag WHERE idx = '$idx'";
		mysqli_query($connect, $sql);
	}
?>
