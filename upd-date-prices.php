#!/usr/bin/env php
<?php
// $_SERVER["DOCUMENT_ROOT"] = "/home/bitrix/ext_www/kipspb2.arc.world";
require("set-doc-root.php");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("catalog");

if ($argv[1] == "")
{
    echo $argv[0]." ERROR: 1st parameter Model_name is required.\n";
    exit(1);
}

$arFilter = array(
    "IBLOCK_ID" => "29",
    "NAME" => $argv[1],
    "ACTIVE" => "Y",
);

//674 - модификации
//675 - модификаторы
$arSelect = Array("ID", "NAME", "TIMESTAMP_X", "MODIFIED_BY", "PROPERTY_674", "PROPERTY_675" );

$el = new CIBlockElement;
$rsItems = $el->GetList(Array("SORT" => "ASC"), $arFilter, false, false, $arSelect);


while($ob = $rsItems->GetNextElement())
{
    $arFields = $ob->GetFields();
    //print_r($arFields);
    //$devName = $arFields["NAME"];
    $prices_id = $arFields["PROPERTY_674_VALUE"];
}



// base price for catalog with id=30
$resPrTypes = CIBlockPriceTools::GetCatalogPrices(30, Array(0 => 'BASE'));
//echo "====" . $resPrTypes["BASE"]["ID"] . " \n";

echo "price_section_id=". $prices_id . "\n";

$arFilter30 = array(
    "IBLOCK_ID" => "30",
    "SECTION_ID" => $prices_id,
);
$res = CIBlockElement::GetList(Array("SORT" => "ASC"), $arFilter30, false, false, $arSelect30);
if ( is_null($rsItems) )
{ 
    echo "ERROR: An empty list of prices in Section=".$prices_id ."\n";
    exit(2);
}

// loop modification in found section
while($item = $res->GetNextElement())
{
   $arFieldsP = $item->GetFields();

   $db_res = CPrice::GetList(
	array(),
	array(
		"PRODUCT_ID" => $arFieldsP["ID"],
		"CATALOG_GROUP_ID" => $resPrTypes["BASE"]["ID"], // 1
	    )
    );
    if ($ar_res = $db_res->Fetch())
    {
       //echo "price_id=". $ar_res["PRODUCT_ID"] ."\n";

       $el30 = new CIBlockElement;
       //$res30 = $el30->GetByID($ar_res["PRODUCT_ID"]);
       //if ( $arRes = $res30->GetNext() )
       //   echo $arRes['NAME'] . "\n" ;
       $el30->Update($ar_res["PRODUCT_ID"]);
       
    }

} // end loop
?>
