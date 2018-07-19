<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?$APPLICATION->IncludeComponent(
	"impavel.kodix:cars.detail", 
	"", 
	array(	    
            'FOLDER' => $arResult['FOLDER'],
            'URL_DETAILS' => $arResult['URL_DETAILS'],
            'URL_TEMPLATES' => $arResult['URL_TEMPLATES'],
            'ID' => $arResult['VARIABLES']['CAR_ID'],            
	),
	$component
)?>
