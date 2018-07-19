<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

class Cars extends CBitrixComponent
{    
    const PAGE_TEMPLATES = [
        'brand' => 'list',
        'detail' => 'detail',
        'model' => 'list',
        'compl' => 'list',
        'car' => 'list'
    ];
    
    protected function parseSefUrl()
    {
        $arDefaultUrlTemplates404 = array(
            "brand" => "", // список брэндов
            "detail" => "detail/#CAR_ID#/", // детальная страница
            "model" => "brand_#BRAND_ID#/", // список моделей брэнда
            "compl" => "brand_#BRAND_ID#/model_#MODEL_ID#/", // список комплектаций модели
            "car" => "brand_#BRAND_ID#/model_#MODEL_ID#/compl_#COMPL_ID#/", // список автомобилей
        );
        $arDefaultVariableAliases404 = array();
        $arComponentVariables = array(
            "CAR_ID",
            "BRAND_ID",
            "MODEL_ID",
            "COMPL_ID",
        );
        
        $arVariables = array();
        $arDetailUrl = [
            'brand' => $arDefaultUrlTemplates404['model'],
            'model' => $arDefaultUrlTemplates404['compl'],
            'compl' => $arDefaultUrlTemplates404['car'],
            'car' => $arDefaultUrlTemplates404['detail'],
        ];

        $arUrlTemplates = CComponentEngine::makeComponentUrlTemplates($arDefaultUrlTemplates404, $this->arParams["SEF_URL_TEMPLATES"]);
        $arVariableAliases = CComponentEngine::makeComponentVariableAliases($arDefaultVariableAliases404, $this->arParams["VARIABLE_ALIASES"]);

        $engine = new CComponentEngine($this);
        $componentPage = $engine->guessComponentPath(
            $this->arParams["SEF_FOLDER"], 
            $arUrlTemplates,
            $arVariables
        );
        
        if (!$componentPage)
        {
            $componentPage = "brand";
        }

        CComponentEngine::initComponentVariables($componentPage, $arComponentVariables, $arVariableAliases, $arVariables);

        $this->arResult['COMPONENT_PAGE'] = $componentPage;
        $this->arResult['FOLDER'] = $this->arParams["SEF_FOLDER"];
        $this->arResult['URL_TEMPLATES'] = $arUrlTemplates;
        $this->arResult['URL_DETAILS'] = $arDetailUrl;
        $this->arResult['VARIABLES'] = $arVariables;
        $this->arResult['ALIASES'] = $arVariableAliases;        
    }
    
    public function onPrepareComponentParams($params)
    {
        $params['SEF_MODE'] = 'Y';
        return $params;
    }

    public function executeComponent()
    {     
        $this->parseSefUrl();
        $this->includeComponentTemplate(self::PAGE_TEMPLATES[$this->arResult['COMPONENT_PAGE']]);
    }
};