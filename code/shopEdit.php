<?php session_start(); ?>
<?php
header("Content-Type: text/html;charset=UTF-8");
include_once $_SERVER[DOCUMENT_ROOT].'/m/header.php';
include_once $_SERVER[DOCUMENT_ROOT].'/m/code/shop_exec.php';
?>
<?php
	$idx = $_GET['idx'];
	$isAgent = $_GET['isA'];
	$id = $_GET['id'];

	$result = ViewList_pop($connect,$idx);
	#$r_result = ViewRoulette_pop($connect, $idx);
	$menu_result = getMenu($connect, $idx);

	#$r_row =  mysqli_fetch_assoc($r_result); // 룰렛 데이터
?>
<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="<?=$path?>/js/shopFunction.js?ver=1"></script>
<script type="text/javascript" src="//dapi.kakao.com/v2/maps/sdk.js?appkey=7a23ce0d0bb4796d1535c957af98c96f&libraries=services"></script>
<script>
	$(document).ready(function(){
		
		$("#table_infor_btn").click(function(){ //테이블 정보 보기
		var idx = <?=$idx?>;

		window.open('table_infor.php?idx='+idx, '', 'width=900, height=900');
		});
		
		$("#btn_set_latlng").click(function(){ //위도&경도 입력
			var geocoder = new daum.maps.services.Geocoder();
			
			var address = $(".list_dist option:selected").text() + " " + $("input[name='shop_addr']").val();
			
			geocoder.addressSearch(address, function (result, status){
				if (status === daum.maps.services.Status.OK) {
					coords_a = new daum.maps.LatLng(result[0].y, result[0].x);

					$("input[name='lat']").val(result[0].y);
					$("input[name='lng']").val(result[0].x);
				}
				else{
					alert("주소에 해당하는 좌표를 찾을 수 없습니다.\n주소를 확인해주세요.");
				}
			});

		});
	});
</script>
<h2 class="title_sub"> 가맹점 정보 수정 </h2>
<form id="frmSearch">
    <input type="hidden" name="h_search_type" id="h_search_type" value="<?=$_GET['s_type']?>">
    <input type="hidden" name="h_search_text" id="h_search_text" value="<?=$_GET['s_text']?>">
</form>

<div class="pop_btn_set">
	<input type="button" name="btn_rgst" value="NFC" class="edit_btn" id="NFCButton">
	<input type="submit" name="btn_rgst" value="저장 / 수정" class="edit_btn" id="editButton">
	<input type="submit" name="btn_rgst" value="가맹점삭제" class="edit_btn" id="removeButton">
	<input type="button" name="btn_rgst" value="닫기" class="edit_btn" id="closeButton">
</div>

