$( document ).ready(function() {

	// 호출관리 상태변경 코드
	$( ".select_state" ).change(function () { 
	
		var result = confirm("상태를 변경하시겠습니까?");
		
		if(result){
			var item = "state";

			var optionSelected = $("option:selected", this);
			var valueSelected = this.value;

			var selectedParent = optionSelected.parent();
			selectedParent.parents("tr").addClass( "selected" );

			var a = $(this).parent();
			var b = a.parent();
			var selectedIDX = b.children($(".idx")).val();

	
			//YES : 변경된 값 서버전송
			$.ajax({
				url:'shopPlus_reserve_exec.php',
				type:'post',
				data:{ item_name : item, value : valueSelected, change_idx : selectedIDX},
				contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
				dataType: 'html',
				success:function(data){
					if(data == "success"){
						
						// 변경된 tr 색상 변경
						switch(valueSelected){
							case "0" : selectedParent.parents("tr").css("background", "#fff"); 
										selectedParent.parents("tr").children().children().css("background", "#fff"); 
										break;
							case "1" : selectedParent.parents("tr").css("background", "#62faff"); 
										selectedParent.parents("tr").children().children().css("background", "#62faff");
										break;
							case "2" : selectedParent.parents("tr").css("background", "#fff200"); 
										selectedParent.parents("tr").children().children().css("background", "#fff200");
										break;
							case "3" : selectedParent.parents("tr").css("background", "#ffa7c4"); 
										selectedParent.parents("tr").children().children().css("background", "#ffa7c4");
										break;
							case "4" : selectedParent.parents("tr").css("background", "#ffa7c4"); 
										selectedParent.parents("tr").children().children().css("background", "#ffa7c4");
										break;
							case "5" : selectedParent.parents("tr").css("background", "#ffa7c4"); 
										selectedParent.parents("tr").children().children().css("background", "#ffa7c4");
										break;
							case "6" : selectedParent.parents("tr").css("background", "#ffa7c4"); 
										selectedParent.parents("tr").children().children().css("background", "#ffa7c4");
										break;
						}
						$(this).blur();

					}else{
						alert(data);
					}
				}
			})
	
			return true;
			
		}else {
			//No : 기존 값 셋팅
			var originValue = $(this).siblings("input").val();
			
			$(this).val(originValue);
			$(this).blur();
			return true;
		}
	});
});