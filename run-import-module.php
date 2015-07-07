<?php
// $_SERVER["DOCUMENT_ROOT"] = "/home/bitrix/ext_www/kipspb2.arc.world";
require("set-doc-root.php");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");


include($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/cat_import_setup.php?lang=ru&ACT_FILE=csv_new&ACTION=IMPORT&PROFILE_ID=38");

