<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?$APPLICATION->IncludeComponent(
	"impavel.kodix:cars.list", 
	"", 
	array(
	    'ENTITY' => $arResult['COMPONENT_PAGE'],
            'VARIABLES' => $arResult['VARIABLES'],
            'FOLDER' => $arResult['FOLDER'],
            'URL_DETAILS' => $arResult['URL_DETAILS'],
            'URL_TEMPLATES' => $arResult['URL_TEMPLATES']
	),
	$component
)?>