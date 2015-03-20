#!/usr/bin/env php
<?php
$_SERVER["DOCUMENT_ROOT"] = "/home/bitrix/ext_www/kipspb2.arc.world";

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("catalog");

if ($argv[1] == "")
{
    echo $argv[0]." ERROR: 1st parameter Model_name is required.\n";
    exit(1);
}


// base price for catalog with id=30
$resPrTypes = CIBlockPriceTools::GetCatalogPrices(30, Array(0 => 'BASE'));
//print_r($resPrTypes["BASE"]);
echo "====" . $resPrTypes["BASE"]["ID"] . " \n";


$arFilter = array(
    "IBLOCK_ID" => "30",
    "NAME" => $argv[1],
    //"NAME" => "AR-HP350",
);
#$arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM");
$rsItems = CIBlockSection::GetList(Array("SORT" => "ASC"), $arFilter, false, false, $arSelect);
if ( is_null($rsItems) )
{
    echo "ERROR: rsItems is NULL\n";
    exit(2);
//} else {
//  print_r($rsItems);
}


while($ob = $rsItems->GetNextElement())
{
 $arFields = $ob->GetFields();
 echo "sect_id=".$arFields["ID"]."\n";
 $arFilter30 = array(
    "IBLOCK_ID" => "30",
    "SECTION_ID" => $arFields["ID"],
 );


 $res = CIBlockElement::GetList(Array("SORT" => "ASC"), $arFilter30, false, false, $arSelect30);
 if ( is_null($rsItems) )
 {
    echo "ERROR: res CIBlockElement::GetList is NULL\n";
    break; //exit(2);
 }
 while($item = $res->GetNextElement())
 {
   $arFieldsP = $item->GetFields();
   print_r($arFieldsP["NAME"]); print("\n");
   echo "PRODUCT_30_ID=".$arFieldsP["ID"]."\n";

   $db_res = CPrice::GetList(
        array(),
        array(
                "PRODUCT_ID" => $arFieldsP["ID"],
                "CATALOG_GROUP_ID" => $resPrTypes["BASE"]["ID"], //  1 // $prices = CIBlockPriceTools::GetCatalogPrices(30, array(0 => "BASE")); 
            )
    );
    if ($ar_res = $db_res->Fetch())
    {
       echo "Цена=".CurrencyFormat($ar_res["PRICE"], $ar_res["CURRENCY"])."\n";
       echo "PRICE_ID=".$ar_res["ID"]."\n";
       echo "CPrice::Delete(".$ar_res["ID"].");\n" ;
       CPrice::Delete($ar_res["ID"]);
       echo "CIBlockElement::Delete(".$arFieldsP["ID"].");\n" ;
       CIBlockElement::Delete($arFieldsP["ID"]);
       //print_r($item);
    }
    else
    {
       echo "Цена не найдена!\n";
    }

 }
}
?>

