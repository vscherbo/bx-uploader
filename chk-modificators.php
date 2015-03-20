#!/usr/bin/env php
<?php
$_SERVER["DOCUMENT_ROOT"] = "/home/bitrix/ext_www/kipspb2.arc.world";

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$arFilter = array(
    "IBLOCK_ID" => "34",
    //"NAME" => "EPC",
    "ID" => "619817",
    "ACTIVE" => "Y",
);

$arSelect = Array("ID", "NAME", "TIMESTAMP_X", "MODIFIED_BY", "PROPERTY_*" );

$el = new CIBlockElement;
$rsItems = $el->GetList(Array("SORT" => "ASC"), $arFilter, false, false, $arSelect);

while($ob = $rsItems->GetNextElement())
{
    $arFields = $ob->GetFields();
    echo $arFields["NAME"]."\n";
    print_r($arFields);
    //echo $arFields["PROPERTY_675_VALUE"]."\n";

}

?>
