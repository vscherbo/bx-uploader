#!/usr/bin/env php
<?php
require("set-doc-root.php");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if ($argv[1] == "")
{
    fwrite(STDERR, $argv[0]." ERROR: 1st parameter Model_name is required.\n" );
    exit(1);
}

$arFilter = array(
    "IBLOCK_ID" => "29",
    "NAME" => $argv[1],
    // "NAME" => "AC-8M",
    // "NAME" => "AR-2W12",
    "ACTIVE" => "Y",
);

$arSelect = Array("ID", "NAME", "TIMESTAMP_X", "MODIFIED_BY", "IBLOCK_SECTION_ID");

$el = new CIBlockElement;
$rsItems = $el->GetList(Array("SORT" => "ASC"), $arFilter, false, false, $arSelect);

while($ob = $rsItems->GetNextElement())
{
    $arFields = $ob->GetFields();
    $devName = $arFields["NAME"];
    // echo "\nПрибор=".$arFields["NAME"]."\n";
    echo "XML_ID=".$arFields["ID"]."^";
    //print_r($arFields);

    $db_res = $el->GetElementGroups($arFields["ID"], true); //, Array("NAME"));

    $not_found = true; 
    while ($ar_res = $db_res->Fetch()) {
      $not_found = false;
      // echo "GRP_ID=". $ar_res["ID"] ."\n";
      // echo "IBLOCK_ID=". $ar_res["IBLOCK_ID"] ."\n";
      // echo "IBLOCK_SECTION_ID=". $ar_res["IBLOCK_SECTION_ID"] ."\n";
      $nav = CIBlockSection::GetNavChain($ar_res["IBLOCK_ID"], $ar_res["IBLOCK_SECTION_ID"]);
      while ($arNav=$nav->GetNext()):
        echo $arNav["NAME"]. "(" .$arNav["ID"].  ")" ."->";
        // print_r($arNav);
      endwhile; 
      // echo "\n";
      echo $ar_res["NAME"] . "(" .$ar_res["ID"].  ")" . "^" ;
      // echo "Секция прибора=".$ar_res["NAME"] . "(" .$ar_res["ID"].  ")" . "\n" ;
    }
    if ($not_found) 
      echo "Прибор=".$devName .", секции id=". $arFields["ID"] ." not found";

}

?>
