<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
if (!$USER->IsAdmin()) {
    LocalRedirect('/');
}

\Bitrix\Main\Loader::includeModule('iblock');

define('IBLOCK_CODE', 'VACANCIES');
define('CSV_FILE_NAME', 'vacancy.csv');
define('SIMILARITY_THRESHOLD', 50);
define('IBLOCK_ID', CIBLock::getList(array(), ['=CODE' => IBLOCK_CODE])->GetNext()['ID']);

$element = new CIBlockElement;

if (IBLOCK_ID === false) {
    echo 'Инфоблок вакансий не найден.';
    exit();
}

if (($handle = fopen(CSV_FILE_NAME, 'r')) !== false) {
    clearVacancies();
    $arrayProperties = initPropertiesList();

    // Пропускаем заголовок таблицы
    fgetcsv($handle);
    while(($data = fgetcsv($handle)) !== false) {
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
        foreach (['ACTIVITY', 'FIELD', 'OFFICE', 'LOCATION', 'TYPE', 'SCHEDULE'] as $key) {
            handleDictionaryValue($key, $PROP[$key], $arrayProperties, $data[3]);
        }
        handleSalaryValue($PROP['SALARY_VALUE'], $PROP['SALARY_TYPE']);

        $arLoadProductArray = [
            "MODIFIED_BY" => $USER->GetID(),
            "IBLOCK_SECTION_ID" => false,
            "IBLOCK_ID" => IBLOCK_ID,
            "PROPERTY_VALUES" => $PROP,
            "NAME" => $data[3],
            "ACTIVE" => end($data) ? 'Y' : 'N',
        ];

        if ($PRODUCT_ID = $element->Add($arLoadProductArray)) {
            echo "Добавлен элемент с ID : " . $PRODUCT_ID . "<br>";
        } else {
            echo "Ошибка: " . $element->LAST_ERROR . '<br>';
        }
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

function handleSalaryValue(&$value, &$type) {
    switch ($value) {
        case 'по договоренности':
            $type = 'договорная';
            // no break
        case '-':
            $value = '';
            break;
        default:
            $salary = explode(' ', $value);
            if ($salary[0] == 'от' || $salary[0] == 'до') {
                $type = $salary[0];
                array_splice($salary, 0, 1);
                $value = implode(' ', $salary);
            } else {
                $type = '=';
            }
            break;
    }
}

function handleDictionaryValue(&$key, &$value, &$arrayProperties, $name) {
    if ($key == 'OFFICE') {
        switch ($value = strtolower($value)) {
            case 'центральный офис':
                $value .= 'свеза ' . $name;
                break;
            case 'лусозагатовка':
                $value = 'свеза ресурс' . $value;
                break;
            case 'свеза тюмень':
                $value = 'свеза тюмени';
                break;
        }
    }

    $properties = $arrayProperties[$key];
    foreach ($properties as $propKey => $propValue) {
        if (stripos($propKey, $value) !== false) {
            $value = $propValue;
            break;
        }

        if (similar_text($propKey, $value) > SIMILARITY_THRESHOLD) {
            $value = $propValue;
        }
    }
}

function initPropertiesList() {
    $enumProperties = CIBlockPropertyEnum::GetList(
        ['SORT' => 'ASC', 'VALUE' => 'ASC'],
        ['IBLOCK_ID' => IBLOCK_ID]
    );
    
    $arrayProperties = [];
    while ($property = $enumProperties->Fetch()) {
        $key = trim($property['VALUE']);
        $arrayProperties[$property['PROPERTY_CODE']][$key] = $property['ID'];
    }

    return $arrayProperties;
}

function clearVacancies() {
    $items = CIBlockElement::GetList(
        [],
        ['IBLOCK_ID' => IBLOCK_ID],
        false,
        false,
        ['ID']
    );
    
    $deletedCount = 0;
    while ($item = $items->GetNext()) {
        if (CIBlockElement::Delete($item['ID'])) {
            $deletedCount++;
        }
    }
    
    echo "Удалено элементов: {$deletedCount}<br>";
}