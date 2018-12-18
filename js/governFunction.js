$(document).ready(function(){
	var g_idx = $("#g_idx").val(); 
	//--------------------------------------로고 이미지 변경 팝업
	$("#mod_logo_image").click(function(){
		window.open("logoPopup.php?idx="+g_idx, "", "menubar=no, width=300, height=200", "");
	});
	
	//--------------------------------------배경 이미지 변경 팝업
	$("#mod_back_image").click(function(){
		window.open("backPopup.php?idx="+g_idx, "", "menubar=no, width=300, height=200", "");
	});
	
	//--------------------------------------축제/행사 수정 팝업
	$(".govern_festival").click(function(){
		var f_idx = parseInt( $(this).attr("id") );
		window.open("modFestival.php?f_idx="+f_idx, "", "menubar=no, width=900, height=650");
	});
	
	//--------------------------------------축제/행사 추가 팝업
	$("#edit_festival").click(function(){
		window.open("newFestival.php?idx="+g_idx, "", "menubar=no, width=900, height=650");	
	});
	
	//--------------------------------------특산물 추가 팝업
	$("#edit_specialty").click(function(){
		window.open("newSpecialty.php?idx="+g_idx, "", "menubar=no, width=900, height=650");
	});
	
	//--------------------------------------축제/행사 추가
	$(".save_btn").click(function(){
		var festival_title = $("input[name='f_title']").val();
		if( festival_title == null || $.trim(festival_title)=='' ){
			alert('제목을 입력해주세요.');
			return;
		}
		
		var content = document.querySelector('#editor');
		var html = content.children[0].innerHTML;		 
		$("input[name='f_content']").val(html);
		
		
		var arr_file_name = [];
		$("input[type='file']").each(function(){
			arr_file_name.push( $(this).attr('name').substring($(this).attr('name').indexOf("_")+1));
		});
		
		$("input[name='arr_file_name']").val(arr_file_name);
			
		$("#form_festival_add").submit();
		alert('추가가 완료되었습니다.');
		setTimeout(function(){window.close();},1000);
		setTimeout(function(){window.opener.parent.location.reload();},1000);
		
	});
	
	//--------------------------------------축제/행사 수정
	$(".modify_btn").click(function(){
		var festival_title = $("input[name='f_title']").val();
		if( festival_title == null || $.trim(festival_title)=='' ){
			alert('제목을 입력해주세요.');
			return;
		}
		
		var content = document.querySelector('#editor');
		var html = content.children[0].innerHTML;		 
		$("input[name='f_content']").val(html);
		
		
		//새로 추가된 사진
		var arr_file_name = [];
		$("input[type='file']").each(function(){
			arr_file_name.push( $(this).attr('name').substring($(this).attr('name').indexOf("_")+1));
		});
		
		$("input[name='arr_file_name']").val(arr_file_name);
			
		//삭제할 기존 사진
		$("input[name='arr_file_idx']").val(arr_del_image.join(","));
		$("#form_festival_mod").submit();
		alert('수정이 완료되었습니다.');
		setTimeout(function(){window.close();},1000);
		setTimeout(function(){window.opener.parent.location.reload();},1000);
	
	});
	
	
	//--------------------------------------축제/행사 삭제
	$(".remove_btn").click(function(e){
		e.preventDefault();
		if(confirm('삭제하시겠습니까?\n삭제된 내용은 복구할 수 없습니다.')){
			var f_idx = $("input[name='f_idx_fes']").val();
			
			$.ajax({
				url : 'govern_exec.php',
				type : 'post',
				data : { delete_f_idx : f_idx },
				contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
				dataType: 'html',
				success:function(){
					alert("삭제를 완료하였습니다.");
					setTimeout(function(){ window.close(); }, 1000);
					setTimeout(function(){window.opener.parent.location.reload();},1000);
				}
			})
		}

	});
	
	//-------------------------------------특산물 아이콘 추가/변경
	$(".s_icon_btn").click(function(e){
		e.preventDefault();
		$(".s_icon_file").click();
		
		$(".s_icon_file").change(function(e2){
			e2.preventDefault();
			readURL(this, $(".s_icon_img"));
		});
	});
	
	//-------------------------------------특산물 새 항목 추가
	$(".s_category_btn").click(function(e){
		var layout = '<section class="section_s_item">'
                    +	'<div class="s_item_header">사진</div>'  
                    +   '<div class="s_item_data">'
                    +   	'<img class="s_item_img" src="../images/noimage.png">'
                    +       '<input type="file" class="s_item_file">'
                    +       '<button type="button" class="edit_btn s_item_btn">사진 선택</button>'
                    +   '</div>'
                    +   '<div class="s_item_header">상품명</div>'
                    +   '<div class="s_item_data">'
                    +    	'<input type="text">'
                    +   '</div>'
                    +   '<div class="s_item_header">가격</div>'
                    +   '<div class="s_item_data">'
                    +   	'<input type="text">'
                    +   '</div>'
                    +   '<div class="s_item_header">연락처</div>'
                    +   '<div class="s_item_data">'
                    +   	'<input type="text">'
                    +   '</div>'
                    +   '<button type="button" class="edit_btn del_item_btn">삭제</button>'
                    + 	'</section>';
		$(".s_appended_item").append(layout);	
		
		$(".s_item_btn").click(function(){
			$(this).siblings(".s_item_file").click();
			$(".s_item_file").change(function(){
				readURL(this, $(this).prev());
			});
		});
		$(".del_item_btn").click(function(){
			$(this).closest(".section_s_item").remove();
		});		
		
	});

	
	//--------------------------------------사진 추가
	var file_idx = 0;
	$("#add_file").click(function(e){
		file_idx++;
		e.preventDefault();
		var file = "<input type='file' name='file_"+file_idx+"' accept='image/*' style='display:none'>";
		$(".inputFile").append(file);
		$("input[name='file_"+file_idx+"']").click();
		
		$("input[type='file']").change(function(e2){
			e2.preventDefault();
			var image = "<div class='cell_img'><div class='box_remove'><img src='http://na.nfczone.co.kr/m/images/icon_x2.png'></div>" 
						+"<img src='' id="+file_idx+" style='width: 150px; height: 93px; float: left;'></div>";
			$(".inputImage").append(image);
			var img = $(".inputImage img:last");
			readURL(this, img);
			
			$(".box_remove").click(function(){
				var idx = $(this).next().attr('id');
				$("input[name='file_"+idx+"']").remove();
				$("img[id='"+idx+"']").remove();
				$(this).parent().remove();
			});
			
			/*if( $(".inputImage img:first").attr('id') == $(".inputImage img:last").attr('id') ){
				var represent = "<div class='represent'></div>";
				$(".inputImage img:first").parent().append(represent);
			}*/
		});
	});
	
	//--------------------------------------사진 삭제
	var arr_del_image = [];
	$(".box_remove").click(function(){
		if(confirm("사진을 삭제하시겠습니까?\n(저장을 눌러야 삭제된 내용이 저장됩니다)")){
			var fi_idx = parseInt( $(this).parent().attr('id') );
			arr_del_image.push(fi_idx);
			$(this).parent().remove();
		}
	});
	
	
	$(".close_btn").click(function(){
		if(confirm("취소하시겠습니까?\n변경된 내용은 저장되지 않습니다."))
			window.close();
	});
	
	function readURL(input, img) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();
	
			reader.onload = function (e) {
				img.attr('src', e.target.result);
			}
	
			reader.readAsDataURL(input.files[0]);
		}
	}
});
	

	