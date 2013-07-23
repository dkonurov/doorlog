<?php

namespace models;
use core\Db;
use core\Model;

class Positions extends Model{
    /**
     * Get from base name and sum of employee whose have this positions
     * @return array
     */
    public function getAll(){
        $q = "SELECT p.name,count(position_id) as total_position,p.id
            FROM position as p
            LEFT JOIN user as u ON u.position_id=p.id
            GROUP BY p.id";
        $result = $this->fetchAll($q);

        return $result;
    }
    
    /**
     * Insert into base new Positions
     * @param string $positionName
     * @return bool
     */
    public function insertPosition($positionName){
        $add= "INSERT INTO `position` (`name`)
            VALUES (:positionName)";
        $params=array();
        $params['positionName']=$positionName;
        $result=$this->execute($add, $params);
        return $result;
    }
    
    /**
     * Get from base one position
     * @param integer $id
     * @return array|false
     */
    public function getPosition($id){
        $q= "SELECT name,id
            FROM position
            WHERE id=:id";
        $params=array();
        $params['id']=$id;
        $result = $this->fetchOne($q,$params);
        return $result;
    }
    
    /**
     * Update position into base
     * @param integer $id
     * @param name $position
     * @return bool
     */
    public function savePosition($id,$position){
        $edit = "UPDATE position
            SET name=:position
            WHERE id = :id";
        $params=array();
        $params['position']=$position;
        $params['id']=$id;
        $result=$this->execute($edit, $params);
        return $result;
    }
    
    /**
     * Delete position from base
     * @param integer $id
     * @return bool
     */
    public function deletePosition($id){
        $update = "UPDATE user
            SET position_id=0
            WHERE position_id=:id";
        $delete ="DELETE
            FROM position
            WHERE id=:id";
        $params = array();
        $params['id']=$id;
        $update = $this->execute($update, $params);
        $result = false;
        if ($update) {
            $result = $this->execute($delete, $params);
        }

        return $result;
    }

    /**
     * Set position and date on registration or change userinfo 
     * @param integer $userId
     * @param integer $userPos
     * @param string $date
     * @return bool
    */
    public function savePositionToHistory($userId, $userPos, $date = 0){
        $q = "INSERT INTO `positions_history` VALUES (NULL, :user_id, :position_id, :date ) ";
        $params = array();
        $params['user_id'] = $userId;
        $params['position_id'] = $userPos;
        if ( $date ){
            $params['date'] = $date;
        } else {
            $params['date'] = date('Y-m-d');
        }
        $result = $this->execute($q, $params);
        return $result;
    }

    /**
     * Get latest actual position for current month
     * @param integer $userId
     * @param string $date
     * @return int
    */
    public function getLatestActualPositionForCurrMonth($userId, $date){
        $params = array();
        $date1 = $date.'-01';
        $date2 = $date.'-31';
        $q = "SELECT * FROM `positions_history` WHERE `user_id` = :user_id AND `date` BETWEEN :date1 AND :date2
            ORDER BY `date` DESC, `id` DESC";
        $params['date1'] = $date1;
        $params['date2'] = $date2;
        $params['user_id'] = $userId;  
        $result = $this->fetchOne($q,$params);
        return $result['position_id'];
    }
}
?>