#!/usr/bin/env php
<?php
// $_SERVER["DOCUMENT_ROOT"] = "/home/bitrix/ext_www/kipspb2.arc.world";
require("set-doc-root.php");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$shortopts  = "";
$shortopts .= "n:";  // name - обязательное значение (или i)
$shortopts .= "i:";  // xml_id - обязательное значение (или n)
$shortopts .= "m::"; // id раздела модификаторов - необязательное значение
$shortopts .= "p::"; // id раздела цен-сроков - необязательное значение


$options = getopt($shortopts);
// var_dump($options);


if ( ($options["n"] != "") && ($options["i"] != "")  )
{
        fwrite(STDERR, $argv[0]." ERROR: only one parameter -n Model_name OR -i XML_ID allowed.\n" );
        exit(1);
}

if ( ($options["n"] == "") && ($options["i"] == "")  )
{
        fwrite(STDERR, $argv[0]." ERROR: parameter -n Model_name OR -i XML_ID is required.\n" );
        exit(1);
}


if ($options["n"] != "")
{
	$arFilter = array(
	    "IBLOCK_ID" => "29",
        "NAME" => $options["n"],
  //2020      "ACTIVE" => "Y",
	//    "XML_ID" => $options["i"],
	);
}

if ($options["i"] != "")
{
	$arFilter = array(
	    "IBLOCK_ID" => "29",
//2020        "ACTIVE" => "Y",
	    "XML_ID" => $options["i"],
	);
}

//$arSelect = Array("IBLOCK_ID", "ID", "NAME", "TIMESTAMP_X", "MODIFIED_BY", "PROPERTY_VALUES");

$el = new CIBlockElement;
$rsItems = $el->GetList(Array("SORT" => "ASC"), $arFilter, false, false, $arSelect);
// print_r($rsItems);

while($ob = $rsItems->GetNextElement())
{
    $arFields = $ob->GetFields();
    // echo $arFields["NAME"]."\n";
    // echo $arFields["ID"]."\n";

    if ($options["m"] != "")
        $el->SetPropertyValues($arFields["ID"], 29, $options["m"], "MOD_ITEM_ID");

    if ($options["p"] != "")
        $el->SetPropertyValues($arFields["ID"], 29, $options["p"], "MOD_SECTION_ID");

    CSiteFinance::UpdateItemFinanceInfo($arFields["ID"]);
    //$arFiledsUpdate = $arFileds;
    //$arFiledsUpdate["MODIFIED_BY"] = 6938;
    $res = $el->Update($arFields["ID"], array("MODIFIED_BY" => 6938));
    if ($res) { echo $arFields["ID"]; }
    else {      fwrite(STDERR, "Update ib29 failed: ". $el->LAST_ERROR . "\n" );}
}

?>
