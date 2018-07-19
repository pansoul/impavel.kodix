<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arComponentDescription = array(
    "NAME" => Loc::GetMessage('kodix_CARS_COMPONENT_NAME'),
    "DESCRIPTION" => Loc::GetMessage('kodix_CARS_COMPONENT_DESC'),
    "ICON" => "/images/news_all.gif",
    "SORT" => 10,
    "COMPLEX" => "Y",
    "PATH" => array(
        "ID" => "cars",
        "NAME" => Loc::GetMessage('kodix_CARS_COMPONENT_NAME'),
    )
);
?>