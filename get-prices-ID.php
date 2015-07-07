#!/usr/bin/env php
<?php
// $_SERVER["DOCUMENT_ROOT"] = "/home/bitrix/ext_www/kipspb2.arc.world";
require("set-doc-root.php");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if ($argv[1] == "")
{
    fwrite(STDERR, $argv[0]." ERROR: 1st parameter Model_name is required.\n" );
    exit(1);
}

$arFilter = array(
    "IBLOCK_ID" => "30",
    "NAME" => $argv[1],
    "ACTIVE" => "Y",
    //"NAME" => "AR-HP350",
);

$arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM");
$rsItems = CIBlockSection::GetList(Array("SORT" => "ASC"), $arFilter, false, false, $arSelect);

while($ob = $rsItems->GetNextElement())
{
 $arFields = $ob->GetFields();
 echo $arFields["ID"];
}

?>
