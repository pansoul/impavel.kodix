<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\PageNavigation;
use Impavel\Kodix\Entities;

Loc::loadMessages(__FILE__);

// @todo 
// Возможно, наследование от класса ElementList было бы полезным, 
// но для этого нужно разобраться в этот классе, а времени нет :(
// ...
// use Bitrix\Iblock;
// use Bitrix\Iblock\Component\ElementList;
// ...
// class CarsList extends ElementList
class CarsList extends CBitrixComponent
{
    const PARAMS_DEFAULT = [
        'pagesize' => 2,
        'sort' => [
            'field' => [
                'value' => 'PRICE',
                'list' => ['PRICE', 'YEAR']
            ],
            'by' => [
                'value' => 'ASC',
                'list' => ['ASC', 'DESC']
            ]
        ]
    ];
    
    protected $entityName = null;
    protected $entityClassName = null;
    protected $ormEntity = null;    
    protected $arTitles = [];
    protected $arBackUrls = [];
    
    protected function checkModules()
    {
        if (!Main\Loader::includeModule('impavel.kodix')) {
            throw new Main\LoaderException(Loc::getMessage('kodix_MODULE_NOT_INSTALLED'));
        }
    }
    
    protected function prepare()
    {
        $this->entityName = strtolower($this->arParams['ENTITY']);
        $this->entityClassName = '\\Impavel\\Kodix\\Entities\\' . ucfirst($this->entityName) . 'Table';                     
        if (!class_exists($this->entityClassName)) {
            throw new Main\ArgumentException(Loc::getMessage('kodix_ENTITY_NOT_EXIST'));
        }
        $this->ormEntity = new $this->entityClassName;
        $this->arTitles = [
            'brand' => Loc::getMessage('kodix_PAGE_TITLE_BRAND'),        
            'model' => Loc::getMessage('kodix_PAGE_TITLE_MODEL'),
            'compl' => Loc::getMessage('kodix_PAGE_TITLE_COMPL'),
            'car' => Loc::getMessage('kodix_PAGE_TITLE_CAR')
        ];
        $this->arBackUrls = [
            'car' => $this->arParams['URL_TEMPLATES']['compl'],
            'compl' => $this->arParams['URL_TEMPLATES']['model'],
            'model' => $this->arParams['URL_TEMPLATES']['brand'],
        ];
    }
    
    protected function getCorrectSortParams($type, $value)
    {
        return in_array($value, self::PARAMS_DEFAULT['sort'][$type]['list']) 
            ? $value 
            : self::PARAMS_DEFAULT['sort'][$type]['value'];
    }
    
    protected function getDetailUrl($mask, $item = null)
    {
        $arVariables = $this->arParams['VARIABLES'];
        $arVariables[strtoupper($this->entityName) . '_ID'] = @$item['ID'];        
        $find = array_map(function($v){
            return '#' . $v . '#';
        }, array_keys($arVariables));
        $replace = array_values($arVariables);
        return $this->arParams['FOLDER'] . str_replace($find, $replace, $mask);
    }
    
    public function onPrepareComponentParams($params)
    {
        $params['PAGESIZE'] = intval($params['PAGESIZE']) > 0 ? intval($params['PAGESIZE']) : self::PARAMS_DEFAULT['pagesize'];                        
        $params['VARIABLES'] = (array) $params['VARIABLES'];
        return $params;
    }

    public function executeComponent()
    {
        $this->checkModules();
        $this->prepare();
        
        // Наш родитель. Для будущей кастомизации вполне может пригодиться.
        // $parent = $this->getParent();        
        
        $pagesize = intval($this->request['pagesize']) > 0 ? intval($this->request['pagesize']) : $this->arParams['PAGESIZE'];
        
        // Определение переменных для указания сортировки
        if ($this->request['sort'])
        {
            list($sortField, $sortBy) = explode('--', $this->request['sort']);
        } 
        elseif (is_array($this->arParams['SORT']))
        {
            list($sortField, $sortBy) = each($this->arParams['SORT']);
        }
        $sortField = $this->getCorrectSortParams('field', $sortField);
        $sortBy = $this->getCorrectSortParams('by', $sortBy);
        $order = [$sortField => $sortBy];        
        
        // Создание постраничной навигации
        $nav = new PageNavigation("nav-list");
        $nav->allowAllRecords(true)
            ->setPageSize($pagesize)
            ->initFromUri();        
        
        // Главной запрос на выборку записей
        $query = new Main\Entity\Query($this->ormEntity->getEntity());
        $query
            ->setSelect(['*'])            
            ->setOffset($nav->getOffset())
            ->setLimit($nav->getLimit())
            ->countTotal(true);
        
        // Корректируем запрос выборки в зависимости от текущей сущности
        $arVariables = $this->arParams['VARIABLES'];        
        if ($arVariables) {
            switch ($this->entityName)
            {   
                case 'model':
                    $query->setFilter(['BRAND_ID' => $arVariables['BRAND_ID']]);
                    break;

                case 'compl':
                    $query
                        ->registerRuntimeField("MODEL", array(                
                            "data_type" => '\\Impavel\\Kodix\\Entities\\ModelTable',
                            'reference' => array('=this.MODEL_ID' => 'ref.ID'),
                            )
                        )
                        ->setFilter([
                            'MODEL_ID' => $arVariables['MODEL_ID'],
                            'MODEL.BRAND_ID' => $arVariables['BRAND_ID'],
                        ]);
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
                        ->setFilter([
                            'COMPL_ID' => $arVariables['COMPL_ID'],
                            'COMPL.MODEL_ID' => $arVariables['MODEL_ID'],
                            'MODEL.BRAND_ID' => $arVariables['BRAND_ID'],
                        ])
                        ->setOrder($order);
                    break;

                default:
                    //
                    break;
            }
        }
        
        $list = $query->exec();       
        
        $nav->setRecordCount($list->getCount());
        
        $this->arResult['TITLE'] = $this->arTitles[$this->entityName];
        $this->arResult['ENTITY'] = $this->entityName;
        $this->arResult['NAV'] = $nav;
        $this->arResult['BACK_URL'] = $this->getDetailUrl($this->arBackUrls[$this->entityName]);
        $this->arResult['ITEMS'] = [];        
        
        $n = $nav->getOffset() + 1;        
        while($item = $list->fetch())
        {            
            $item['DETAIL_URL'] = $this->getDetailUrl($this->arParams['URL_DETAILS'][$this->entityName], $item);
            $item['N'] = $n;
            $this->arResult['ITEMS'][] = $item;                        
            $n++;
        }

        $this->includeComponentTemplate();
    }
    
}