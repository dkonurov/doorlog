<?php
namespace models;

use core\Model;

/**
 * Class work with table status
 */
class Status extends Model 
{
    /**
     * Get all type status from table status
     * @return array status
     */
    public function getAllType()
    {
        $q="SELECT type_id FROM `status`";
        $result = $this->fetchAll($q);
        return $result;
    }

    public function getAllTypeFullInfo()
    {
        $q="SELECT * FROM `status`";
        $result = $this->fetchAll($q);
        return $result;
    }
}
