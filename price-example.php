<?
$_SERVER["DOCUMENT_ROOT"] = "/home/bitrix/ext_www/kipspb2.arc.world";

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule("catalog");

$db_res = CPrice::GetList(
        array(),
        array(
                "PRODUCT_ID" => 739121,
                "CATALOG_GROUP_ID" => 1
            )
    );
if ($ar_res = $db_res->Fetch())
{
    echo CurrencyFormat($ar_res["PRICE"], $ar_res["CURRENCY"]);
}
else
{
    echo "Цена не найдена!";
}
?>

