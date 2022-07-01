#!/usr/bin/env php
<?php
// $_SERVER["DOCUMENT_ROOT"] = "/home/bitrix/ext_www/kipspb2.arc.world";
require("set-doc-root.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("sale");

if ($argv[1] == "")
{
    echo $argv[0]." ERROR: 1st parameter Order_ID is required.\n";
    exit(1);
}
$ORDER_ID = $argv[1];

if (!($arOrder = CSaleOrder::GetByID($ORDER_ID)))
{
   echo "Заказ с кодом ".$ORDER_ID." не найден";
}
else
{
   //print_r($arOrder["STATUS_ID"]);
   print_r($arOrder);
   // echo $arOrder["STATUS_ID"] ; // . "\n";
}
?>
