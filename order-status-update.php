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

if ($argv[2] == "")
{
    echo $argv[0]." ERROR: 2nd parameter Order_status is required.\n";
    exit(1);
}

if (!CSaleOrder::StatusOrder($argv[1], $argv[2])) 
   echo "Ошибка установки нового статуса заказа";
?>
