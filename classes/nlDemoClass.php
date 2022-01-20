<?php

class nlDemoClass extends ObjectModelCore
{
    public $id;

    public $id_product;

    public $note;

    public $id_user;


    public static $definition = array(
        'table' => 'nldemo',
        'primary' => 'id_nldemo',
        'multilang' => false,
        'fields' => array(
            'id_product' => array('type' => self::TYPE_INT, 'required' => true),
            'note' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
            'id_user' => array('type' => self::TYPE_INT, 'required' => false),
        )
    );
}