<?php

include_once(dirname(__DIR__).'/lib/helper.php');

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\IO\Directory;
use Bitrix\Main\Application;
use Impavel\Kodix\Helper;

Loc::loadMessages(__FILE__);

if (class_exists('impavel_kodix'))
{
    return;
}

class impavel_kodix extends CModule
{

    public $MODULE_ID = "impavel.kodix";
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $MODULE_CSS;
    
    public $PARTNER_NAME;
    public $PARTNER_URI;
    
    private $errors = false;

    function __construct()
    {
        $arModuleVersion = array();
        include(__DIR__ . "/version.php");

        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME = Loc::getMessage("kodix_MODULE_NAME");
        $this->MODULE_DESCRIPTION = Loc::getMessage("kodix_MODULE_DESC");

        $this->PARTNER_NAME = Loc::getMessage("kodix_PARTNER_NAME");
        $this->PARTNER_URI = Loc::getMessage("kodix_PARTNER_URI");
    }

    function DoInstall()
    {
        global $APPLICATION, $USER, $step, $DB;
        $step = IntVal($step);

        if (!$USER->IsAdmin()) {
            return;
        }
        
        if (PHP_MAJOR_VERSION < 7)
        {
            $APPLICATION->ThrowException(GetMessage('kodix_PHPVERSION_NOT_SUPPORTED'));
        }

        if (strtolower($DB->type) !== 'mysql')
        {
            $APPLICATION->ThrowException(GetMessage('kodix_DB_NOT_SUPPORTED'));
            $APPLICATION->IncludeAdminFile(GetMessage("kodix_INSTALL_TITLE"), Helper::GetModulePath() . "/install/step1.php");
        } 
        else
        {
            if (!check_bitrix_sessid())
            {
                $step = 1;
            }
            
            if ($step < 2)
            {
                $APPLICATION->IncludeAdminFile(GetMessage("kodix_INSTALL_TITLE"), Helper::GetModulePath() . "/install/step1.php");
            } 
            elseif ($step == 2)
            {
                $this->InstallDB([
                    "remove_tables" => $_REQUEST["remove_tables"],
                ]);
                $this->InstallFiles();
                $this->manageApiFiles('install');
                $GLOBALS["errors"] = $this->errors;
                
                if ($this->errors === false) {
                    RegisterModule($this->MODULE_ID);
                }
                
                $APPLICATION->IncludeAdminFile(GetMessage("kodix_INSTALL_TITLE"), Helper::GetModulePath() . "/install/step2.php");
            }
        }
        
        return true;
    }

    function DoUninstall()
    {
        global $APPLICATION, $USER, $step;
        if ($USER->IsAdmin())
        {
            $step = IntVal($step);
            
            if ($step < 2)
            {
                $APPLICATION->IncludeAdminFile(GetMessage("kodix_UNINSTALL_TITLE"), Helper::GetModulePath() . "/install/unstep1.php");
            } 
            elseif ($step == 2)
            {
                $this->UnInstallDB([
                    "savedata" => $_REQUEST["savedata"],
                ]);
                $this->UnInstallFiles();
                $this->manageApiFiles('uninstall');
                $GLOBALS["errors"] = $this->errors;
                
                if ($this->errors === false) {
                    UnRegisterModule($this->MODULE_ID);
                }
                
                $APPLICATION->IncludeAdminFile(GetMessage("kodix_UNINSTALL_TITLE"), Helper::GetModulePath() . "/install/unstep2.php");   
            }
        }
        
        return true;
    }

    function InstallDB($arParams = [])
    {   
        global $DB, $APPLICATION;

        $this->errors = false;
        
        try {
            $tables = ['kdx_brand', 'kdx_car', 'kdx_car_option', 'kdx_compl', 'kdx_compl_option', 'kdx_model', 'kdx_option'];
            foreach ($tables as $table)
            {
                Application::getConnection()->queryExecute("SELECT 1 FROM {$table} LIMIT 1;");
            }
        } catch (Bitrix\Main\DB\SqlQueryException $e) {
            $arParams["remove_tables"] = "Y";
        }
        
        if ($arParams["remove_tables"] != "Y") {
            return;
        } 

        if (strtolower($DB->type) !== 'mysql')
        {
            $this->errors = array(
                GetMessage('kodix_DB_NOT_SUPPORTED'),
            );
        } 
        
        $this->UnInstallDB();
        $this->errors = $DB->RunSQLBatch(Helper::GetModulePath() . "/install/db/" . strtolower($DB->type) . "/install.sql");        
        $this->errors = $DB->RunSQLBatch(Helper::GetModulePath() . "/install/db/" . strtolower($DB->type) . "/filldata.sql");        

        if ($this->errors !== false)
        {
            $APPLICATION->ThrowException(implode("<br>", $this->errors));
            return false;
        }
        
        return true;
    }

    function UnInstallDB($arParams = [])
    {
        global $DB, $APPLICATION;

        $this->errors = false;

        if ($arParams["savedata"] != "Y")
        {
            $this->errors = $DB->RunSQLBatch(Helper::GetModulePath() . "/install/db/" . strtolower($DB->type) . "/uninstall.sql");
        }

        if ($this->errors !== false)
        {
            $APPLICATION->ThrowException(implode("<br>", $this->errors));
            return false;
        }
        
        return true;
    }
    
    function manageApiFiles($mode = null)
    {
        $urlrewriteFile = Application::getDocumentRoot() . '/urlrewrite.php';
        include $urlrewriteFile;
        
        if ($mode == 'install')
        {
            CopyDirFiles(__DIR__ . "/page/", Application::getDocumentRoot(), true, true);                
            $arUrlRewrite[] = [
                "CONDITION" => "#^/api/#",
                "RULE" => "",
                "ID" => "impavel.kodix:cars.api",
                "PATH" => "/api/index.php",
            ];
        }
        elseif ($mode == 'uninstall')
        {
            Directory::deleteDirectory(Application::getDocumentRoot() . "/api");        
            $arUrlRewrite = array_filter($arUrlRewrite, function($v){
                return $v['CONDITION'] != '#^/api/#';
            });
        }
        else
        {
            return;
        }
        
        file_put_contents($urlrewriteFile, "<?php \r\n \$arUrlRewrite = " . var_export($arUrlRewrite, true) . ";");
    }    

    function InstallFiles()
    {
        CopyDirFiles(__DIR__ . "/components/" . $this->MODULE_ID, Application::getDocumentRoot() . "/bitrix/components/" . $this->MODULE_ID, true, true);        
        
        return true;
    }    

    function UnInstallFiles()
    {
        Directory::deleteDirectory(Application::getDocumentRoot() . "/bitrix/components/" . $this->MODULE_ID);        
        
        return true;
    }
    
    function GetPath($notDocumentRoot = false)
    {
        if ($notDocumentRoot) {
            return str_ireplace(Application::getDocumentRoot(), '', dirname(__DIR__));
        }
        else
        {
            return dirname(__DIR__);
        }
    }

}