#!/usr/bin/env php
<?php
// $_SERVER["DOCUMENT_ROOT"] = "/home/bitrix/ext_www/kipspb2.arc.world";
require("set-doc-root.php");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if ($argv[1] == "")
{
    echo $argv[0]." ERROR: 1st parameter Model_name is required.\n";
    exit(1);
}

$arFilter = array(
    "IBLOCK_ID" => "34",
    "NAME" => $argv[1],
    "ACTIVE" => "Y",
//    "NAME" => "AR-5531-03",
);

$arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM");
$rsItems = CIBlockElement::GetList(Array("SORT" => "ASC"), $arFilter, false, false, $arSelect);

while($ob = $rsItems->GetNextElement())
{
 $arFields = $ob->GetFields();
 //print_r($arFields);
 print_r($arFields["ID"]);
}

?>
