<?php session_start(); ?>
<?php
header("Content-Type: text/html;charset=UTF-8");
include_once $_SERVER[DOCUMENT_ROOT].'/m/header.php';
include_once $_SERVER[DOCUMENT_ROOT].'/m/code/shop_host/shop_host_exec.php';
?>
<?php
	$idx = $_GET['idx'];
	$result = viewKeywordList($connect, $idx);
?>
<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="<?=$path?>/js/shopHostFunction.js?ver=1"></script>
<section>
    <h2 class="title_sub"> 검색어 관리 </h2>
    <table class="list_data">
        <colgroup>
            <col style="width:30%">
            <col style="width:70%">
        </colgroup>
        <tr>
            <th>검색어 목록</th>
            <td>
                <div class="panel_keyword">
                <? while($row = mysqli_fetch_assoc($result)){ ?>
                	<div id="<?=$row['idx']?>"><?=$row['tag']?></div>
                <? } ?>
                </div>
            </td>
        </tr>
        <tr>
        	<td colspan="2">
            	<div>
                    <span>예약 서비스에서 입력한 검색어가 검색어 목록에 포함되어 있으면 검색 결과에 나타납니다.</span><br />
                    <span>가맹점명, 주소, 업종, 메뉴명은 검색어 목록에 입력하지 않아도 검색풀에 포함됩니다.</span><br />
                    <input type="text" id="input_keyword"><br />
                    <span>추가 : 띄어쓰기(space) 입력 / 삭제 : 삭제할 검색어 클릭</span>
                </div>
            </td>
        </tr>
    </table>
</section>