<?

// $_SERVER["DOCUMENT_ROOT"] = "/home/bitrix/ext_www/kipspb2.arc.world";
require("set-doc-root.php");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");


$res = CIBlockProperty::GetByID(674, 29);
if($ar_res = $res->GetNext())
  //echo $ar_res['NAME'];
  print_r($ar_res);
?>
