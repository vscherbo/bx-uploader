#!/usr/bin/env php
<?php
// $_SERVER["DOCUMENT_ROOT"] = "/home/bitrix/ext_www/kipspb2.arc.world";
require("set-doc-root.php");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if ($argv[2] == "")
{
	// 2nd parameter group_list is empty, i.e "no changes". Exit
	exit(0);
}

if ($argv[1] == "")
{
	echo $argv[0]." ERROR: 1st parameter Model_name is required.\n";
	//echo $argv[0]." ERROR: 1st parameter XML_ID is required.\n";
	exit(1);
}

$arFilter = array(
    "IBLOCK_ID" => "29",
    "NAME" => $argv[1],
    "ACTIVE" => "Y",
);

$arSelect = Array("ID", "NAME", "TIMESTAMP_X", "MODIFIED_BY", "IBLOCK_SECTION_ID");

$el = new CIBlockElement;
$rsItems = $el->GetList(Array("SORT" => "ASC"), $arFilter, false, false, $arSelect);

while($ob = $rsItems->GetNextElement())
{
    $arFields = $ob->GetFields();
    //$devName = $arFields["NAME"];
    //echo "\nПрибор=".$arFields["NAME"]."\n";
    //print_r($arFields);

    $arSects = explode(',', $argv[2]) ; // массив кодов групп
    //$arSects = (6592);
    $res = $el->SetElementSection($arFields["ID"], $arSects);
    print_r($res);
}

?>
