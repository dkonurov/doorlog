<?php
namespace models;
use core\Db;
use core\Model;
use core\Utils;

class Status extends Model {
    public function getAllType(){
        $q="SELECT type_id FROM `status`";
        $result=$this->fetchAll($q);
        return $result;
    }
}
