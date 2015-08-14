#!/usr/bin/env php
<?php
require("set-doc-root.php");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if ($argv[1] == "")
{
    fwrite(STDERR, $argv[0]." ERROR: 1st parameter Model_name is required.\n");
    exit(1);
}

$arFilter = array(
    "IBLOCK_ID" => "30",
    "NAME" => $argv[1],
    "ACTIVE" => "Y",
);

$arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM");
$rsItems = CIBlockSection::GetList(Array("SORT" => "ASC"), $arFilter, false, false, $arSelect);

while($ob = $rsItems->GetNextElement())
{
 $arFields = $ob->GetFields();
 $ib30_id = $arFields["ID"];
 // echo $arFields["ID"];
}

//////////////////////////////////////////////////////////////////////////////////

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
    $ib29_prop674 = $arFields["PROPERTY_674_VALUE"];
}

if ($ib30_id == $ib29_prop674 || is_null($ib29_prop674) ) {
        echo $ib30_id;
} else {
	$arFilter = array(
	    "IBLOCK_ID" => "30",
	    "XML_ID" => $ib29_prop674,
	    "ACTIVE" => "Y",
	);

	$arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM");
	$rsItems = CIBlockSection::GetList(Array("SORT" => "ASC"), $arFilter, false, false, $arSelect);

	while($ob = $rsItems->GetNextElement())
	{
	 $arFields = $ob->GetFields();
	 $prop674_name = $arFields["NAME"];
	}
        fwrite(STDERR, "Model_name=". $argv[1] . ", PROP674=". $ib29_prop674 . " with Name=". $prop674_name . ", Modifications_byName_ID=". $ib30_id ."\n");
}

?>
