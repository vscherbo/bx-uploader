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

if ($options["t"] == "") 
{
        fwrite(STDERR, $argv[0]." ERROR: parameter -t delivery_period is required.\n" );
        exit(1);
}

//$arPeriods = CSiteFinance::ReturnStatusConvertDictionary();


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
    $ib30_id = $arFields["ID"];
    /**
    echo "id=". $ib30_id
        . " name=" . $arFields["NAME"]
        . " section_id=" . $arFields["IBLOCK_SECTION_ID"]
        . " xml_id=". $arFields["XML_ID"]
        . "\n";
        **/
    // print_r($arFields);

    $db_props = CIBlockElement::GetProperty(30, $ib30_id, array("sort" => "asc"), Array("CODE"=>"SKLAD"));
    if (! ($ar_props = $db_props->Fetch()) ) {
        fwrite(STDERR, "property SKLAD not found\n");
    } /**
    else {
        echo "срок=". $ar_props["VALUE"] . "\n";
    } **/

    $db_props = CIBlockElement::GetProperty(30, $ib30_id, array("sort" => "asc"), Array("CODE"=>"COD"));
    if (! ($ar_props = $db_props->Fetch()) ) {
        fwrite(STDERR, "property COD not found\n");
    } /**
    else {
        echo "Код модификации=". $ar_props["VALUE"] . "\n";
    } **/

    $arFilter29 = array(
        "IBLOCK_ID" => "29",
        "PROPERTY_MOD_SECTION_ID" => $arFields["IBLOCK_SECTION_ID"],
        "ACTIVE" => "Y",
    );
    $rsItems29 = CIBlockElement::GetList(Array("SORT" => "ASC"), $arFilter29, false, false, array() );
    while($ob29 = $rsItems29->GetNextElement())
    {
        $arFields29 = $ob29->GetFields();
        /**
        echo "id=". $arFields29["ID"]
            . " name=" . $arFields29["NAME"]
            //. " section_id=" . $arFields["IBLOCK_SECTION_ID"]
            //. " xml_id=". $arFields["XML_ID"]
            . "\n";
        // print_r($arFields);
        print_r ($options["t"]); echo "\n";
        **/
        $el30 = new CIBlockElement;
        $el30->SetPropertyValues($ib30_id, 30, $options["t"], "SKLAD");
        $res = $el30->Update($ib30_id, array("MODIFIED_BY" => 6938));
        if (! ($res) ) {fwrite(STDERR, "Update ib30 failed: ". $el30->LAST_ERROR . "\n" );}
    
        CSiteFinance::UpdateItemFinanceInfo($arFields29["ID"]);

        $el29 = new CIBlockElement;
        $res = $el29->Update($arFields29["ID"], array("MODIFIED_BY" => 6938));
        if (! ($res) ) {fwrite(STDERR, "Update ib29 failed: ". $el29->LAST_ERROR . "\n" );}
        /**/
    }



}



//////////////////////////////////////////////////////////////////////////////////

// UpdateItemFinanceInfo($item_id)

?>
