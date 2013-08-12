<?php

namespace fixtures;

use models\StatusesType;
use models\Status;
use core\Model;
/**
 * Class work with base and update table status
 */
Class StatusesFixtures extends Model 
{
    /**
     * search type_id in table status and if don't find them insert
     */
    public function update() 
    {
        
        $statuses = new Status();
        $type = StatusesType::values();
        $statusDB = $statuses->getAllType();
        foreach ($type as $status => $name) {
            $chek = true;
            if (isset($statusDB)) {
                for ($i = 0; $i < count($statusDB); $i++) {
                    if ($status == $statusDB[$i]['type_id']) {
                        $chek = false;
                        break;
                    }
                }
            }
            if ($chek) {
                $q="INSERT INTO `status` VALUES('NULL', :type_id, :name, :time)";
                $params['type_id'] = $status;
                $params['name'] = $name;
                if ($status == StatusesType::HOLIDAY || $status == StatusesType::SICK || StatusesType::VACATION) {
                    $params['time'] = 0;
                } else {
                    $params['time'] = 8;
                }
                $result=$this->execute($q, $params);
            }
        }
    }   
}
