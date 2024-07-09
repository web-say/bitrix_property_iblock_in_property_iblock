<?php

namespace lib\usertype;

use Bitrix\Iblock;
use CIBlockElement;
use CIBlockPropertyEnum;
use CModule;

/**
 * Реализация свойств «Свойства инфоблока»
 * Class CUserTypePropertyIblock
 * @package lib\usertype
 */
class CUserTypePropertyIblock
{
    /**
     * Метод возвращает массив описания собственного типа свойств
     * @return array
     */
    public static function GetUserTypeDescription()
    {
        return array(
            'USER_TYPE_ID' => 'property_iblock', //Уникальный идентификатор типа свойств
            'USER_TYPE' => 'CUSTOM',
            'CLASS_NAME' => __CLASS__,
            'DESCRIPTION' => 'Свойства инфоблока',
            'PROPERTY_TYPE' => Iblock\PropertyTable::TYPE_SECTION,
            'ConvertToDB' => [__CLASS__, 'ConvertToDB'],
            'ConvertFromDB' => [__CLASS__, 'ConvertFromDB'],
            'GetPropertyFieldHtml' => [__CLASS__, 'GetPropertyFieldHtml'],
            'GetPublicEditHTMLMulty' => array(__CLASS__, 'GetPublicEditHTMLMulty'),
            "GetSettingsHTML" => array(__CLASS__, "GetSettingsHTML"),
            "PrepareSettings" => array(__CLASS__, "PrepareSettings"),
        );
    }


    public static function GetSettingsHTML($arProperty, $strHTMLControlName, &$arPropertyFields)
    {

        $fields = [];

        if (CModule::IncludeModule('iblock')) {
            $dbEl = CIBlockElement::GetList(array(), array("IBLOCK_ID" => $arProperty['LINK_IBLOCK_ID']));
            if ($obEl = $dbEl->GetNextElement()) {
                $fields = $obEl->GetProperties();
            }
        }

        $value = $arProperty['USER_TYPE_SETTINGS']['FIELD_NAME'] ?? '';

        $html = "<tr>
				<td>Выбор свойства:</td>
				<td>";
        $html .= "<select name='" . $strHTMLControlName['NAME'] . "[FIELD_NAME]'>";

        foreach ($fields as $field) {
            if ($value == $field['CODE']) {
                $html .= "<option value='" . $field['CODE'] . "' selected>" . $field['NAME'] . " (" . $field['CODE'] . ")</option>";
            } else {
                $html .= "<option value='" . $field['CODE'] . "'>" . $field['NAME'] . " (" . $field['CODE'] . ")</option>";
            }
        }
        $html .= "</select>";
        $html .= "</td></tr>";
        return $html;
    }


    public static function PrepareSettings($arProperty)
    {
        $arProperty['USER_TYPE_SETTINGS'] = array(
            'FIELD_NAME' => isset($arProperty['USER_TYPE_SETTINGS']['FIELD_NAME']) ? $arProperty['USER_TYPE_SETTINGS']['FIELD_NAME'] : ''
        );
        return $arProperty;
    }

    /**
     * Конвертация данных перед сохранением в БД
     * @param $arProperty
     * @param $value
     * @return mixed
     */
    public static function ConvertToDB($arProperty, $value)
    {
        return $value;
    }

    /**
     * Конвертируем данные при извлечении из БД
     * @param $arProperty
     * @param $value
     * @param string $format
     * @return mixed
     */
    public static function ConvertFromDB($arProperty, $value, $format = '')
    {
        return $value;
    }

    /**
     * Представление формы редактирования значения
     * @param $arProperty
     * @param $value
     * @param $arHtmlControl
     * @return string
     */
    public static function GetPropertyFieldHtml($arProperty, $value, $arHtmlControl)
    {
        if (empty($arProperty['LINK_IBLOCK_ID'])) {
            return 'Заполните настройки свойства';
        }

        if (empty($arProperty['USER_TYPE_SETTINGS']['FIELD_NAME'])) {
            return 'Заполните настройки свойства';
        }

        $arrField = [];
        $property_enums = CIBlockPropertyEnum::GetList(array("DEF" => "DESC", "SORT" => "ASC"), array("IBLOCK_ID" => $arProperty['LINK_IBLOCK_ID'], "CODE" => $arProperty['USER_TYPE_SETTINGS']['FIELD_NAME']));
        while ($enum_fields = $property_enums->GetNext()) {
            $arrField[$enum_fields["ID"]] = $enum_fields["VALUE"];
        }

        $fieldName = htmlspecialcharsbx($arHtmlControl['VALUE']);

        $select = '<select name="' . $fieldName . '" size="1"><option value="">(не установлено)</option>';
        foreach ($arrField as $key => $val) {
            if ($key == $value['VALUE']) {
                $select .= '<option value="' . $key . '" selected>' . $val . '</option>';
            } else {
                $select .= '<option  value="' . $key . '">' . $val . '</option>';
            }
        }
        $select .= '</select>';
        $html = '<div>';
        $html .= $select;
        $html .= '</div>';

        return $html;
    }

}