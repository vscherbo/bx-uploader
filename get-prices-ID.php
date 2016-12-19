#!/usr/bin/env php
<?php
require("set-doc-root.php");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if ($argv[1] == "")
{
    fwrite(STDERR, $argv[0]." ERROR: 1st parameter Model_name is required.\n");
    exit(1);
}

$arFilter = array(
    "IBLOCK_ID" => "30",
    "NAME" => $argv[1],
    "ACTIVE" => "Y",
);

$arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM");
$rsItems = CIBlockSection::GetList(Array("SORT" => "ASC"), $arFilter, false, $arSelect, false);

$cnt=0;
while($ob = $rsItems->GetNextElement())
{
 $arFields = $ob->GetFields();
 $ib30_id = $arFields["ID"];
 //echo 'ib30 $arFields["ID"]='. $arFields["ID"] ."\n";
 $cnt++;
}

//echo "cnt=".$cnt."\n";
if ( $cnt > 1) {
    fwrite(STDERR, $argv[0]." ERROR: Обнаружено ". $cnt ." раздела(-ов) модификаций с именем ".$argv[1] .".\n");
    exit(2);
}

//////////////////////////////////////////////////////////////////////////////////

$arFilter = array(
    "IBLOCK_ID" => "29",
    "NAME" => $argv[1],
    "ACTIVE" => "Y",
);

//674 - модификации
//675 - модификаторы
$arSelect = Array("IBLOCK_ID", "ID", "NAME", "TIMESTAMP_X", "MODIFIED_BY", "PROPERTY_674", "PROPERTY_675" );

$el = new CIBlockElement;
$rsItems = $el->GetList(Array("SORT" => "ASC"), $arFilter, false, false, $arSelect);

while($ob = $rsItems->GetNextElement())
{
    $arFields = $ob->GetFields();
    //$devName = $arFields["NAME"];
    $ib29_prop674 = $arFields["PROPERTY_674_VALUE"];
}

if ($ib30_id == $ib29_prop674 || is_null($ib29_prop674) ) {
        echo $ib30_id;
} else {
	$arFilter = array(
	    "IBLOCK_ID" => "30",
	    "XML_ID" => $ib29_prop674,
	    "ACTIVE" => "Y",
	);

	$arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM");
	$rsItems = CIBlockSection::GetList(Array("SORT" => "ASC"), $arFilter, false, $arSelect, false);

	while($ob = $rsItems->GetNextElement())
	{
	 $arFields = $ob->GetFields();
	 $prop674_name = $arFields["NAME"];
	}
    fwrite(STDERR, "Несоответствие: Прибор=". $argv[1] .
        ", Секция с ценами=". $prop674_name .
        ", Ид секции в приборе=". $ib29_prop674 .
        ", Ид секции с именем[".$argv[1]."]=". $ib30_id 
        ."\n");
}

?>
