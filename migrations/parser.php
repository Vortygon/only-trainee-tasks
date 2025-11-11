<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
if (!$USER->IsAdmin()) {
    LocalRedirect('/');
}

\Bitrix\Main\Loader::includeModule('iblock');

define('IBLOCK_CODE', 'VACANCIES');
define('CSV_FILE_NAME', 'vacancy.csv');
define('SIMILARITY_THRESHOLD', 50);
$IBLOCK_ID = IBlockElementLoader::getIBlockId(IBLOCK_CODE);

if ($IBLOCK_ID === false) {
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

        // foreach (['ACTIVITY', 'FIELD', 'OFFICE', 'LOCATION', 'TYPE', 'SCHEDULE'] as $key) {
        //     handleDictionaryValue($key, $PROP[$key], $arrayProperties, $data[3]);
        // }

        handleSalaryValue($PROP['SALARY_VALUE'], $PROP['SALARY_TYPE']);

        // echo implode(", ", $PROP) . "<br>";
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
    $properties = $arrayProperties[$key];

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

    foreach ($properties as $property) {
        if (stripos($property, $value) !== false) {
            $value = $property;
            break;
        }
    
        if (similar_text($property, $value) > SIMILARITY_THRESHOLD) {
            $value = $property;
        }
    }


    // foreach ($PROP as $key => &$value) {
    //     if (stripos($value, '•') !== false) {
    //         $value = explode('•', $value);
    //         array_splice($value, 0, 1);
    //         foreach ($value as &$str) {
    //             $str = trim($str);
    //         }
    //     } elseif ($arProps[$key]) {
    //         $arSimilar = [];
    //         foreach ($arProps[$key] as $propKey => $propVal) {
    //             if ($key == 'OFFICE') {
    //                 $value = strtolower($value);
    //                 if ($value == 'центральный офис') {
    //                     $value .= 'свеза ' . $data[2];
    //                 } elseif ($value == 'лесозаготовка') {
    //                     $value = 'свеза ресурс ' . $value;
    //                 } elseif ($value == 'свеза тюмень') {
    //                     $value = 'свеза тюмени';
    //                 }
    //                 $arSimilar[similar_text($value, $propKey)] = $propVal;
    //             }
    //             if (stripos($propKey, $value) !== false) {
    //                 $value = $propVal;
    //                 break;
    //             }

    //             if (similar_text($propKey, $value) > 50) {
    //                 $value = $propVal;
    //             }
    //         }
    //         if ($key == 'OFFICE' && !is_numeric($value)) {
    //             ksort($arSimilar);
    //             $value = array_pop($arSimilar);
    //         }
    //     }
    // }
}

function initPropertiesList() {
    $arrayProperties = [];

    $enumProperties = CIBlockPropertyEnum::GetList(
        ['SORT' => 'ASC', 'VALUE' => 'ASC'],
        ['IBLOCK_ID' => self::$IBLOCK_ID]
    );

    while ($property = $enumProperties->Fetch()) {
        $key = trim($property['VALUE']);
        $arrayProperties[$property['PROPERTY_CODE']][$key] = $property['ID'];
    }

    return $arrayProperties;
}

function clearVacancies() {
    $items = CIBlockElement::GetList(
        [],
        ['IBLOCK_ID' => VACANCY_IBLOCK_ID],
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