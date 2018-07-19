<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use Impavel\Kodix\Entities;

Loc::loadMessages(__FILE__);

define('CARSAPI_STATUS_OK', 200);
define('CARSAPI_STATUS_ERROR', 400);
define('CARSAPI_STATUS_NOTFOUND', 404);

class CarsApi extends CBitrixComponent
{
    
    protected $status = null;
    protected $context = null;

    protected function checkModules()
    {
        if (!Main\Loader::includeModule('impavel.kodix')) {
            $this->showResponse(CARSAPI_STATUS_ERROR, 'The module impavel.kodix not installed.');            
        }
        global $APPLICATION;
        if($APPLICATION->GetUserRight('impavel.kodix') <= 'D')
        {
            $this->showResponse(CARSAPI_STATUS_ERROR, 'Access to module impavel.kodix denied.');            
        }
    }
    
    protected function parseSefUrl()
    {
        $arDefaultUrlTemplates404 = array(
            "brand" => "brands",            
            "model" => "models",
            "compl" => "comps",            
            "car" => "cars",            
            "detail" => "cars/#CAR_ID#"
        );
        $arDefaultVariableAliases404 = array();
        $arComponentVariables = array(
            "CAR_ID",
        );
        
        $arVariables = array();

        $arUrlTemplates = CComponentEngine::makeComponentUrlTemplates($arDefaultUrlTemplates404, $this->arParams["SEF_URL_TEMPLATES"]);
        $arVariableAliases = CComponentEngine::makeComponentVariableAliases($arDefaultVariableAliases404, $this->arParams["VARIABLE_ALIASES"]);

        $engine = new CComponentEngine($this);
        $componentPage = $engine->guessComponentPath(
            $this->arParams["SEF_FOLDER"], 
            $arUrlTemplates,
            $arVariables
        );
        
        if (!$componentPage) {
            $this->showResponse(CARSAPI_STATUS_NOTFOUND, 'Api controller not found. Tip: check your url request - it mustn\'t have a closing slash.');
        }

        CComponentEngine::initComponentVariables($componentPage, $arComponentVariables, $arVariableAliases, $arVariables);

        $this->arResult['COMPONENT_PAGE'] = $componentPage;       
        $this->arResult['VARIABLES'] = $arVariables;
    }
    
    protected function setStatus($code)
    {
        $this->status = $code;
    }
    
    protected function setContext($context)
    {
        $this->context = $context;
    }
    
    protected function showResponse($code = null, $context = null)
    {
        global $APPLICATION;
        
        $response = [
            'status' => $code ?? $this->status,
            'context' => $context ?? $this->context
        ];
        
        $APPLICATION->RestartBuffer();
        header('Content-type: application/json');
        echo Main\Web\Json::encode($response, JSON_PRETTY_PRINT);
        exit;
    }
    
    public function executeComponent()
    {
        $this->checkModules();  
        $this->parseSefUrl();            
        
        // Узнаём класс сущности и создаём ORM объект
        $entityName = strtolower($this->arResult['COMPONENT_PAGE'] == 'detail' ? 'car' : $this->arResult['COMPONENT_PAGE']);
        $entityClassName = '\\Impavel\\Kodix\\Entities\\' . ucfirst($entityName) . 'Table';                             
        if (!class_exists($entityClassName)) {
            $this->showResponse(CARSAPI_STATUS_ERROR, "The entity \"{$entityName}\" does not exist.");             
        }
        $ormEntity = new $entityClassName;
        
        // Если метод не GET, то сообщаем об ошибке
        if (strtolower($this->request->getRequestMethod()) !== 'get')
        {
            $this->showResponse(CARSAPI_STATUS_ERROR, "The method GET is available only.");
        }        
        
        // Параметры get-запроса
        $q = $this->request->getQueryList()->toArray();
        $q = array_change_key_case($q);
        
        // Главной запрос на выборку записей
        $query = new Main\Entity\Query($ormEntity->getEntity());
        $query
            ->setSelect(['*']);
        
        // В зависимости от сущности и существующих get-параметров фильтруем записи
        switch ($entityName)
        {   
            case 'model':
                    if (!empty($q['brand_id'])) 
                    {
                        $query->setFilter(['BRAND_ID' => (array)$q['brand_id']]);
                    }
                    break;

                case 'compl':
                    if (!empty($q['model_id'])) 
                    {
                        $query->setFilter(['MODEL_ID' => (array)$q['model_id']]);
                    }                    
                    break;

                case 'car':
                    $query
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
                        ]);
                    
                    if ($this->arResult['COMPONENT_PAGE'] == 'detail')
                    {
                        $query->setFilter(['ID' => $this->arResult['VARIABLES']['CAR_ID']]); 
                    }
                    else
                    {
                        $filter = ['compl_id', 'model_id', 'brand_id', 'year', 'price'];
                        foreach ($filter as $f)
                        {
                            $data = (array)$q[$f];
                            if (!empty($data)) 
                            {
                                $query->setFilter([strtoupper($f) => $data]);
                            }
                        }                        
                    }                    
                    break;

                default:
                    //
                    break;
        }
        
        // Получаем список элементов и сохраняем результат в контексте ответа
        try {
            $this->setContext(
                $this->arResult['COMPONENT_PAGE'] == 'detail' ? $query->exec()->fetch() : $query->exec()->fetchAll()
            );
        } catch (Exception $e) {
            $this->showResponse(CARSAPI_STATUS_ERROR, $e->getMessage());
        }
        
        // Проверяем на существование элемента, если был запрос на детальный вывод
        if ($this->arResult['COMPONENT_PAGE'] == 'detail' && !$this->context)
        {
            $this->showResponse(CARSAPI_STATUS_NOTFOUND, 'Car not found.');
        }
        
        // Если скрипт дошёл до сюда - значит всё ок. О чем и сообщаем, а также показываем результат.
        $this->setStatus(CARSAPI_STATUS_OK);
        $this->showResponse();
    }
    
}