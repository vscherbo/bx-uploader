#!/usr/bin/env php
<?php
// $_SERVER["DOCUMENT_ROOT"] = "/home/bitrix/ext_www/kipspb2.arc.world";
require("set-doc-root.php");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if ($argv[1] == "")
{
        echo $argv[0]." ERROR: 1st parameter XML_ID is required.\n";
            exit(1);
}

$arFilter = array(
    "IBLOCK_ID" => "29",
    "ID" => $argv[1],
);

//$arSelect = Array("IBLOCK_ID", "ID", "NAME", "TIMESTAMP_X", "MODIFIED_BY", "PROPERTY_VALUES");

$el = new CIBlockElement;
$rsItems = $el->GetList(Array("SORT" => "ASC"), $arFilter, false, false, $arSelect);
// print_r($rsItems);

while($ob = $rsItems->GetNextElement())
{
    $arFields = $ob->GetFields();
    //echo $arFields["ID"].  ", ACTIVE=". $arFields["ACTIVE"]  ."\n";
    //$arFields["ACTIVE"] = "Y";
    //echo $arFields["IBLOCK_ID"].  ", ACTIVE=". $arFields["ACTIVE"]  ."\n";

    // $el29 = new CIBlockElement;
    $res = $el->Update($arFields["ID"], array("ACTIVE" => "Y") );
    if ($res) { echo $arFields["ID"]; }
    else {      echo "False\n";}
}

?>
