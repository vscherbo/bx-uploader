<?php
$_SERVER["DOCUMENT_ROOT"] = "/home/bitrix/ext_www/kipspb2.arc.world";

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$arOrder = Array("NAME"=>"ASC");
      //$arFilter = Array("IBLOCK_ID"=>30,"ID"=>739120);
      $arFilter = Array("IBLOCK_ID"=>30,"SECTION_ID"=>10601);
      //$arGroupBy = false;
      //$arNavStartParams = false;
      //$arSelectedFields = false;

$prices = CIBlockPriceTools::GetCatalogPrices(1, array(0 => "BASE")); 
foreach($prices as $key => $value) 
{ 
$arSelect[] = $value["SELECT"]; 
$arFilter["CATALOG_SHOP_QUANTITY_".$value["ID"]] = $arParams["SHOW_PRICE_COUNT"]; 
}
               
      $itemList = CIBlockElement::GetList($arOrder,$arFilter,$arGroupBy,$arNavStartParams,$arSelect);
      
      while($item = $itemList->GetNextElement())
      { 
            //$prop['INGREDIENTS'] = $item->GetProperty("INGREDIENTS");
            //$prop['NOMEN']       = $item->GetProperty("CML2_TRAITS");
            //$prop['SEC_SITE']    = $item->GetProperty("SEC_SITE");
            //$prop['PDF']         = $item->GetProperty("PDF");
            //$prop['FORMAT']      = $item->GetProperty("FORMAT");
            
            $item = $item->GetFields();

            //==============================================
            $arParams["PRICE_VAT_INCLUDE"] = $arParams["PRICE_VAT_INCLUDE"] !== "N";
            $item["PRICES_CAT"] = CIBlockPriceTools::GetCatalogPrices(27, $arParams["PRICE_CODE"]);
            $item["PRICES"] = CIBlockPriceTools::GetItemPrices(30, $item["PRICES_CAT"], $item, $arParams['PRICE_VAT_INCLUDE']);
            print_r($item);
/*
            echo $item["NAME"];
            echo " ".$item["CATALOG_PRICE_ID_1"];
            echo " ".$item["CATALOG_PRICE_1"];
            echo " ".$item["CATALOG_PRICE_CURRENCY_1"]."\n";
*/
            //==============================================
      }
?>
