<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);

$APPLICATION->IncludeComponent(
    "impavel.kodix:cars.api", 
    "", 
    array(
        "SEF_FOLDER" => "/api/",
        "SEF_MODE" => "Y",
    ),
    false
);