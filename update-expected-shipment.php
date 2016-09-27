#!/usr/bin/env php
<?php
echo "Start:".date("Y-m-d H:i:s ")."\n";
require("set-doc-root.php");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("catalog");

$shortopts  = "";
$shortopts .= "m::"; // код модификации, для которой обновляем срок
$shortopts .= "e::"; // ожидаемые поставки
$shortopts .= "q::"; // количество на складе
$shortopts .= "d::"; // стандартный срок поставки


$options = getopt($shortopts);
// var_dump($options);

/**/
if ($options["m"] == "") 
{
        fwrite(STDERR, $argv[0]." ERROR: parameter -m modification_code is required.\n" );
        exit(1);
}

$expected_shipments = '';
if ( $options["e"] != "" ) 
{
  echo "options_e=". $options["e"] ."\n";
  $expected_shipments = $options["e"];
}

$qnt_set = FALSE;
if ( $options["q"] != "" ) 
{
    //echo "options_q=". $options["q"] ."\n";
    if ( ! is_numeric($options["q"]) ) 
    {
            fwrite(STDERR, $argv[0]." ERROR: parameter -q is NOT numeric.\n" );
            exit(1);
    } else {
        $qnt = intval($options["q"]);
        //echo "qnt=". $qnt ."\n";
        $qnt_set = TRUE;
    }
}

$def_delivery = '';
if ( $options["d"] != "" ) 
{
  //echo "options_d=". $options["d"] ."\n";
  $def_delivery = $options["d"];
}


$cod_min = (double) $options["m"] - 0.1  ;
$cod_max = (double) $options["m"] + 0.1  ;
//echo "cod_min=". (double)$cod_min ."\n";
//echo "cod_max=". (double)$cod_max ."\n";
/**/

$arFilter = array(
    "IBLOCK_ID" => "30",
    "PROPERTY_COD" => intval($options["m"]),
    //"PROPERTY_COD" => round((double)$options["m"], 0),
    //"><PROPERTY_COD" => array($cod_min, $cod_max),
    "ACTIVE" => "Y",
);

//$arSelect = Array("ID", "NAME", "PROPERTY_*");
$arSelect = Array();
$rsItems = CIBlockElement::GetList(Array("SORT" => "ASC"), $arFilter, false, false, $arSelect);
echo "ib30:".date("Y-m-d H:i:s ")."\n";
$notFound = True;
while($ob = $rsItems->GetNextElement())
{
    $arFields = $ob->GetFields();
    $notFound = False;
    $ib30_id = $arFields["ID"];
    /**/
    echo "id=". $ib30_id
        . " name=" . $arFields["NAME"]
        . " section_id=" . $arFields["IBLOCK_SECTION_ID"]
        . " xml_id=". $arFields["XML_ID"]
        . "\n";
    /**/

    $arFilter29 = array(
        "IBLOCK_ID" => "29",
        "PROPERTY_MOD_SECTION_ID" => $arFields["IBLOCK_SECTION_ID"],
        "ACTIVE" => "Y",
    );
    $notFound29 = True;
    $rsItems29 = CIBlockElement::GetList(Array("SORT" => "ASC"), $arFilter29, false, false, array() );
    echo "ib29:".date("Y-m-d H:i:s ")."\n";
    while($ob29 = $rsItems29->GetNextElement())
    {
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
        if ( $expected_shipments != '' ) {
            $el30->SetPropertyValues($ib30_id, 30, $options["e"], "EXPECTED_SHIPMENTS");
        }

        if ( $def_delivery != '' ) {
            $el30->SetPropertyValues($ib30_id, 30, $options["d"], "DEFAULT_DELIVERY_PERIOD");
        }

        if ( $qnt_set ) {
            $res = CCatalogProduct::Update($ib30_id, array("QUANTITY" => $qnt));
            if (! ($res) ) {fwrite(STDERR, "Update ib30 _QUANTITY_ failed: ". $el30->LAST_ERROR . "\n" );}
        }

        $res = $el30->Update($ib30_id, array("MODIFIED_BY" => 6938));
        if (! ($res) ) {fwrite(STDERR, "Update ib30 failed: ". $el30->LAST_ERROR . "\n" );}
        //echo "after Update ib30:".date("Y-m-d H:i:s ")."\n";

        CSiteFinance::UpdateItemFinanceInfo($arFields29["ID"]);
        echo "after UpdateItemFinanceInfo:".date("Y-m-d H:i:s ")."\n";

        $el29 = new CIBlockElement;
        $res = $el29->Update($arFields29["ID"], array("MODIFIED_BY" => 6938));
        if (! ($res) ) {fwrite(STDERR, "Update ib29 failed: ". $el29->LAST_ERROR . "\n" );}
        echo "after Update ib29:".date("Y-m-d H:i:s ")."\n";
        /**/
    }
    if ($notFound29) {fwrite(STDERR, "Device with Active=Y and PROPERTY_MOD_SECTION_ID=[". $arFields["IBLOCK_SECTION_ID"] . "] not found\n");}

}

if ($notFound) {fwrite(STDERR, "Modification_code=[". $options["m"] . "] not found\n");}


//////////////////////////////////////////////////////////////////////////////////

// UpdateItemFinanceInfo($item_id)

?>
