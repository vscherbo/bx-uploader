#!/usr/bin/env php
<?php
$_SERVER["DOCUMENT_ROOT"] = "/home/bitrix/ext_www/kipspb2.arc.world";

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$arFilter = array(
    "IBLOCK_ID" => "29",
    //"NAME" => "AC-8M",
    "NAME" => "AR-2W12",
    "ACTIVE" => "Y",
);

$arSelect = Array("ID", "NAME", "TIMESTAMP_X", "MODIFIED_BY", "IBLOCK_SECTION_ID");

$el = new CIBlockElement;
$rsItems = $el->GetList(Array("SORT" => "ASC"), $arFilter, false, false, $arSelect);

while($ob = $rsItems->GetNextElement())
{
    $arFields = $ob->GetFields();
    $devName = $arFields["NAME"];
    echo "\nПрибор=".$arFields["NAME"]."\n";
    //print_r($arFields);

    $db_res = $el->GetElementGroups($arFields["ID"], true); //, Array("NAME"));

// Обнуление секций
    //$arSects = array(); // массив кодов групп
    //CIBlockElement::SetElementSection($ID, $arSects);

    $not_found = true; 
    while ($ar_res = $db_res->Fetch()) {
      $not_found = false;
      echo "GRP_ID=". $ar_res["ID"] ."\n";
      $nav = CIBlockSection::GetNavChain($ar_res["IBLOCK_ID"], $ar_res["IBLOCK_SECTION_ID"]);
      while ($arNav=$nav->GetNext()):
        echo $arNav["NAME"]."->";
      endwhile; 
      //echo "\n";
      echo "Секция=".$ar_res["NAME"] . "\n" ;
      //echo "  СекцияID=".$ar_res["ID"] ." РодСекцияID=".$ar_res["IBLOCK_SECTION_ID"] . " DepthLevel=".$ar_res["DEPTH_LEVEL"] . "\n" ;
    }
    if ($not_found) 
      echo "Прибор=".$devName .", секции id=". $arFields["ID"] ." not found\n";


}

?>
