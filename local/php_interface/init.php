<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Loader;

// Review
use Bitrix\Iblock\IblockTable;

class LogCleanerAgent
{
    const LOG_IBLOCK_CODE = 'LOG';
    const KEEP_LOGS_COUNT = 10;

    /**
     * Основной метод агента
     * @return string
     */

    public static function cleanOldLogs()
    {
        if (!Loader::includeModule('iblock')) {
            return self::getAgentString();
        }

        $logIblockId = self::getLogIblockId();
        if (!$logIblockId) {
            return self::getAgentString();
        }

        self::deleteOldElements($logIblockId);

        return self::getAgentString();
    }

    /**
     * Получает ID инфоблока LOG
     */
    private static function getLogIblockId()
    {
        $result = IblockTable::getList([
            'filter' => ['CODE' => self::LOG_IBLOCK_CODE],
            'select' => ['ID']
        ])->fetch();

        return $result ? $result['ID'] : false;
    }

    /**
     * Удаляет старые элементы, оставляя только 10 последних
     */
    private static function deleteOldElements($iblockId)
    {
        // Получаем все элементы, отсортированные по дате создания (новые первые)
        $arFilter = [
            'IBLOCK_ID' => $iblockId,
            'ACTIVE' => 'Y'
        ];

        $arSelect = ['ID', 'TIMESTAMP_X'];

        $rsElements = CIBlockElement::GetList(
            ['TIMESTAMP_X' => 'DESC'],
            $arFilter,
            false,
            false,
            $arSelect
        );

        $elementIds = [];
        while ($arElement = $rsElements->GetNext()) {
            $elementIds[] = $arElement['ID'];
        }

        // Если элементов меньше или равно 10, ничего не удаляем
        if (count($elementIds) <= self::KEEP_LOGS_COUNT) {
            return;
        }

        // Оставляем первые 10 элементов, остальные удаляем
        $elementsToDelete = array_slice($elementIds, self::KEEP_LOGS_COUNT);

        foreach ($elementsToDelete as $elementId) {
            CIBlockElement::Delete($elementId);
        }
    }

    /**
     * Возвращает строку для повторного запуска агента
     */
    private static function getAgentString()
    {
        return '\\LogCleanerAgent::cleanOldLogs();';
    }

    /**
     * Регистрирует агент в системе
     */
    public static function register()
    {
        if (!Loader::includeModule('main')) {
            return false;
        }

        // Проверяем, не зарегистрирован ли уже агент
        $agentExists = CAgent::GetList(
            [],
            [
                'NAME' => '\\LogCleanerAgent::cleanOldLogs();',
                '=MODULE_ID' => ''
            ]
        )->Fetch();

        if ($agentExists) {
            return true;
        }


        CAgent::AddAgent(
            '\\LogCleanerAgent::cleanOldLogs();',
            '',
            'N',
            3600,
            '',
            'Y',
            '',
            30
        );

        return true;
    }
}

// Автоматическая регистрация агента при подключении файла
LogCleanerAgent::register();
