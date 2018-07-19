<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Application;
use Bitrix\Main\Web\Uri;    

Loc::loadMessages(__FILE__);
?>

<div class="container">
    <h1><?= $arResult['ITEM']['NAME'] ?></h1>

    <div class="row">
        <div class="col-12 col-md-4">
            <p><img src="<?= $this->GetFolder() ?>/images/car.jpg" class="img-thumbnail"></p>            
        </div>
        <div class="col-12 col-md-8">
            <h3 class="mt-0"><?= Loc::getMessage('kodix_ITEM_TITLE_FULLDATA') ?></h3>
            <table class="table table-striped">
                <? foreach ($arResult['ITEM'] as $key => $value): ?>
                    <tr>
                        <th><?= $key ?></th>
                        <td><?= $value ?></td>
                    </tr>
                <? endforeach; ?>
            </table>
        </div>
    </div>

    <h3><?= Loc::getMessage('kodix_ITEM_TITLE_OPTIONS_CAR') ?></h3>

    <? if ($arResult['OPTIONS']['CAR']): ?>
        <table class="table table-striped">
            <? foreach ($arResult['OPTIONS']['CAR'] as $arOption): ?>
                <tr>
                    <td><?= $arOption['NAME'] ?></td>
                </tr>
            <? endforeach; ?>
        </table>
    <? else: ?>
        <div class="alert alert-info"><?= Loc::getMessage('kodix_ITEM_OPTIONS_EMPTY') ?></div>
    <? endif; ?>

    <h3><?= Loc::getMessage('kodix_ITEM_TITLE_OPTIONS_COMPL') ?></h3>

    <? if ($arResult['OPTIONS']['COMPL']): ?>
        <table class="table table-striped">
            <? foreach ($arResult['OPTIONS']['COMPL'] as $arOption): ?>
                <tr>
                    <td><?= $arOption['NAME'] ?></td>
                </tr>
            <? endforeach; ?>
        </table>
    <? else: ?>
        <div class="alert alert-info"><?= Loc::getMessage('kodix_ITEM_OPTIONS_EMPTY') ?></div>
    <? endif; ?>

    <br/><br/>
    <a href="<?= $arResult['BACK_URL'] ?>"><?= Loc::getMessage('kodix_ITEM_BACK') ?></a>
</div>

<?
\Bitrix\Main\Page\Asset::getInstance()->addCss('https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css');
?>