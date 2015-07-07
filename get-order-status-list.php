#!/usr/bin/env php
<?php
// $_SERVER["DOCUMENT_ROOT"] = "/home/bitrix/ext_www/kipspb2.arc.world";
require("set-doc-root.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("sale");

$rsItems = CSaleStatus::GetList(array("SORT"=>"ASC"), array("LID" => "ru"), false, false, array("ID") );

$cnt = 1;
while($ob = $rsItems->Fetch())        // GetNextElement())
{
    // $orderStatus[$cnt++] = $ob["ID"];
    $orderStatus[ $ob["ID"] ] = $cnt++ ;
}

print_r($orderStatus);
echo "\n";

?>