<?php

	$chk_res = "";
	$chk_free = "";
	$chk_park = "";
	$chk_seats = "";

	while ($row = mysqli_fetch_assoc($result)) {
	
		//if($row['isReserve'] != 0) $chk_res = "checked='checked'";
		//if($row['isFree_cds'] != 0) $chk_free = "checked='checked'";
		if($row['isParking'] != 0) $chk_park = "checked='checked'";
		if($row['isSeats'] != 0) $chk_seats = "checked='checked'";
	
?>

<table class="list_data">
    <colgroup>
        <col style="width:30%;">
        <col align="left" style="width:70%;" >
    </colgroup>
    <tr class="pop_list_item">
    	<th>상점 전경</th>
        <td>
            <form id='uploadimage_store' method='post' enctype='multipart/form-data'>
                <input type='hidden' name='shop_img_idx' value='<?=$idx?>'>
                <h4 id='loading'>
                업로드 가능한 확장자 : jpg, jpeg, png, gif<br/>
                (* 사진파일 업로드 용량 제한을 개선하였습니다)
                </h4>
                <div id='message'></div>
                <div id='image_preview'><img id='previewing' src='upload/noimage.png'/></div>
            <?php
            $photo_list = viewPhoto($connect, $idx);
            while($photo = mysqli_fetch_assoc($photo_list)){
                if($photo['photo_url']!=null){?>
                <div style="display:inline;">
                    <img class='image_select' src='<?=$photo['photo_url']?>' width='100px' height="62px" id="<?=$photo['idx']?>"/>
                </div>
            <? }
            }?>
            <?php
            if(mysqli_num_rows($photo_list)<8){
            ?>
                <input type='file' name='file' id='file' required />
                <input type='submit' value='Upload' class='edit_btn' /><? } ?>
                <input type="hidden" name="del_idx" id="del_idx"/>
        	</form>
        </td>
    </tr>
    
    <form method="post" id="shop_edit_form" name="shop_edit_form">
    <input type="hidden" name="edit_idx" value="<?=$idx?>" id="h_idx">
    <input type="hidden" name="edit_id" value="<?=$id?>" id="h_id">
    <input type="hidden" name="edit_url" value="<?=$row['url']?>" id="h_url"/>
    
    <tr class="pop_list_item">
        <th>가맹점명 / 업소분류</th>
        <td>
        	<input type="text" name="shop_name" class="input_text" value="<?=$row['shop_name']?>">
        	<select id="store_type" name="store_type">
        <?
        $type = array("한식", "일식", "노래방", "퓨전주점", "유흥주점", "기타", "멤버쉽");
        
        for($i = 1; $i < 7 ; $i++){
			if($row['type'] == $i) $select = "selected";
			else $select = "";
			
			echo "<option value='".$i."' ".$select.">".$type[($i-1)]."</option>";
		}
        ?>
    	    </select>
        </td>
    </tr>
    <tr class="pop_list_item">
        <th>가맹일</th>
        <td><input type="text" name="rgst_date" class="input_text" value="<?=$row['shop_rgst_date']?>" readonly="readonly"></td>
    </tr>
    
    <tr class="pop_list_item">
        <th>고유 파라미터 (URL)</th>
        <td>http://hanjicds001.gabia.io/index.jsp?p=<?=$row['url']?></td>
    </tr>
    
    <tr class="pop_list_item">
        <th>주인장 비밀번호</th>
        <td><input type="password" name="host_password" value="<?=$row['pass']?>"><button type='button' id='btn_reveal_password' class='edit_btn'>비밀번호 확인</button></td>
    </tr>
    
    <tr class="pop_list_item">
        <th>지사 / 센터</th>
        <td>
            <select id="branch_name_edit" name="branch_name_edit">
                <option value="<?=$row['branch_code']?>"><?=$row['branch_name']?></option>
            </select>
        	<input type="hidden" name="isAgent" id="isAgent" value="<?=$isAgent?>" />
            <select id="agent_name_edit" name="agent_name_edit">
			<?php
	        if($isAgent == 'n')
	        	$id = '';
	 
	        $agent = getAgent_pop($connect, $row['branch_code'], $id);
			
	        while ($ag = mysqli_fetch_assoc($agent)) {
				if($ag['agent_idx'] == $row['agent_idx'] ) $isSelect = "selected";
				else $isSelect = "";
	        	echo "<option value='".$ag['agent_idx']."' ".$isSelect.">".$ag["agent_name"]."</option>";
	        }
	        ?>
            </select>
        </td>
    </tr>
    
    <tr class="pop_list_item">
        <th>대표자</th>
        <td><input type="text" name="shop_ceo_name" class="input_text" value="<?=$row['shop_ceo_name']?>"></td>
    </tr>
    <tr class="pop_list_item">
        <th>연락처</th>
        <td>가맹점 전화번호 : <input type="text" name="shop_phone" class="input_text" value="<?=$row['shop_phone']?>"><br/>
        	가맹점 대표 연락처 : <input type="text" name="shop_ceo_phone" class="input_text" value="<?=$row['shop_ceo_phone']?>"></td>
    </tr>
    
    <tr class="pop_list_item">
        <th>주소(출발지)</th>
        <td>
            <select class="list_city">
            <?php
            $shop_location_city = ((int)($row['location_code']/100))*100;
            
            $cityList = getLocationCityList($connect);
            while($tlc_code = mysqli_fetch_assoc($cityList)){
                $location_code = $tlc_code['location_code'];
                $city = $tlc_code['city'];
                $selected = "";
                
                if( $location_code == $shop_location_city )
                    $selected = "selected";
                echo "<option value='".$location_code."' ".$selected.">".$city."</option>";
            }
            ?>
            </select>

            <select class="list_dist">
            <?php	
            $distList = getLocationDist($connect, $row['location_code']);
            while ($dist = mysqli_fetch_assoc($distList)){
                $dist_code = $dist['location_code'];
                $dist_place = $dist['location_place'];
            
                echo "<option value='".$dist_code."'>".$dist_place."</option>";
            }
            ?>
            </select>
            
            <div class="text_addr">
                <input type="text" name="shop_addr" class="input_text" value="<?=$row['shop_addr']?>" size="50" maxlength="100" placeholder="나머지 주소를 입력해주세요.">
                <span></span>
            </div>
            <button type="button" class="edit_btn" id="btn_set_latlng">좌표 입력</button></td>
    </tr>
    
    <tr class="pop_list_item">
        <th>위도/경도</th>
        <td>
        	위도 : <input type="text" name="lat" class="input_text" value="<?=$row['lat']?>"><br/>
			경도 : <input type="text" name="lng" class="input_text" value="<?=$row['lng']?>">
        </td>
    </tr>
    
    <tr class="pop_list_item">
        <th>등록일</th>
        <td><input type="text" name="rgst_date" class="input_text" value="<?=$row['shop_rgst_date']?>">
        (정보 수정일 : <?=$row['shop_edit_date']?>)
        </td>
    </tr>
    
    <tr class="pop_list_item">
        <th>영업정보</th>
        <td>
            평일 <input type="time" name="open_weekDay" value="<?=$row['open_weekDay']?>" /> ~ <input type="time" name="close_weekDay" value="<?=$row['close_weekDay']?>" /><br />
            주말 <input type="time" name="open_weekEnd" value="<?=$row['open_weekEnd']?>" /> ~ <input type="time" name="close_weekEnd" value="<?=$row['close_weekEnd']?>" /><br/>
            영업일 관련 정보(휴무일 등) : <input type="text" name="offday" class="input_text" value="<?=$row['offday']?>" size="50"><br />
            <!-- <button type="button" class="edit_btn" id="table_infor_btn">테이블별 주문 내역</button> -->
        </td>
    </tr>
    
    <tr class="pop_list_item">
        <th>추천메뉴</th>
        <td>
            <div class="text_recom_menu">
                <input type="text" name="recom_menu" class="input_text" value="<?=$row['recom_menu']?>" size="50" maxlength="100">
                <span></span>
            </div>
        </td>
    </tr>
    
    <tr class="pop_list_item">
        <th>한 줄 소개</th>
        <td>
            <div class="text_intro_text">
                <input type="text" name="intro_text" class="input_text" value="<?=$row['intro_text']?>" size="80" maxlength="100">
                <span></span>
            </div>
        </td>
    </tr>
    
    <tr class="pop_list_item">
        <th>부가정보</th>
        <td>
            할인율(%)
            <select name="discount">
                <option value="0" <?php if($row['discount']==0){?>selected<? }?>>0%</option>
                <option value="5" <?php if($row['discount']==5){?>selected<? }?>>5%</option>
                <option value="10" <?php if($row['discount']==10){?>selected<? }?>>10%</option>
                <option value="15" <?php if($row['discount']==15){?>selected<? }?>>15%</option>
            </select>
            <input type='checkbox' name='isParking' value="<?=$row['isParking']?>" <?=$chk_park?>>주차시설
            <input type='checkbox' name='isSeats' value="<?=$row['isSeats']?>" <?=$chk_seats?>>단체석 완비
        </td>
    </tr> 
    <? } ?>
    
    </form>

    <tr class="pop_list_item">
        <th>메뉴정보</th>
        <td><input type="button" name="btn_menu" value="메뉴등록" class="edit_btn" id="menuListButton"></td>
    </tr>
    <tr class="pop_list_item">
        <th>주인장이야기 관리</th>
        <td>
            <button type="button" class="edit_btn" id="boardListButton">게시판</button>
            <button type="button" class="edit_btn" id="popupListButton">이벤트</button> 
            <button type="button" class="edit_btn" id="saleListButton">번개할인</button>
            <button type="button" class="edit_btn" id="replyListButton">손님이야기</button>
            <button type="button" class="edit_btn" id="tagListButton">검색어</button>
            <br/>※ 버튼을 클릭하면 각 항목 관리 페이지가 생성됩니다.
        </td>
    </tr>
</table>
<?php
/* free result set */
mysqli_free_result($result);
?>

<script>
$(document).ready(function(){
	
});
</script>
</body>
</html>
