<?php

namespace Impavel\Kodix\Entities;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class CarTable extends Entity\DataManager
{

    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'kdx_car';
    }

    /**
     * Returns entity map definition.
     *
     * @return array
     */
    public static function getMap()
    {
        return array(
            new Entity\IntegerField('ID', array(
                'primary' => true,
                'autocomplete' => true
            )),
            new Entity\StringField('NAME', array(
                'required' => true,
                'validation' => array(__CLASS__, 'validateName'),
            )),            
            new Entity\IntegerField('YEAR', array(
                'required' => true,
            )),
            new Entity\IntegerField('PRICE', array(
                'required' => true,
            )),
            new Entity\IntegerField('COMPL_ID', array(
                'required' => true,
            )),
        );
    }
    
    /**
     * Returns validators for NAME field.
     *
     * @return array
     */
    public static function validateName()
    {
        return array(
            new Entity\Validator\Length(null, 255),
        );
    }

}