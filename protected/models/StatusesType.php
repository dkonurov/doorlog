<?php

namespace models;

/**
 * Class includes const and array name type of status
 */
Class StatusesType 
{
    
    const HOLIDAY = 1;
    const SICK = 2;
    const TRIP = 3;
    const HOME = 4;
    const OTHER_OFFICE = 5;
    const VACATION = 6;
    
    /**
     * get type of status
     * @return array type of status
     */
    public static function values()
    {
        return array (
            self::HOLIDAY => "Отгул",
            self::SICK => "Болел",
            self::TRIP => "Командировка",
            self::HOME => "Из дома",
            self::OTHER_OFFICE => "В другом офисе",
            self::VACATION => "Отпуск",
        );
    }
    
    /**
     * get name of status
     * @param integer $status
     * @return string name of status
     */
    public static function getValue($status)
    {
        $type = self::values();
        return $type[$status];
    }
    
}
