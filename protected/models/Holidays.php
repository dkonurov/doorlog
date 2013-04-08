<?php
namespace models;
use core\Db;
use core\Model;

class Holidays extends Model{
    public function getMonthDays($date){
        $uDay = 24*60*60;
        $date = substr($date,0,-2)."01";
        $num = date("t",strtotime(substr($date,0,-2)))-2;
        $first = date("w",strtotime($date));
        $uTime = strtotime($date);
        $uMonthFirstDay = $uTime;
        $uOffsetDay = $uMonthFirstDay+$num*$uDay;
        $days = $uMonthFirstDay;
        while($days<=$uOffsetDay){
        $type = 0;
        $date = date("Y-m-d", $days);
        $name = strftime("%A", $days);
        if(date("w",$days)==0 or date("w",$days)==6){
            $type = 1;
        } 
        $Month[] = array("Days" =>$name, "date" => $date,"type" => $type);
        $days = $days + $uDay;
        }
        return $Month;
    }
    public function getAllDays($date){
        $month = $this->getMonthDays($date);
        $num = date("t",strtotime(substr($date,0,-2)))-2;
        $uFirst = $month[0]['date'];
        $uLast = $month[$num]['date'];
        $q="SELECT date,holiday_type_id
        FROM holiday WHERE date>='$uFirst' AND date<='$uLast'";
        $result = $this->fetchAll($q);
        for ($i=0;$i<=$num;$i++){
            foreach ($result as $holiday){
                if ($month[$i]['date']==$holiday['date']){
                   $month[$i]['type']=$holiday['holiday_type_id'];
                }
            }
        }
        return $month;
    }
}