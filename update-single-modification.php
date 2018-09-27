#!/usr/bin/env php
<?php
require("set-doc-root.php");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("catalog");

$shortopts  = "";
$shortopts .= "m::"; // код модификации, для которой обновляем срок
$shortopts .= "t::"; // срок поставки - литерал из init_finance.php
$shortopts .= "q::"; // количество на складе


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

$qnt_set = FALSE;
if ( $options["q"] != "" )
{
    // echo "options_q=". $options["q"] ."\n";
    if ( ! is_numeric($options["q"]) )
    {
            fwrite(STDERR, $argv[0]." ERROR: parameter -q is NOT numeric.\n" );
            exit(1);
    } else {
        $res = preg_match('/[.,]/', $options["q"]);
        if ( 1 == $res ) { // delimiter found
            $qnt = floatval($options["q"]);
            $qnt_set = TRUE;
        } elseif ( 0 == $res ) { // not found
            $qnt = intval($options["q"]);
            $qnt_set = TRUE;
        }
        //echo "qnt=". $qnt ."\n";
    }
}

$cod_min = (double) $options["m"] - 0.1  ;
$cod_max = (double) $options["m"] + 0.1  ;
//echo "cod_min=". (double)$cod_min ."\n";
//echo "cod_max=". (double)$cod_max ."\n";

$arFilter = array(
    "IBLOCK_ID" => "30",
    //"PROPERTY_COD" => $options["m"],
    "><PROPERTY_COD" => array($cod_min, $cod_max),
    "ACTIVE" => "Y",
);

//$arSelect = Array("ID", "NAME", "PROPERTY_*");
$arSelect = Array();
$rsItems = CIBlockElement::GetList(Array("SORT" => "ASC"), $arFilter, false, false, $arSelect);

$mod_id_cnt = 0;
$notFound = True;
while($ob = $rsItems->GetNextElement())
{
    $mod_id_cnt++ ;
    if ($mod_id_cnt > 1 ) { // stop loop
        break;
    }

    $arFields = $ob->GetFields();
    $notFound = False;
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

    /**
    echo "arFilter29, "
	. "PROPERTY_MOD_SECTION_ID=" . $arFields["IBLOCK_SECTION_ID"]
        . "\n";
    **/
    $arFilter29 = array(
        "IBLOCK_ID" => "29",
        "PROPERTY_MOD_SECTION_ID" => $arFields["IBLOCK_SECTION_ID"],
        "ACTIVE" => "Y",
    );
    $ib29_cnt = 0;
    $notFound29 = True;
    $rsItems29 = CIBlockElement::GetList(Array("SORT" => "ASC"), $arFilter29, false, false, array() );
    while($ob29 = $rsItems29->GetNextElement())
    {
        $ib29_cnt++ ;
        if ($ib29_cnt > 1 ) { // stop loop
            break;
        }

        $notFound29 = False;
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

        if ( $qnt_set ) {
            $res = CCatalogProduct::Update($ib30_id, array("QUANTITY" => $qnt));
            if (! ($res) ) {fwrite(STDERR, "Update ib30 _QUANTITY_ failed: ". $el30->LAST_ERROR . "\n" );}
        }

        $el29 = new CIBlockElement;
        $res = $el29->Update($arFields29["ID"], array("ACTIVE"=>$arFields29["ACTIVE"], "MODIFIED_BY" => 6938));
        if (! ($res) ) {fwrite(STDERR, "Update ib29 failed: ". $el29->LAST_ERROR . "\n" );}
        /**/
    }
    if ($notFound29) {fwrite(STDERR, "Device with Active=Y and PROPERTY_MOD_SECTION_ID=[". $arFields["IBLOCK_SECTION_ID"] . "] not found\n");}
    if ($ib29_cnt > 1 ) {fwrite(STDERR, "More than 1 device with Active=Y and PROPERTY_MOD_SECTION_ID=[". $arFields["IBLOCK_SECTION_ID"] . "] found\n");}

}

if ($notFound) {fwrite(STDERR, "Modification_code=[". $options["m"] . "] not found\n");}
if ($mod_id_cnt > 1 ) {fwrite(STDERR, "More than 1 Modification_code=[". $options["m"] . "] found\n");}


//////////////////////////////////////////////////////////////////////////////////

// UpdateItemFinanceInfo($item_id)

?>
