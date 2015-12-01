#!/usr/bin/env php
<?php
require("set-doc-root.php");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$shortopts  = "";
$shortopts .= "f:";  // filename - обязательное значение


$options = getopt($shortopts);
// var_dump($options);


if ($options["f"] == "")
{
        fwrite(STDERR, $argv[0]." ERROR: parameter -f _filename.csv_ is required.\n" );
        exit(1);
}

// parse input CSV file, extract 3rd filed "model_name"
$mod_name_ind=3;
$row = 1;
if (($handle = fopen($options["f"], "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, "^")) !== FALSE) {
        $row++;
        if ( 2 == $row ) continue; // skip 1st row with names
        if (! in_array($data[$mod_name_ind], $arModels) ) {
            $arModels[] = $data[$mod_name_ind];
        }
    }
    print_r($arModels);
    fclose($handle);
}


foreach ( $arModels as $model_name) {
	$arFilter = array(
	    "IBLOCK_ID" => "29",
	    "NAME" => $model_name,
	);

	//$arSelect = Array("ID", "NAME", "TIMESTAMP_X", "MODIFIED_BY", "PROPERTY_VALUES");

	$el = new CIBlockElement;
	$rsItems = $el->GetList(Array("SORT" => "ASC"), $arFilter, false, false, $arSelect);
	// print_r($rsItems);

	while($ob = $rsItems->GetNextElement())
	{
	    $arFields = $ob->GetFields();
	    // echo $arFields["NAME"]."\n";
	    // echo $arFields["ID"]."\n";


	    CSiteFinance::UpdateItemFinanceInfo($arFields["ID"]);
	    $res = $el->Update($arFields["ID"]);
	    if ($res) { echo $arFields["ID"]; }
	    else {      fwrite(STDERR, "Update ib29 for $model_name failed\n" );}
	}

} // for arModels

?>
