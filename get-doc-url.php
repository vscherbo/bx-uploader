#!/usr/bin/env php
<?php
require("set-doc-root.php");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$shortopts  = "";
$shortopts .= "n:";  // name - обязательное значение (или i)
$shortopts .= "i:";  // xml_id - обязательное значение (или n)

$options = getopt($shortopts);
// var_dump($options);


if ( ($options["n"] != "") && ($options["i"] != "")  )
{
        fwrite(STDERR, $argv[0]." ERROR: only one parameter -n Model_name OR -i XML_ID allowed.\n" );
        exit(1);
}

if ( ($options["n"] == "") && ($options["i"] == "")  )
{
        fwrite(STDERR, $argv[0]." ERROR: parameter -n Model_name OR -i XML_ID is required.\n" );
        exit(1);
}


if ($options["n"] != "")
{
    $arFilter = array(
        "IBLOCK_ID" => "29",
        "ACTIVE" => "Y",
        "NAME" => $options["n"],
        "!PROPERTY_607" => false,
    );
}

if ($options["i"] != "")
{
    $arFilter = array(
        "IBLOCK_ID" => "29",
        "ACTIVE" => "Y",
        "XML_ID" => $options["i"],
        "!PROPERTY_607" => false,
    );
}

//    "PROPERTY_609_VALUE" => "305061",
// 305061 - Автоматика

// 607 - документация

//609 - производитель 315128 Haupa "PROD_ID"
//656 - дилерская позиция "EX_SYNC_FLAG"
$arSelect = Array("ID", "NAME", "TIMESTAMP_X", "MODIFIED_BY", "PROPERTY_607" );

$el = new CIBlockElement;
$rsItems = $el->GetList(Array("SORT" => "ASC"), $arFilter, false, false, $arSelect);

while($ob = $rsItems->GetNextElement())
{
    $arFields = $ob->GetFields();
    $devName = $arFields["NAME"];
    $doc_url = "";
    $doc_name = "";
    // echo " Прибор=".$arFields["NAME"]."\n";
    //print_r($arFields);
    // echo "Docs prop_607=". print_r($arFields["PROPERTY_607_VALUE"], true) ."\n";
    $arFilter52 = array(
        "IBLOCK_ID" => "52",
        "ID" => $arFields["PROPERTY_607_VALUE"],
        //"ID" => 349504,
        //"CODE" => 'manual',
    );
    $items52 = CIBlockElement::GetList(Array("sort"=>"asc"), $arFilter52); // manual - section_id 5160
    $cnt=0;
    while($arItem52 = $items52->GetNext())
    {
        $cnt += 1;
        // echo "Doc name=" . $arItem52['NAME'] . "\n";
        //echo "arItem52=" . print_r($arItem52, true) . "\n";
        $nav = CIBlockSection::GetNavChain(52, $arItem52["IBLOCK_SECTION_ID"]);
        $flg_manual = false;
        while ($arNav=$nav->GetNext()):
            if ('manual' == $arNav["CODE"]) {
               $flg_manual = true;
               // echo $arNav["CODE"]."::" . $arNav["NAME"]. "(" .$arNav["ID"].  ")" ."->";
               //print_r($arNav);
            }
        endwhile;
        if ( $flg_manual) {
            // echo $arItem52["NAME"] . "(" .$arItem52["ID"].  ")" . "^URL=" . $arItem52["DETAIL_PAGE_URL"] ;
            if ( "" == $doc_url ) {
                $doc_url = $arItem52["DETAIL_PAGE_URL"] ;
            } else {
                $doc_url = $doc_url . "^". $arItem52["DETAIL_PAGE_URL"] ;
            }
            if ( "" == $doc_name ) {
                $doc_name = $arItem52["NAME"];
            } else {
                $doc_name = $doc_name . "^". $arItem52["NAME"] ;
            }
        }

        /**
        $arFilterSect52 = array(
             "ID" => $arItem52['IBLOCK_SECTION_ID'],
             "CODE"=>'manual',
         );
        $section52 = GetIBlockSectionList(52, false, Array("sort"=>"asc"), 0, $arFilterSect52);
        while($arSect52 = $section52->GetNext())
        {
            echo "  Doc name=" . $arItem52['NAME'] . "\n";
            echo "arSect52=" . print_r($arSect52, true) . "\n";
        }
        **/
    }
    /***
    $el->SetPropertyValues($arFields["ID"], 29, "N", "EX_SYNC_FLAG");
    if (895371 == $arFields["ID"]) {
       if ( $el->Update($arFields["ID"], array()) )
          echo "updated:".$arFields["NAME"]."\n";
       else
          echo "Error: ".$el->LAST_ERROR ."\n";

    }
    ***/
}

// echo "cnt=". $cnt . "\n";
if ($cnt > 1) {
    echo $doc_name . ";";
    echo $doc_url . "\n";
} else {
    //echo $doc_name . "\n";
    echo $doc_url . "\n";
}    

?>
