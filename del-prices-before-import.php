#!/usr/bin/env php
<?php
// $_SERVER["DOCUMENT_ROOT"] = "/home/bitrix/ext_www/kipspb2.arc.world";
require("set-doc-root.php");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("catalog");

if ($argv[1] == "")
{
    //echo $argv[0]." ERROR: 1st parameter XML_ID is required.\n";
    fwrite(STDERR, $argv[0]." ERROR: 1st parameter XML_ID is required.\n");
    exit(1);
}

$arFilter = array(
    "IBLOCK_ID" => "29",
    "XML_ID" => $argv[1],
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
    //$devName = $arFields["NAME"];
    $prices_id = $arFields["PROPERTY_674_VALUE"];
}



// base price for catalog with id=30
$resPrTypes = CIBlockPriceTools::GetCatalogPrices(30, Array(0 => 'BASE'));
//echo "====" . $resPrTypes["BASE"]["ID"] . " \n";


$arFilter30 = array(
    "IBLOCK_ID" => "30",
    "SECTION_ID" => $prices_id,
);
$res = CIBlockElement::GetList(Array("SORT" => "ASC"), $arFilter30, false, false, $arSelect30);
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
       CPrice::Delete($ar_res["ID"]);
       CIBlockElement::Delete($arFieldsP["ID"]);
    }

} // end loop
?>
