<?php

namespace Impavel\Kodix;

use Bitrix\Main\Application;

class Helper
{

    public static function GetModulePath($notDocumentRoot=false)
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