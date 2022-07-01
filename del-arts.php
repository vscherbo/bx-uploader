<?php

require("set-doc-root.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$arr = include 'del-arts.arr';
#print_r($arr);

if ($arr) {
        echo "Non-empty array\n";

	$arFilter = array(
	    "IBLOCK_ID" => "298",
	    'NAME' => $arr,
	);

	/**
	foreach (array_values($arr) as $art) {
	    print_r($art);
	    
	}
	**/

	$res = CIBlockElement::GetList(array(), $arFilter, false, false, array());
	while($item = $res->GetNextElement())
	{
	   $arFields = $item->GetFields();
	   fwrite(STDERR, $argv[0]." art:". $arFields["NAME"] .  ", id=". $arFields["ID"]. "\n" );
	   /**/
	   if(!CIBlockElement::Delete($arFields["ID"]))
	   {                                                                             
		$strWarning .= 'Error.';                                                 
		fwrite(STDERR, $argv[0]." ERROR deleting:". var_export($arFields["NAME"]) ."\n" );
	   }
	   /**/
											 
	} // end loop  

} else {
	echo "Empty array. Nothing to do, exiting\n";
}

?>
