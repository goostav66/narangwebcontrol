/* shopHostFunction.js
  주인장이야기 관리
*/
$(document).ready(function(){
  //공통 - 창 닫기
  $(document).on('click', '#closeButton', function () {
    window.close();
  });

  //게시판 - 텍스트 에디터 팝업(등록, 수정 클릭시)
  $(document).on('click', '.board_textEditor_btn', function(){
    var idx = $(this).siblings("input[name='idx']").val();
    if(idx == null || idx == 0)
      var url = "hostBoard.php?p="+getParameters("p")+"&idx=0";
    else
      var url = "hostBoard.php?p="+getParameters("p")+"&idx="+idx;
    window.open(url, "게시물 등록/수정", "width=800, height=900, scrollbars=yes, resizable=no");
  });

  //게시판 - 게시물 등록(수정)
  $(document).on('click', '#editButton_board', function(){
    var url = getParameters("p");
    var idx = getParameters("idx");
    var content = $(".ql-editor").html();

    if(idx != null && idx != 0){//수정
      $.ajax({
  			url: 'shop_host_exec.php',
  			type: 'post',
  			data: { host_board_idx : idx, content : content},
  			contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
  			dataType: 'html',
  			success:function(){
  					alert("수정을 완료하였습니다.");
            setTimeout(function(){window.self.close();},1000);
            setTimeout(function(){window.opener.parent.location.reload();},1000);
  			}
  		})
    }else if( url != null && url != ""){//등록
      $.ajax({
          url : 'shop_host_exec.php',
          type : 'post',
          data : { new_board_url : url, content : content },
          contentType : 'application/x-www-form-urlencoded; charset=UTF-8',
          dataType: 'html',
          success: function(){
            alert("등록을 완료하였습니다.");
            setTimeout(function(){window.self.close();},1000);
            setTimeout(function(){window.opener.parent.location.reload();},1000);
          }
      })
    }
  });

  //게시판 - 게시물 삭제
  $(document).on('click', '.board_delete_btn', function(){
    var idx = $(this).siblings("input[type='hidden']").val();

    $.ajax({
      url : 'shop_host_exec.php',
      type : 'post',
      data : { del_board_idx : idx },
      contentType : 'application/x-www-form-urlencoded; charset=UTF-8',
      dataType: 'html',
      success: function(){
        alert("삭제를 완료하였습니다.");
        setTimeout(function(){window.location.reload();},1000);
      }
    })
  });

  //이벤트 -  텍스트 에디터 팝업(등록, 수정 클릭시)
  $(document).on('click', '.event_textEditor_btn', function(){
   	var idx = $(this).closest(".layout_event").find("input[name='idx']").val();
	if(idx != null && idx != 0)
 		var url = "hostEvent.php?p="+getParameters("p")+"&idx="+idx;
	else
		var url = "hostEvent.php?p="+getParameters("p")+"&idx=0";
    window.open(url, "이벤트 등록/수정", "width=550, height=900, scrollbars=yes, resizable=no");
  });


  //이벤트 - 등록(수정)
  $(document).on('click', '#editButton_event', function(){
    var url = getParameters("p");
	var idx = getParameters("idx");
	
    var background_img = $("input[name='background_img']:checked").val();
    var message = $(".ql-editor").html();
    var date_start = $("input[name='date_start']").val();
    var date_end = $("input[name='date_end']").val();
   
   	if( $(".ql-editor").text()){
		alert("내용을 입력해주세요.");
		return;	
	}
    else if (date_start == "" || date_end == ""){
    	alert('날짜를 지정해주세요.');
	    return;
    }
	
	if(idx != null && idx != 0){//수정
		$.ajax({
			url: 'shop_host_exec.php',
			type: 'post',
			data: { host_popup_idx : idx, background_img: background_img, message : message, date_start : date_start, date_end : date_end },
			contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
			dataType: 'html',
			success:function(){
				alert("수정을 완료하였습니다.");
				setTimeout(function(){window.self.close();},1000);
				setTimeout(function(){window.opener.parent.location.reload();},1000);
			}
		})	
	}else{//등록
		$.ajax({
			url: 'shop_host_exec.php',
			type: 'post',
			data: { host_popup_url : url, background_img: background_img, message : message, date_start : date_start, date_end : date_end },
			contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
			dataType: 'html',
			success:function(){
				alert("수정을 완료하였습니다.");
				setTimeout(function(){window.self.close();},1000);
				setTimeout(function(){window.opener.parent.location.reload();},1000);
			}
		})	
	}
  });

	//이벤트 - 이벤트 삭제
	$(document).on('click', '.event_delete_btn', function(){
		var idx = $(this).closest(".layout_event").find("input[name='idx']").val();
		$.ajax({
			url: 'shop_exec.php',
			type: 'post',
			data: { popup_delete_idx : idx },
			contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
			dataType: 'html',
			success:function(){
				alert("삭제를 완료하였습니다.");
				setTimeout(function(){window.location.reload();},1000);
			}
		})	
	});
	
	//손님이야기 - 리뷰 사진 초기화
	$(".reply_photo img").each(function(){
		var index = $(this).index();
		$(this).css({ "right" : index*190+"px"});
		if(index > 0){
			$(this).hide();
		}
		$(".reply_photo img:eq(0)").hover(function(){
			$(".reply_photo img").show();
		}, function(){
			$(".reply_photo img:not(:eq(0))").hide();
		});
	});
	
	$(document).on("click", ".reply_comment", function(){
		$(this).toggleClass("limitedHeight");
	});
	
	$(document).on("click", ".reply_delete img", function(){
		if( !confirm("삭제하시겠습니까?") )
			return;
		var idx = $(this).attr('id');
		var layout = $(this).closest(".layout_reply");
		$.ajax({
			url: 'shop_host_exec.php',
			type: 'POST',
			data: { reply_delete_idx : idx},
			success: function(){
				layout.remove();
			}
		})
	});
	
	//키워드 - 키워드 등록
	$(document).on('keyup', '#input_keyword', function(e){
		var shop_idx = getParameters('idx');
		if(e.keyCode == 32){
			var keyword = $(this).val();
			$.ajax({
				url: 'shop_host_exec.php',
				type: 'POST',
				data: { tag_shop_idx : shop_idx, keyword : keyword },
				success: function(keywordIdx){
					var panel = "<div id='"+keywordIdx+"'>"+keyword+"</div>";
					$(".panel_keyword").append(panel);
					$("#input_keyword").val("");
				},
				error: function(){
					alert("에러가 발생하였습니다. 검색어 관리창을 다시 실행해주세요.");
					window.close();
				}
			})
		}
	});
	
	//키워드 - 키워드 삭제
	$(document).on('click', '.panel_keyword>div', function(){
		var keyword_idx = $(this).attr('id').trim();
		$.ajax({
			url: 'shop_host_exec.php',
			type: 'POST',
			data: { tag_word_idx : keyword_idx },
			success: function(){
				$(this).remove();
			}
		})
	});
});

//공통 - 파라미터 가져오기
function getParameters(paramName) {
    var returnValue;
    var url = location.href;
    var parameters = (url.slice(url.indexOf('?') + 1, url.length)).split('&');

    for (var i = 0; i < parameters.length; i++) {
        var varName = parameters[i].split('=')[0];
        if (varName.toUpperCase() == paramName.toUpperCase()) {
            returnValue = parameters[i].split('=')[1];
            return decodeURIComponent(returnValue);
        }
    }
}
