#!/usr/bin/env php
<?php
require(getenv("HOME"). "/set-doc-root.php");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$shortopts  = "";
$shortopts .= "f:";  // filename - обязательное значение


$options = getopt($shortopts);
// var_dump($options);


if ($options["f"] == "")
{
        fwrite(STDERR, $argv[0]." ERROR: parameter -f _import-update.csv_ is required.\n" );
        exit(1);
}

// parse input CSV file, extract a part of the 1st filed with delimiter ":" - model_name
$mod_name_ind=0;
$row = 0;
if (($handle = fopen($options["f"], "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, "^")) !== FALSE) {
        $row++;
        if ( 1 == $row ) continue; // skip 1st row with names
        list($mod_name, $mod_code) = explode(":", $data[$mod_name_ind]);
        if (! in_array($mod_name, $arModels) ) {
            $arModels[] = $mod_name;
        }
    }
    // debug print_r($arModels);
    fclose($handle);
}

//exit("Interrupted for debug\n");


foreach ( $arModels as $model_name) {
	$arFilter = array(
	    "IBLOCK_ID" => "29",
	    "NAME" => $model_name,
	);

	//$arSelect = Array("ID", "NAME", "TIMESTAMP_X", "MODIFIED_BY", "PROPERTY_VALUES");

	$el = new CIBlockElement;
	$rsItems = $el->GetList(Array("SORT" => "ASC"), $arFilter, false, false, $arSelect);
	// print_r($rsItems);

        $not_found = True;
	while($ob = $rsItems->GetNextElement())
	{
            $not_found = False;
	    $arFields = $ob->GetFields();
	    // echo $arFields["NAME"]."\n";
	    // echo $arFields["ID"]."\n";

	    CSiteFinance::UpdateItemFinanceInfo($arFields["ID"]);
	    $res = $el->Update($arFields["ID"]);
	    if (! $res) { fwrite(STDERR, "Update ib29 for $model_name failed\n" );}
	}
        if ( $not_found ) {
           fwrite(STDERR, "Model $model_name not found in ib29\n" );
        }

} // for arModels

?>
