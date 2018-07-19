<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arComponentDescription = array(
    "NAME" => Loc::GetMessage('kodix_CARS.DETAIL_COMPONENT_NAME'),
    "DESCRIPTION" => Loc::GetMessage('kodix_CARS.DETAIL_COMPONENT_DESC'),
    "ICON" => "/images/news_detail.gif", 
    "SORT" => 30,
    "PATH" => array(
        "ID" => "cars",
    ),    
);
?>