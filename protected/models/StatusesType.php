<?php

namespace models;
use core\Db;
use core\Model;
Class StatusType {
    const HOLIDAY=1;
    const SICK=2;
    const TRIP=3;
    const HOME=4;
    const OTHER_OFFICE=5;
    
    public static function values(){
        return array (
            self::HOLIDAY=>"Отгул",
            self::SICK=>"Болел",
            self::TRIP=>"Командировка",
            self::HOME=>"Из дома",
            self::OTHER_OFFICE=>"В другом офисе"
        );
    }
    
    public static function getValue($status){
        return self::values()[$status];
    }
    
}