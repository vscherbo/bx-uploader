#!/usr/bin/env php
<?php
// $_SERVER["DOCUMENT_ROOT"] = "/home/bitrix/ext_www/kipspb2.arc.world";
require("set-doc-root.php");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
//CModule::IncludeModule("catalog");

CModule::IncludeModule("sale");

$rsItems = CIBlockElement::GetList(Array(), Array("IBLOCK_ID" => 56, ">PROPERTY_ORDER_ID" => 1), false, false, Array("IBLOCK_ID", "ID", "IBLOCK_ID", "NAME", "PROPERTY_ORDER_ID", "PROPERTY_PAYMENT_FLAG", "PROPERTY_DELIVERY_FLAG"));
while($arItem = $rsItems->GetNext())
{
	$order_id = $arItem["PROPERTY_ORDER_ID_VALUE"];
	//debug($order_id, 'order_id');

	$arOrder = CSaleOrder::GetByID($order_id);
	if ($arOrder["ID"] != $order_id)
		continue;

	//debug($arItem, 'arItem');
	//debug($arOrder, 'arOrder');

	if ($arItem["PROPERTY_PAYMENT_FLAG_VALUE"] == 1 && $arOrder["CANCELED"] != "Y")
	{
		if (! $arOrder["PAYED"] != "Y") {
	           //CSaleOrder::PayOrder($order_id, "Y", false, false);
		   print_r($arOrder);
			$toBeComparedDate = '2014-12-12';
			//$expiry = (new DateTime($toBeComparedDate))->format('Y-m-d');

			// if ( $arOrder["DATE_UPDATE_FORMAT"]  > strtotime($expiry)); //false or true

			//if ( strtotime($arOrder["DATE_UPDATE_FORMAT"])  > strtotime($toBeComparedDate) ) {
			if ( strtotime($arOrder["DATE_UPDATE_FORMAT"])  > strtotime('2014-12-12') ) {
           		   echo "Resent=". $arOrder["DATE_UPDATE_FORMAT"] ."\n" ;
//                        } else {
//           		   echo $arOrder["DATE_UPDATE_FORMAT"] ."\n" ;
//                           var_dump ( strtotime($arOrder["DATE_UPDATE_FORMAT"]) );
//			   var_dump ( strtotime($toBeComparedDate) );
                        }
		   break;
		   // CSaleOrder::StatusOrder($order_id, "A"); //A - Оплачен
                }
	}

}

?>

