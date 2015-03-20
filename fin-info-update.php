#!/usr/bin/env php
<?php
$_SERVER["DOCUMENT_ROOT"] = "/home/bitrix/ext_www/kipspb2.arc.world";

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if ($argv[1] == "")
{
        echo $argv[0]." ERROR: 1st parameter Model_name is required.\n";
            exit(1);
}

$arFilter = array(
    "IBLOCK_ID" => "29",
    "NAME" => $argv[1],
    //"NAME" => "AR-HP350",
);

//$arSelect = Array("ID", "NAME", "TIMESTAMP_X", "MODIFIED_BY", "PROPERTY_VALUES");

$el = new CIBlockElement;
$rsItems = $el->GetList(Array("SORT" => "ASC"), $arFilter, false, false, $arSelect);


while($ob = $rsItems->GetNextElement())
{
    $arFields = $ob->GetFields();
    //echo $arFields["NAME"]."\n";

    if ($argv[2] != "")
        $el->SetPropertyValues($arFields["ID"], 29, $argv[2], "MOD_ITEM_ID");

    if ($argv[3] != "")
        $el->SetPropertyValues($arFields["ID"], 29, $argv[3], "MOD_SECTION_ID");

    CSiteFinance::UpdateItemFinanceInfo($arFields["ID"]);
    $res = $el->Update($arFields["ID"]);
    if ($res) { echo $arFields["ID"]; }
    else {      echo "False\n";}
}

?>
