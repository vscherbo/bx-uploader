#!/usr/bin/env php
<?php
require("set-doc-root.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("sale");

if ($argv[1] == "")
{
    fwrite(STDERR, $argv[0]." ERROR: 1st parameter Order_ID is required.");
    exit(1);
}

if ($argv[2] == "")
{
    fwrite(STDERR, $argv[0]." ERROR: 2nd parameter New_status is required.");
    exit(2);
}

if ($argv[3] == "force")
{
    $FORCE_MODE = True;
} else {
    $FORCE_MODE = False;
}

$ORDER_ID = $argv[1];
$NEW_STATUS = $argv[2];

// Get current status
if (!($arOrder = CSaleOrder::GetByID($ORDER_ID )))
{
   fwrite(STDERR, "Заказ с кодом ".$ORDER_ID." не найден");
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

$DO_UPDATE = (( $FORCE_MODE) || ( $orderStatus[$NEW_STATUS] > $orderStatus[$currentStatus] )) ;

// if equal do nothing
// OLD if ( $orderStatus[$NEW_STATUS] > $orderStatus[$currentStatus] ) {
if ( $DO_UPDATE ) {
    $USER = new CUser;
    if (!$USER->Authorize(6575)) {
        fwrite(STDERR, "order-status-safe-update: Ошибка авторизации") ;
        exit(4);
    }

	// Update
	if (!CSaleOrder::StatusOrder($ORDER_ID, $NEW_STATUS)) {
           fwrite(STDERR, "Ошибка установки нового статуса заказа");
           exit(5);
        }
} elseif ( $orderStatus[$NEW_STATUS] < $orderStatus[$currentStatus] ) {
        fwrite(STDERR, "Текщий статус заказа " .$currentStatus. " больше, чем новый ".$NEW_STATUS. "") ;
        exit(6);
}

?>
