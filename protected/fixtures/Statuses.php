<?php

namespace models;
use models\StatusesType as StatusesType;
use models\Status as Status;
use core\Db;
use core\Model;
Class StatusesFixtures extends Model {
    function update(){
        $statuses = new Status();
        $type = StatusType::values();
        $statusDB=$statuses->getAllType();
        foreach ($type as $status => $name) {
            $chek=TRUE;
            for ($i=0;$i<count($statusDB);$i++) {
                if ($status==$statusDB[$i]['type_id']) {
                    $chek=FALSE;
                    break;
                }
            }
            if ($chek) {
                $q="INSERT INTO `status` VALUES('NULL', ':type_id, ':name', ':time')";
                $params['type_id']=$status;
                $params['name']=$name;
                $params['time']=8;
                $result=$this->execute($q, $params);
            }
        }
    }
}
