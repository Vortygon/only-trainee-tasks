<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Migrations");
?><?$APPLICATION->IncludeComponent(
	"bitrix:iblock.element.add.list",
	"",
	Array(
		"ALLOW_DELETE" => "Y",
		"ALLOW_EDIT" => "Y",
		"EDIT_URL" => "",
		"ELEMENT_ASSOC" => "CREATED_BY",
		"GROUPS" => array(),
		"IBLOCK_ID" => "3",
		"IBLOCK_TYPE" => "CONTENT_RU",
		"MAX_USER_ENTRIES" => "100000",
		"NAV_ON_PAGE" => "10",
		"SEF_MODE" => "N",
		"STATUS" => "ANY"
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>