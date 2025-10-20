<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("tests");
?><?$APPLICATION->IncludeComponent(
	"bitrix:form.result.new", 
	"form.result.new", 
	[
		"COMPONENT_TEMPLATE" => "form.result.new",
		"WEB_FORM_ID" => "2",
		"IGNORE_CUSTOM_TEMPLATE" => "N",
		"USE_EXTENDED_ERRORS" => "N",
		"SEF_MODE" => "N",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600",
		"LIST_URL" => "result_list.php",
		"EDIT_URL" => "result_edit.php",
		"SUCCESS_URL" => "",
		"CHAIN_ITEM_TEXT" => "",
		"CHAIN_ITEM_LINK" => "",
		"VARIABLE_ALIASES" => [
			"WEB_FORM_ID" => "WEB_FORM_ID",
			"RESULT_ID" => "RESULT_ID",
		]
	],
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>