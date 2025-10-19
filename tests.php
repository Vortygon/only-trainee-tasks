<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("tests");
?><?$APPLICATION->IncludeComponent(
	"bitrix:form.result.new", 
	"form.result.new", 
	[
		
	],
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>