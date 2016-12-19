#!/usr/bin/env php
<?php
require("set-doc-root.php");
// $_SERVER["DOCUMENT_ROOT"] = "/home/bitrix/ext_www/kipspb2.arc.world";

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$arFilter = array(
    "IBLOCK_ID" => "29",
//    "NAME" => "EPC",
    //"MODIFIED_BY" => "6938",
    "CREATED_BY" => "6938",
    "ACTIVE" => "Y",
);

//674 - модификации
//675 - модификаторы
$arSelect = Array("IBLOCK_ID", "ID", "NAME", "TIMESTAMP_X", "PROPERTY_674", "PROPERTY_675" );

$el = new CIBlockElement;
$rsItems = $el->GetList(Array("SORT" => "ASC"), $arFilter, false, false, $arSelect);

while($ob = $rsItems->GetNextElement())
{
    $arFields = $ob->GetFields();
    $devName = $arFields["NAME"];
    //echo "\nПрибор=".$arFields["NAME"]."\n";
    //print_r($arFields);
    //echo $arFields["PROPERTY_675_VALUE"]."\n";
    if ($arFields["PROPERTY_675_VALUE"] != "") { 
	    $ib34_id = $arFields["PROPERTY_675_VALUE"];
	    $res = CIBlockElement::GetList(Array("SORT" => "ASC"), Array("IBLOCK_ID"=>"34", "ID"=>$ib34_id), false, false, Array("IBLOCK_ID", "ID", "NAME"));
	    if($ar_res = $res->GetNextElement()) {
	      $ar_flds = $ar_res->GetFields();
/*
	      if ($ar_flds && $devName != $ar_flds['NAME'])
		echo "Модификаторы=".$ar_flds['NAME']. " для прибора=". $devName ."\n" ;
*/
	    } else
	      echo "Модификаторы для прибора=".$devName .", ib34_id=". $ib34_id ." не найдены\n";
    }

    //echo "\n".$arFields["PROPERTY_674_VALUE"]."\n";
    if ($arFields["PROPERTY_674_VALUE"] != "") { 
	    $ib30_id = $arFields["PROPERTY_674_VALUE"];
        $res = CIBlockSection::GetList(Array("SORT" => "DESC"), Array("IBLOCK_ID"=>"30", "ID"=>$ib30_id), false, Array(), false); //Array("NAME"));
	    //while ($ar_res = $res->GetNextElement()) {
	    $not_found = true; 
	    while ($ar_res = $res->GetNext()) {
	      $not_found = false; 
	      if ($devName != $ar_res["NAME"])
		 echo "Секция цен={".$ar_res["NAME"] . "} для прибора={". $devName ."}\n" ;
	    }
	    if ($not_found) 
	      echo "####### Цены для прибора=".$devName .", ib30_id=". $ib30_id ." not found\n";
    }
}

?>
