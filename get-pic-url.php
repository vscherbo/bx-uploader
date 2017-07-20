#!/usr/bin/env php
<?php
require("set-doc-root.php");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$shortopts  = "";
$shortopts .= "n:";  // name - обязательное значение (или i)
$shortopts .= "i:";  // xml_id - обязательное значение (или n)

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
        "ACTIVE" => "Y",
        "NAME" => $options["n"],
        // "!PROPERTY_607" => false,
    );
}

if ($options["i"] != "")
{
    $arFilter = array(
        "IBLOCK_ID" => "29",
        "ACTIVE" => "Y",
        "XML_ID" => $options["i"],
        "!PROPERTY_607" => false,
    );
}


$arSelect = Array("IBLOCK_ID", "ID", "NAME", "TIMESTAMP_X", "MODIFIED_BY", "PREVIEW_PICTURE" );

$el = new CIBlockElement;
$rsItems = $el->GetList(Array("SORT" => "ASC"), $arFilter, false, false, $arSelect);

while($ob = $rsItems->GetNextElement())
{
    $arFields = $ob->GetFields();
    $devName = $arFields["NAME"];
    $pic_url = "";
    $pic_name = "";
    //echo " Прибор=".$arFields["NAME"]."\n";
    //print_r($arFields);
    $img_path = CFile::GetPath($arFields["PREVIEW_PICTURE"]);
    echo $img_path."\n";
}

?>
