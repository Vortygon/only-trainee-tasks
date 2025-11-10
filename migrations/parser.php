<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
if (!$USER->IsAdmin()) {
    LocalRedirect('/');
}

\Bitrix\Main\Loader::includeModule('iblock');

define('IBLOCK_CODE', 'VACANCIES');
define('CSV_FILE_NAME', 'vacancy.csv');

if (($handle = fopen(CSV_FILE_NAME, "r")) !== false) {
    fgetcsv($handle);

    while(($data = fgetcsv($handle)) !== false) {
        echo implode(", ", $data) . '\n';
    }

    fclose($handle);
}