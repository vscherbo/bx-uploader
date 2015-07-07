#!/usr/bin/env php
<?php
// $_SERVER["DOCUMENT_ROOT"] = "/home/bitrix/ext_www/kipspb2.arc.world";
require("set-doc-root.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("sale");

$STDERR = fopen('php://stderr', 'w+');

if ($argv[1] == "")
{
    fwrite($STDERR, $argv[0]." ERROR: 1st parameter Order_ID is required.");
    exit(1);
}

if ($argv[2] == "")
{
    fwrite($STDERR, $argv[0]." ERROR: 2nd parameter New_status is required.");
    exit(2);
}

$ORDER_ID = $argv[1];
$NEW_STATUS = $argv[2];

// Get current status
if (!($arOrder = CSaleOrder::GetByID($ORDER_ID )))
{
   fwrite($STDERR, "Заказ с кодом ".$ORDER_ID." не найден");
   exit(3);
}
else
{
   $currentStatus = $arOrder["STATUS_ID"] ;
}

// Get sorted list
$rsItems = CSaleStatus::GetList(array("SORT"=>"ASC"), array("LID" => "ru"), false, false, array("ID") );
$cnt = 1;
while($ob = $rsItems->Fetch())
{
    $orderStatus[ $ob["ID"] ] = $cnt++ ;
}

// if equal do nothing
if ( $orderStatus[$NEW_STATUS] > $orderStatus[$currentStatus] ) {
	// Update
	if (!CSaleOrder::StatusOrder($ORDER_ID, $NEW_STATUS)) {
           fwrite($STDERR, "Ошибка установки нового статуса заказа");
           exit(4);
        }
} elseif ( $orderStatus[$NEW_STATUS] < $orderStatus[$currentStatus] ) {
        fwrite($STDERR, "Текщий статус заказа " .$currentStatus. " больше, чем новый ".$NEW_STATUS. "") ;
        exit(5);
}

fclose($STDERR);
?>
