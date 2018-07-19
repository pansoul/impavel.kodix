<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Application;
use Bitrix\Main\Web\Uri;    

Loc::loadMessages(__FILE__);

$request = Application::getInstance()->getContext()->getRequest();
$uriString = $request->getRequestUri();
$uri = new Uri($uriString);
?>

<div class="container">
    <h1><?= $arResult['TITLE'] ?></h1>

    <? if (empty($arResult['ITEMS'])): ?>

        <div class="alert alert-info"><?= Loc::getMessage('kodix_ITEMS_EMPTY') ?></div>

    <? else: ?>

        <nav class="navbar navbar-expand-lg navbar-light bg-light">

            <? if ($arResult['ENTITY'] == 'car'): ?>
                <div class="dropdown mr-auto">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownSort" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?= Loc::getMessage('kodix_NAVBAR_SORT') ?>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownSort">
                        <a class="dropdown-item" href="<?= $uri->addParams(['sort' => 'PRICE--ASC'])->getUri() ?>"><?= Loc::getMessage('kodix_NAVBAR_SORT_PRICE_ASC') ?></a>
                        <a class="dropdown-item" href="<?= $uri->addParams(['sort' => 'PRICE--DESC'])->getUri() ?>"><?= Loc::getMessage('kodix_NAVBAR_SORT_PRICE_DESC') ?></a>
                        <a class="dropdown-item" href="<?= $uri->addParams(['sort' => 'YEAR--ASC'])->getUri() ?>"><?= Loc::getMessage('kodix_NAVBAR_SORT_YEAR_ASC') ?></a>
                        <a class="dropdown-item" href="<?= $uri->addParams(['sort' => 'YEAR--DESC'])->getUri() ?>"><?= Loc::getMessage('kodix_NAVBAR_SORT_YEAR_DESC') ?></a>
                    </div>
                </div>
            <? endif; ?>

            <div class="dropdown ml-auto">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownPagesize" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?= Loc::getMessage('kodix_NAVBAR_PAGESIZE') ?>
                </button>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownPagesize">
                    <a class="dropdown-item" href="<?= $uri->addParams(['pagesize' => 2])->deleteParams(['nav-list'])->getUri() ?>">2</a>
                    <a class="dropdown-item" href="<?= $uri->addParams(['pagesize' => 5])->deleteParams(['nav-list'])->getUri() ?>">5</a>
                    <a class="dropdown-item" href="<?= $uri->addParams(['pagesize' => 10])->deleteParams(['nav-list'])->getUri() ?>">10</a>
                </div>
            </div>

        </nav>
        <br/><br/>

        <table class="table table-striped">
            <? foreach ($arResult['ITEMS'] as $arItem): ?>
                <tr>
                    <th><?= $arItem['N'] ?></th>
                    <td>
                        <a href="<?= $arItem['DETAIL_URL']  ?>"><?= $arItem['NAME'] ?></a>
                    </td>
                    <? if ($arResult['ENTITY'] == 'car'): ?>
                        <td><?= $arItem['YEAR'] ?></td>
                        <td><?= number_format($arItem['PRICE'], 0, '.', ' ') ?></td>
                    <? endif; ?>
                </tr>
            <? endforeach; ?>
        </table>

        <?
        $APPLICATION->IncludeComponent(
           "bitrix:main.pagenavigation",
           "modern",
           array(
              "NAV_OBJECT" => $arResult['NAV'],
              "SEF_MODE" => "N",
           ),
           false
        );
        ?>

    <? endif; ?>

    <? if ($arResult['ENTITY'] != 'brand'): ?>
        <br/><br/>
        <a href="<?= $arResult['BACK_URL'] ?>"><?= Loc::getMessage('kodix_ITEMS_BACK') ?></a>    
    <? endif; ?>    
</div>

<?
\Bitrix\Main\Page\Asset::getInstance()->addCss('https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css');
\Bitrix\Main\Page\Asset::getInstance()->addJs('https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js');
\Bitrix\Main\Page\Asset::getInstance()->addJs('https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js');
?>