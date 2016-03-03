#!/usr/bin/env php
<?php
require("set-doc-root.php");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$shortopts  = "";
$shortopts .= "m::"; // код модификации, для которой обновляем срок
$shortopts .= "t::"; // срок поставки - литерал из init_finance.php


$options = getopt($shortopts);
// var_dump($options);


if ($options["m"] == "") 
{
        fwrite(STDERR, $argv[0]." ERROR: parameter -m modification_code is required.\n" );
        exit(1);
}

$arPeriods = CSiteFinance::ReturnStatusConvertDictionary();


$arFilter = array(
    "IBLOCK_ID" => "30",
    "PROPERTY_COD" => $options["m"],
    "ACTIVE" => "Y",
);

//$arSelect = Array("ID", "NAME", "PROPERTY_*");
$arSelect = Array();
$rsItems = CIBlockElement::GetList(Array("SORT" => "ASC"), $arFilter, false, false, $arSelect);

while($ob = $rsItems->GetNextElement())
{
    $arFields = $ob->GetFields();
    echo "id=". $arFields["ID"]
        . " name=" . $arFields["NAME"]
        . " section_id=" . $arFields["IBLOCK_SECTION_ID"]
        . " xml_id=". $arFields["XML_ID"]
        . "\n";
    // print_r($arFields);

    $db_props = CIBlockElement::GetProperty(30, $arFields["ID"], array("sort" => "asc"), Array("CODE"=>"SKLAD"));
    if($ar_props = $db_props->Fetch()) {
        echo "срок=". $ar_props["VALUE"] . "\n";
        print_r( CSiteFinance::GetPropertyValuesForPeriodString($ar_props["VALUE"]) );

    } else {
        echo "property SKLAD not found\n";
    }

    $db_props = CIBlockElement::GetProperty(30, $arFields["ID"], array("sort" => "asc"), Array("CODE"=>"COD"));
    if($ar_props = $db_props->Fetch()) {
        echo "Код модификации=". $ar_props["VALUE"] . "\n";
    } else {
        echo "property COD not found\n";
    }

    $arFilter29 = array(
        "IBLOCK_ID" => "29",
        "PROPERTY_MOD_SECTION_ID" => $arFields["IBLOCK_SECTION_ID"],
        "ACTIVE" => "Y",
    );
    $rsItems29 = CIBlockElement::GetList(Array("SORT" => "ASC"), $arFilter29, false, false, array() );
    while($ob29 = $rsItems29->GetNextElement())
    {
        $arFields29 = $ob29->GetFields();
        echo "id=". $arFields29["ID"]
            . " name=" . $arFields29["NAME"]
            //. " section_id=" . $arFields["IBLOCK_SECTION_ID"]
            //. " xml_id=". $arFields["XML_ID"]
            . "\n";
        // print_r($arFields);
    }


    /**
    if ($options["t"] != "")
        $el->SetPropertyValues($arFields["ID"], 30, $options["t"], "SKLAD");

    CSiteFinance::UpdateItemFinanceInfo($arFields29["ID"]);
    $res = $el->Update($arFields29["ID"]);
    if ($res) { echo $arFields29["ID"]; }
    else {      fwrite(STDERR, "Update ib29 failed: ". $el->LAST_ERROR . "\n" );}
    **/

}



//////////////////////////////////////////////////////////////////////////////////

// UpdateItemFinanceInfo($item_id)

?>
