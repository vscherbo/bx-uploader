#!/usr/bin/env php
<?php
require("set-doc-root.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule("iblock");

        AddEventHandler("iblock", "OnAfterIBlockSectionAdd", "OnAfterAddImport");
        AddEventHandler("iblock", "OnAfterIBlockSectionUpdate", "OnAfterAddImport");
        function OnAfterAddImport($arElement)
        {
        // print "AfterAdd ". $arElement["IBLOCK_ID"] . "\n";
                if (in_array($arElement["IBLOCK_ID"], Array(30)))
                {
                        $arOriginalElement = $arElement;

                        $arElement["XML_ID"] = $arElement["ID"];

                        if ($arOriginalElement !== $arElement)
                        {
                                $obSection = new CIBlockSection;
                                $obSection->Update($arElement["ID"], $arElement);
                        }
                        return true;
                }

        }

        
        AddEventHandler("iblock", "OnBeforeIBlockElementAdd", "OnBeforeImport");
        AddEventHandler("iblock", "OnBeforeIBlockelementUpdate", "OnBeforeImport");
        function OnBeforeImport($arElement)
        {
                if (in_array($arElement["IBLOCK_ID"], Array(29, 30, 34)))
                {
                        $arElement["MODIFIED_BY"] = 6938;
                        return true;
                }

        }

        

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/php_interface/include/catalog_import/cron_frame.php");
?>
