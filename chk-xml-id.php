#!/usr/bin/env php
<?php
require("set-doc-root.php");
// $_SERVER["DOCUMENT_ROOT"] = "/home/bitrix/ext_www/kipspb2.arc.world";

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$arFilter = array(
    "IBLOCK_ID" => "29",
//    "NAME" => "EPC",
    "ACTIVE" => "Y",
);

$arSelect = Array("ID", "NAME", "TIMESTAMP_X", "MODIFIED_BY", "XML_ID" );

$el = new CIBlockElement;
$rsItems = $el->GetList(Array("SORT" => "ASC"), $arFilter, false, false, $arSelect);

while($ob = $rsItems->GetNextElement())
{
    $arFields = $ob->GetFields();
    $devName = $arFields["NAME"];
    //if ( $arFields["ID"] != $arFields["XML_ID"] ) 
    echo "\nПрибор=".$arFields["NAME"]."::ID=". $arFields["ID"] ."/XML_ID=". $arFields["XML_ID"] ."\n";

      //print_r($arFields);

}

?>
