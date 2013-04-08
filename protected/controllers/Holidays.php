<?php
namespace controllers;

use core\Controller;
use core\FlashMessages;
use models\Holidays as HolidayModel;
use controllers\Main as Time;

class Holidays extends Controller{
    public function indexAction(){
        $time = new Time();
        
        $date = date("Y-m-d");
        $obj = new HolidayModel();
        $holidays = $obj->getAllDays($date);
        
        $types = array('Выходной','Короткий','Рабочий');
        $values = array('t1','t2','t3');
        $third='t3';
        $second='t2';
        $first='t1';
        
        $this->render("Holidays/index.tpl", array('holidays' => $holidays, 'types' => $types, 'values' => $values,'first' => $first,'second' => $second, 'third' => $third));
    }
}