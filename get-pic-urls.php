#!/usr/bin/env php
<?php
require("set-doc-root.php");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$shortopts  = "";
$shortopts .= "l:";  // name - обязательное значение (или i)

$options = getopt($shortopts);
// var_dump($options);


if ($options["l"] == "")
{
        fwrite(STDERR, $argv[0]." ERROR: parameter -l Model_name_List.\n" );
        exit(1);
}


$ar_dev_names = explode("^", $options["l"]);

if ($options["l"] != "")
{
    $arFilter = array(
        "IBLOCK_ID" => "29",
        "ACTIVE" => "Y",
        "NAME" => $ar_dev_names,
        // "NAME" => $options["n"],
        // "!PROPERTY_607" => false,
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
    echo $devName."^".$img_path."\n";
}

?>
