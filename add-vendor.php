#!/usr/bin/env php
<?php

if ( $_SERVER["DOCUMENT_ROOT"] == "" ) 
   $_SERVER["DOCUMENT_ROOT"] = "/home/bitrix/ext_www/kipspb2.arc.world";

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if ($argv[1] == "")
{
        echo $argv[0]." ERROR: 1st parameter Vendor_name is required.\n";
            exit(1);
}


if ($argv[2] == "")
{
        echo $argv[0]." ERROR: 2nd parameter Vendor_code is required.\n";
            exit(1);
}

$name = $argv[1];
$code = $argv[2];

$arFilter= array(
  "NAME" =>$name,
  "CODE" =>$code,
);

$found = false;
$el = new CIBlockElement;
$res = $el->GetList(Array("SORT"=>"ASC"), $arFilter);
while($ob = $res->GetNextElement())
{
 //$arFields = $ob->GetFields();
 $found = true;
}

if ( $found ) {
  echo "Error: Vendor ".$name." already exists";
  exit(2);
}

$rsUser = CUser::GetByLogin("devimport");
$arUser = $rsUser->Fetch();

$arVendor = array(
    "CREATED_BY"    => $arUser["ID"],
    //"MODIFIED_BY"    => $arUser["ID"],
    "IBLOCK_ID" => "55",
    "NAME" => $name,
    "CODE" => $code,
);


if ( $PRODUCT_ID = $el->Add($arVendor) )
  echo $PRODUCT_ID;
else
  echo "Error: ".$el->LAST_ERROR;

?>
