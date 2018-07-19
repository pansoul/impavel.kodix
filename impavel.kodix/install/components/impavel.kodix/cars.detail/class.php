<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use Impavel\Kodix\Entities;

Loc::loadMessages(__FILE__);

class CarsDetail extends CBitrixComponent
{
    protected function checkModules()
    {
        if (!Main\Loader::includeModule('impavel.kodix')) {
            throw new Main\LoaderException(Loc::getMessage('kodix_MODULE_NOT_INSTALLED'));
        }
    }
    
    public function onPrepareComponentParams($params)
    {
        $params['ID'] = intval($params['ID']);                        
        return $params;
    }

    public function executeComponent()
    {
        $this->checkModules();        
        
        // Объект ORM сущности "Автомобиль"
        $carEntity = new Entities\CarTable;
        // Объект ORM сущности "Опции"
        $optionEntity = new Entities\OptionTable;
        
        // Главной запрос на выборку записи        
        $carQuery = new Main\Entity\Query($carEntity->getEntity());
        $carResult = $carQuery
            ->registerRuntimeField("COMPL", array(                
                "data_type" => '\\Impavel\\Kodix\\Entities\\ComplTable',
                'reference' => array('=this.COMPL_ID' => 'ref.ID'),
                )
            )
            ->registerRuntimeField("MODEL", array(                
                "data_type" => '\\Impavel\\Kodix\\Entities\\ModelTable',
                'reference' => array('=this.COMPL.MODEL_ID' => 'ref.ID'),
                )
            )
            ->registerRuntimeField("BRAND", array(                
                "data_type" => '\\Impavel\\Kodix\\Entities\\BrandTable',
                'reference' => array('=this.MODEL.BRAND_ID' => 'ref.ID'),
                )
            )
            ->setSelect([
                '*', 
                'COMPL_NAME' => 'COMPL.NAME', 
                'MODEL_NAME' => 'MODEL.NAME',
                'MODEL_ID' => 'MODEL.ID',
                'BRAND_NAME' => 'BRAND.NAME',
                'BRAND_ID' => 'BRAND.ID',
            ]) 
            ->setFilter(['ID' => $this->arParams['ID']])
            ->exec();       
        
        $item = $carResult->fetch();        
        
        // Запрос на выборку опций.
        // P.S. Как? КАК? Как добавить ёбанный distinct в запрос?
        // Да никак! Вот скрин от чувака, который писал в ТП https://imgur.com/GGWKBCU
        // P.P.S. По счастливому истечению обстоятельств мне не нужен distinct.
        $optionQuery = new Main\Entity\Query($optionEntity->getEntity());
        $optionResult = $optionQuery            
            ->registerRuntimeField("CAROPTION", array(                
                "data_type" => '\\Impavel\\Kodix\\Entities\\CarOptionTable',
                'reference' => array('=this.ID' => 'ref.OPTION_ID'),
                )
            )
            ->registerRuntimeField("COMPLOPTION", array(                
                "data_type" => '\\Impavel\\Kodix\\Entities\\ComplOptionTable',
                'reference' => array('=this.ID' => 'ref.OPTION_ID'),
                )
            )
            ->setSelect(['*', 'CAR_ID' => 'CAROPTION.CAR_ID', 'COMPL_ID' => 'COMPLOPTION.COMPL_ID',]) 
            ->setFilter([
                'LOGIC' => 'OR',
                ['CAROPTION.CAR_ID' => $this->arParams['ID']],
                ['COMPLOPTION.COMPL_ID' => $item['COMPL_ID']]
            ])
            ->exec();
        
        $options = $optionResult->fetchAll();
        
        $backUrlMask = $this->arParams['URL_TEMPLATES']['car'];
        $find = ['#BRAND_ID#', '#MODEL_ID#', '#COMPL_ID#'];
        $replace = [$item['BRAND_ID'], $item['MODEL_ID'], $item['COMPL_ID']];
        
        $this->arResult['ITEM'] = $item;
        $this->arResult['BACK_URL'] = $this->arParams['FOLDER'] . str_replace($find, $replace, $backUrlMask);
        $this->arResult['OPTIONS'] = [
            'CAR' => array_filter($options, function($v){
                return $v['CAR_ID'] > 0;
            }),
            'COMPL' => array_filter($options, function($v){
                return $v['COMPL_ID'] > 0;
            }),
        ];

        $this->includeComponentTemplate();
    }
    
}