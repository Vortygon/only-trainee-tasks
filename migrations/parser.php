<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
if (!$USER->IsAdmin()) {
    LocalRedirect('/');
}

\Bitrix\Main\Loader::includeModule('iblock');

define('IBLOCK_CODE', 'VACANCIES');
define('CSV_FILE_NAME', 'vacancy.csv');

if (($handle = fopen(CSV_FILE_NAME, 'r')) !== false) {
    // Пропускаем заголовок таблицы
    fgetcsv($handle);

    while(($data = fgetcsv($handle)) !== false) {
        // echo implode(", ", $data) . "<br>";

        $PROP['ACTIVITY'] = $data[9];
        $PROP['FIELD'] = $data[11];
        $PROP['OFFICE'] = $data[1];
        $PROP['LOCATION'] = $data[2];
        $PROP['REQUIRE'] = $data[4];
        $PROP['DUTY'] = $data[5];
        $PROP['CONDITIONS'] = $data[6];
        $PROP['EMAIL'] = $data[12];
        $PROP['DATE'] = date('d.m.Y');
        $PROP['TYPE'] = $data[8];
        $PROP['SALARY_TYPE'] = '';
        $PROP['SALARY_VALUE'] = $data[7];
        $PROP['SCHEDULE'] = $data[10];

        foreach ($PROP as $key => &$value) {
            sanitizeValue($value);
        }

        foreach (['REQUIRE', 'DUTY', 'CONDITIONS'] as $key) {
            parseListValue($PROP[$key]);
        }

        echo implode(", ", $PROP) . "<br>";
    }

    fclose($handle);
}

function sanitizeValue(&$value) {
    $value = trim($value);
    $value = str_replace('\n', '', $value);
}

function parseListValue(&$value) {
    if (stripos($value, '•') !== false) {
        $value = explode('•', $value);
        foreach ($value as &$el) {
            $el = trim($el);
        }
    }
}

