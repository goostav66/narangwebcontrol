<?php session_start(); ?>
<?php 
header("Content-Type: text/html;charset=UTF-8");
include_once $_SERVER[DOCUMENT_ROOT].'/m/header.php';
include_once $_SERVER[DOCUMENT_ROOT].'/m/code/shop_exec.php';
?>
<?php
	$idx = $_GET['idx'];
	$shop_name = $_GET['shop_name'];
	$menu_result = getMenu($connect, $idx);
?>
<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="<?=$path?>/js/shopFunctiontest.js"></script>
<script>
$( document ).ready(function() {
function toArray(list) {
  return Array.prototype.slice.call(list || [], 0);
}

function listResults(entries) {
  // Document fragments can improve performance since they're only appended
  // to the DOM once. Only one browser reflow occurs.
  var fragment = document.createDocumentFragment();

  entries.forEach(function(entry, i) {
    var img = entry.isDirectory ? '<img src="folder-icon.gif">' :
                                  '<img src="file-icon.gif">';
    var li = document.createElement('li');
    li.innerHTML = [img, '<span>', entry.name, '</span>'].join('');
    fragment.appendChild(li);
  });

  document.querySelector('#filelist').appendChild(fragment);
}

function onInitFs(fs) {

  var dirReader = fs.root.createReader();
  var entries = [];

  // Call the reader.readEntries() until no more results are returned.
  var readEntries = function() {
     dirReader.readEntries (function(results) {
      if (!results.length) {
        listResults(entries.sort());
      } else {
        entries = entries.concat(toArray(results));
        readEntries();
      }
    }, errorHandler);
  };

  readEntries(); // Start reading dirs.

}

window.requestFileSystem(window.TEMPORARY, 1024*1024, onInitFs, errorHandler);
});
</script>

<h2 class="title_sub"> 가맹점 메뉴 리스트 </h2>

<div class="pop_btn_set">
* 메뉴 하나씩 등록,수정,삭제가 가능합니다.
	
	<input type="button" name="btn_rgst" value="닫기" class="edit_btn" id="closeButton" />
</div>

<table class="list_data">
	<colgroup>
		<col style="width:30%;">
		<col align="left" style="width:70%;" >
	</colgroup>
<input type="hidden" name="edit_idx" value="<?=$idx?>" id="h_idx" />
	<tr class="pop_list_item">
		<th>가맹점명</th>
		<td><?=$shop_name?></td>
	</tr>
	<tr class="pop_list_item">
		<th>메뉴정보</th>
		<td>
		
			<?php while ($row = mysqli_fetch_assoc($menu_result)) { 
					
					if($row['type'] == 0){
						$menu_all = "selected";
						$launch = "";
					}else if($row['type'] == 1){
						$menu_all = "";
						$launch = "selected";
					}

					if(!empty($row['price_m']) || !empty($row['price_l'])){
						$display = "style='display: none'";
						$display_sml = "style='display: block'";
					}else if(empty($row['price_m']) && empty($row['price_l'])){
						$display = "style='display: block'";
						$display_sml = "style='display: none'";
					}
			?>
			<div class="shop_menu">
				
				<div>
					<input type="button" name="btn_menu_remove" value="메뉴삭제" class="btn_menu_delete edit_btn" id="btn_menu_remove">
					<input type="hidden" id="menu_remove" name="menu_remove" value="<?=$row['idx']?>" />
				</div>
				<form class="uploadimage" name="uploadimage" method="post" enctype="multipart/form-data">
					<div class="shopmenu_img" >
						<div class='image_preview_menu'>
							<img class='previewing_menu' src='<?=$row['photo_url']?>' width="100px" />
						</div>
						<input type="file" name="file_menu" class="file_menu" />
					</div>
					<div>type : <select id="menu_type" name="menu_type">
								  <option value="0" <?=$menu_all?>>전체메뉴</option>
								  <option value="1" <?=$launch?>>점심특선</option>
								</select>
					
						이름 : <input type="text" name="menu_name" class="input_text" size="10" 
								value="<?=$row['menu_name']?>" required><br/>

						<span class="price" <?=$display?>>
								가격 : <input type="text" name="price" class="input_text" size="10" 
								value="<?=$row['price']?>">

								<input type="button" value="대/중/소 전환" class="price_btn" 
								id="btn_menu_price" name="btn_menu_price" /><br/>  
						</span>

						<span class="price_sml" <?=$display_sml?>>
								대 : <input type="text" name="price_l" class="input_text" size="10" 
								value="<?=$row['price_l']?>"> 

								중 : <input type="text" name="price_m" class="input_text" size="10" 
								value="<?=$row['price_m']?>">

								소 : <input type="text" name="price_s" class="input_text" size="10" 
								value="<?=$row['price']?>">

								<input type="button" value="단일가격 전환" class="price_btn" 
								id="btn_menu_price" name="btn_menu_price" /><br/> 
						</span>

						정보 : <textarea rows='4' cols='50' name="info" class="input_text" ><?=$row['info']?></textarea>
								<span class='letter' style="width:20px;display:inline-block"></span></br/>
								<input type="submit" value="메뉴저장" class="edit_btn" id="btn_menu_edit" name="btn_menu_edit" style="float: right; margin-top: -38px;" />
								<input type='hidden' name='menu_idx' id='menu_idx' value='<?=$row['idx']?>'>
								<input type='hidden' name='shop_menu_idx' id='shop_menu_idx' value='<?=$idx?>'>
								<input type='hidden' name='shop_menu_edit' id='shop_menu_edit' value='1'>
					</div>
				</form>
			</div>
			<? }?>
			
			<input type="button" name="btn_menu" value="항목추가" class="edit_btn" id="menuButton">
			<div class="shop_menu">
				<?/* 삭제버튼은 두번째 메뉴 입력 항목부터
				<div><input type="button" name="btn_menu_delete" value="X" class="btn_menu_delete edit_btn"
							id="btn_menu_delete"></div>
				*/?>
				
				<? //메뉴판 입력폼 -- 폼 추가 액션 : shopFunction.js ?>
				<form class="uploadimage" method="post" enctype="multipart/form-data">
					<div class="shopmenu_img" >
						<div class='image_preview_menu'>
							<img class='previewing_menu' src='upload/noimage.png' />
						</div>
						<input type="file" name="file_menu" class="file_menu" />
					</div>
					<div>type : <select id="menu_type" name="menu_type">
								  <option value="0">전체메뉴</option>
								  <option value="1">점심특선</option>
								</select>
					
						이름 : <input type="text" name="menu_name" class="input_text" size="10" required>
						<span class="price" >
								가격 : <input type="text" name="price" class="input_text" size="10" >

								<input type="button" value="대/중/소 전환" class="price_btn" 
								id="btn_menu_price" name="btn_menu_price" /><br/>  
						</span>

						<span class="price_sml" style="display:none">
								대 : <input type="text" name="price_l" class="input_text" size="10" > 

								중 : <input type="text" name="price_m" class="input_text" size="10" >

								소 : <input type="text" name="price_s" class="input_text" size="10" >

								<input type="button" value="단일가격 전환" class="price_btn" 
								id="btn_menu_price" name="btn_menu_price" /><br/> 
						</span>
						정보 : <textarea rows='4' cols='50' name="info" class="input_text" style="width:20px;display:inline-block"></textarea>
								<input type="submit" value="메뉴저장" class="edit_btn" />
								<input type='hidden' name='shop_menu_idx' id='shop_menu_idx' value='<?=$idx?>'>
					</div>
				</form>
				<? //메뉴판 입력폼 ?>
				
				
			</div>
		</td>
	</tr>

</table>


</body>
</html>