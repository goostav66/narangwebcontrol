<?php 
header("Content-Type: text/html;charset=UTF-8");
$path = "/m";
$m_path = "http://m.nfczone.co.kr";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ko" xml:lang="ko">
<head>
<title> NFC ZONE </title>
<meta name="generator" content="editplus" />
<meta name="author" content="hanjiCDS" />
<meta name="keywords" content="NFC, N-DEADA, DRIVER CALL SERVICE, E-MENU" />
<meta name="description" content="This Mobile Web is developed for the user of Driving service" />

<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes">
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">

<link rel="stylesheet" href="<?=$path?>/js/jquery.mobile-1.4.5/jquery.mobile-1.4.5.min.css" />
<link rel="stylesheet" href="<?=$path?>/css/reset.css" />
<link rel="stylesheet" href="<?=$path?>/reserve/style_reserve.css" />
<script type="text/javascript" src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="<?=$path?>/js/jquery.mobile-1.4.5/jquery.mobile-1.4.5.min.js"></script>
<script src="<?=$path?>/reserve/script.js"></script>
<script src="<?=$path?>/js/jquery.session.js"></script>
<script src="<?=$path?>/js/customSwipePage.js"></script>

<!-- Google Translate -->
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
<script>
function googleTranslateElementInit() {
	  new google.translate.TranslateElement(
		  {pageLanguage: 'ko', 
		  includedLanguages: 'en', 
		  layout: google.translate.TranslateElement.InlineLayout.SIMPLE, 
		  multilanguagePage: true}, 
		  'google_translate_element');
	}
</script>

<!-- Bootstrap
<link href="<?=$path?>/css/bootstrap-formhelpers.min.css" rel="stylesheet" media="screen" />
<link href="<?=$path?>/css/bootstrap-formhelpers.css" rel="stylesheet" media="screen" />
<script src="<?=$path?>/js/bootstrap-formhelpers.js"></script>
 -->
</head>
