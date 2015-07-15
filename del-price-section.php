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
    //$devName = $arFields["NAME"];
    $prices_id = $arFields["PROPERTY_674_VALUE"];
}

$arFilter30 = array(
    "IBLOCK_ID" => "30",
    "XML_ID" => $prices_id,
);
$arSelect30 = Array("ID", "NAME", "DATE_ACTIVE_FROM");
$rsItems = CIBlockSection::GetList(Array("SORT" => "ASC"), $arFilter30, false, false, $arSelect30);
if ( is_null($rsItems) )
{
    fwrite(STDERR, $argv[0]." ERROR: Section not found:". var_export($arFilter30) ."\n");
    exit(2);
}
while($ob = $rsItems->GetNextElement())
{
   $arSect = $ob->GetFields();
   // echo ">>>>>> Item:\n";
   // print_r($arSect);

   $DB->StartTransaction();
   if(!CIBlockSection::Delete($arSect["ID"]))
   {
        $strWarning .= 'Error.';
        $DB->Rollback();
    	fwrite(STDERR, $argv[0]." ERROR deleting:". var_export($arFilter30) ."\n" );
	exit(3);
   }
   else
       $DB->Commit();

}

?>
