#!/usr/bin/env php
<?php
/*** Only for DEBUG memory_limit errors
@ini_set("memory_limit", "16M");
***/

require("set-doc-root.php");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("catalog");

if ($argv[1] == "")
{
    fwrite(STDERR, $argv[0]." ERROR: 1st parameter Model_name is required.\n");
    exit(1);
}

$arFilter = array(
    "IBLOCK_ID" => "29",
    "NAME" => $argv[1],
    "ACTIVE" => "Y",
);

//674 - модификации
//675 - модификаторы
$arSelect = Array("IBLOCK_ID", "ID", "NAME", "TIMESTAMP_X", "MODIFIED_BY", "PROPERTY_674", "PROPERTY_675" );

$el = new CIBlockElement;
$rsItems = $el->GetList(Array("SORT" => "ASC"), $arFilter, false, false, $arSelect);

$notFound = true;

while($ob = $rsItems->GetNextElement())
{
    $arFields = $ob->GetFields();
    $notFound = false;
    //$devName = $arFields["NAME"];
    //print_r($arFields);
    $prices_id = $arFields["PROPERTY_674_VALUE"];
}

if ($notFound) {
    fwrite(STDERR, $argv[0]." ERROR: Device not found:". var_export($arFilter) ."\n");
    exit(2);
}

if (is_null($prices_id)) { //
    echo "PROPERTY_674 is empty. Exit\n";
    exit(0);
}

$arFilter30 = array(
    "IBLOCK_ID" => "30",
    "ID" => $prices_id,
    "ACTIVE" => "Y",
);
$arSelect30 = Array("ID", "NAME", "DATE_ACTIVE_FROM");
$rsItems = CIBlockSection::GetList(Array("SORT" => "ASC"), $arFilter30, false, $arSelect30, false);

$noElements = true;
while($ob = $rsItems->GetNextElement())
{
   $arSect = $ob->GetFields();
   echo ">>>>>> Section_ID:";
   print_r($arSect["ID"]);
   echo "\n";

   $noElements = false;
   $DB->StartTransaction();
   if(!CIBlockSection::Delete($arSect["ID"]))
   {
        $strWarning .= 'Error.';
        $DB->Rollback();
    	fwrite(STDERR, $argv[0]." ERROR deleting:". var_export($arFilter30) ."\n" );
	exit(3);
   }
   else {
        $DB->Commit();
        echo "Delete commited\n";
/**/
        // Check if deleted
        $arFilter30 = array(
         "IBLOCK_ID" => "30",
         "ID" => $arSect["ID"],
         "ACTIVE" => "Y",
        );
        $arSelect30 = Array("ID", "NAME", "DATE_ACTIVE_FROM");
        $rsItems = CIBlockSection::GetList(Array("SORT" => "ASC"), $arFilter30, false, $arSelect30, false);
        $noElements = true;
        while($ob = $rsItems->GetNextElement())
        {
           $arSect = $ob->GetFields();
           $noElements = false;
        }
        if ($noElements) {
           echo "Deleted section not found. Ok.\n";
        } else {  
           fwrite(STDERR, $argv[0]." ERROR: Deleted section found:". var_export($arFilter30) ."\n");
           exit(2);
        }
/**/
   }

}

/*
if ( $noElements) {
    	fwrite(STDERR, $argv[0]." WARNING: Prices-elements not found: ". var_export($arFilter30) ."\n" );
}
*/

?>
