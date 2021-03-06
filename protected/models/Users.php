<?php
namespace models;
use core\Db;
use core\Model;

class Users extends Model{

    /**
     * Get all unregistered users in system
     * @return array
     */
    public function getAllUnregistered(){
        $q= "SELECT id, name
            FROM `tc-db-main`.`personal`
            WHERE type='EMP'
              AND status='AVAILABLE'
            AND id!=ALL(SELECT `personal_id` FROM `user`)
            ORDER BY name";
        $result = $this->fetchAll($q);

        return $result;
    }

    /**
     * Get all registered users in system or from $firstElement to $firstElement+$elementsCount optional
     * @param integer $firstElement
     * @param integer $elementsCount
     * @return array
     */
    public function getRegistered($firstElement=0, $elementsCount=0){
        $params = array();
        $q= "SELECT
              u.id,
              t.id as personal_id,
              u.second_name as s_name,
              u.first_name as f_name,
              d.name as department,
              p.name as position,
              u.email as email
            FROM `user` u
            JOIN `tc-db-main`.`personal` t
              ON u.personal_id = t.id
            LEFT JOIN `position` p
              ON u.position_id = p.id
            LEFT JOIN `department` d
              ON u.department_id = d.id
            ORDER BY t.NAME ";

        if ($elementsCount){
            $q.="LIMIT :firstElement, :elementsCount";
            $params['firstElement'] = $firstElement;
            $params['elementsCount'] = $elementsCount;
        }

        $result = $this->fetchAll($q, $params);
        return $result;
    }

    /**
     * Get amount all registered users
     * @return array
     */
    public function getAllRegisteredCount(){
        $q = "SELECT count(id) AS count
              FROM user";
        $result = $this->fetchOne($q);
        return $result;
    }

    /**
     * Get all users which names contain searchstring
     * @param string $name
     * @return array
     */
    public function searchByName($name){
        $searchName = '%' . $name . '%';
        $q="SELECT u.second_name as s_name,
                u.first_name as f_name,
                u.id,
                u.department_id as dep_id,
                d.name as dep,
                p.name as pos
            FROM `user` u
            LEFT JOIN department as d
              ON u.department_id = d.id
            LEFT JOIN position as p
              ON u.position_id = p.id
            WHERE  CONCAT(u.second_name, u.first_name) LIKE :searchName

            ORDER BY CONCAT(u.second_name, u.first_name)
        ";
        $params=array();
        $params['searchName']=$searchName;
        $result = $this->fetchAll($q,$params);

        return $result;
    }

    /**
     * Add new user
     * @param string $user
     * @param string secondName
     * @param string firstName
     * @param string middleName
     * @param string $email
     * @param string $hash
     * @param string $salt
     * @param integer $position
     * @param integer $department
     * @param integer $tel
     * @param string $bday
     * @param bool $is_shown
     * @return bool
     */
    public function insertUsers($user, $secondName, $firstName, $middleName, $email, $hash, $salt, $position, $department, $tel, $bday, $swork, $ework, $is_shown, $halftime, $timesheetid){
        $add="INSERT INTO user(personal_id, first_name, second_name, middle_name, position_id, email, timesheetid, password, salt, department_id, created, birthday, startwork, endwork, phone, is_shown, halftime)
            VALUES (:user, :firstName, :secondName, :middleName, :position,:email, :timesheetid, :hash,:salt,:department, NOW(), :bday, :startwork, :endwork, :tel, :is_shown, :halftime)";
        $params=array();
        $params['user'] = $user;
        $params['secondName'] = $secondName;
        $params['firstName'] = $firstName;
        $params['middleName'] = $middleName;
        $params['position'] = $position;
        $params['email'] = $email;
        $params['hash'] = $hash;
        $params['salt'] = $salt;
        $params['department'] = $department;
        $params['bday'] = $bday;
        $params['startwork'] = $swork;
        $params['endwork'] = $ework;
        $params['tel'] = $tel;
        $params['is_shown'] = $is_shown;
        $params['halftime'] = $halftime;
        $params['timesheetid'] = $timesheetid;


        $result = $this->execute($add,$params);
        return $result;
    }

    /**
     * Validation of the contents of fields
     * @param string $email
     * @param integer $tel
     * @param integer $position
     * @param integer $department
     * @return array
     */
    public function checkUserAttr($email, $position, $department, $timesheetid = 1){
        $errors = array();
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email';
        }
        if (!$timesheetid || !$this->checkTimesheetId($timesheetid)) {
            $errors[] = 'Табельный номер уже существует';
        }

        if (!$position) {
            $errors[] = 'Должность';
        }

        if (!$department) {
            $errors[] = 'Отдел';
        }
        return $errors;
    }

    /** Check unique timesheetd
     * @param int $timesheetid
     * @return bool
     */
    public function checkTimesheetId($timesheetId)
    {
        $q = "SELECT timesheetid FROM user WHERE timesheetid = :timesheetid";
        $params['timesheetid'] = $timesheetId;
        $result = $this->fetchOne($q, $params);
        if (is_numeric($result['timesheetid']) && $result != 0) {
            return false;
        } else {
            return true;
        }
    }

    /** Get all user attributes by email
     * @param string $email
     * @return array
     */
    public function getInfoByEmail($email){
        $q="SELECT *
            FROM `user`
            WHERE email=:email";
        $params = array();
        $params['email']=$email;
        $result = $this->fetchOne($q,$params);

        return $result;
    }

    /**
     * Get user attributes by codekey
     * @param integer $codekey
     * @return array
     */
    public function getInfoByCodeKey($codekey){
        $codekey = (int) $codekey;
        $q="SELECT u.id, u.personal_id, u.email, u.password, u.salt
            FROM `user` u
            JOIN `tc-db-main`.`personal` t ON u.personal_id = t.id
            WHERE SUBSTRING( HEX(`CODEKEY`) , 5, 4 ) = HEX($codekey)";
        $result = $this->fetchOne($q);
        return $result;
    }

    /**
     * Get list etrys and exits in date range
     * @param integer $userId
     * @param string $fromDate
     * @param string $toDate
     * @return array
     */
    public function getActions($userId, $fromDate, $toDate){
        $q = "SELECT id,
                logtime,
                emphint,
                DATE( logtime ) AS `day`,
                TIME( logtime ) AS `time`,
                SUBSTRING( HEX( `logdata` ) , 10, 1 ) as direction
            FROM `tc-db-log`.`logs`
            WHERE
              emphint = :userId
              AND DATE(logtime) >= FROM_UNIXTIME(:fromDate)
              AND DATE(logtime) <= FROM_UNIXTIME(:toDate)
            ORDER BY logtime ASC
            ";
        $params=array();
        $params['userId']=$userId;
        $params['fromDate']=$fromDate;
        $params['toDate']=$toDate;
        $result = $this->fetchAll($q,$params);
        return $result;
    }

    /**
     * Get all position name and id
     * @return array
     */
    public function getPositionsList(){
        $q ="SELECT name, id
             FROM position";

        $result = $this->fetchAll($q);
        return $result;
    }

    /**
     * Get all department name and id
     * @return array
     */
    public function getDepartmentsList(){
        $q = "SELECT name, id
              FROM department";

        $result = $this->fetchAll($q);
        return $result;

    }

    /**
     * Get user role by user_id
     * @param integer $userId
     * @return array
     */
    public function getUserRoles($userId){
        $q = "SELECT r.name, r.id
            FROM users_roles as ur
            JOIN role as r ON ur.role_id = r.id
            JOIN user as u ON ur.user_id = u.id
            WHERE u.id = :userId";
        $params=array();
        $params['userId']=$userId;
        $result = $this->fetchAll($q,$params);
        return $result;
    }

    /**
     * Get all user attributes by user_id
     * @param integer $userId
     * @return array
     */
    public function getUserInfo($userId){
        $q = "SELECT
              u.id,
              u.personal_id,
              u.first_name,
              u.second_name,
              u.middle_name,
              u.position_id,
              u.department_id,
              u.timesheetid,
              u.password,
              u.salt,
              u.second_name as s_name,
              u.first_name as f_name,
              u.email,
              d.name as department,
              p.name as position,
              u.birthday,
              u.startwork,
              u.endwork,
              u.phone,
              u.created,
              u.is_shown,
              u.halftime
            FROM `user` u
            LEFT JOIN `position` p
              ON u.position_id = p.id
            LEFT JOIN `department` d
              ON u.department_id = d.id
            WHERE u.id = :id
            ";
        $params=array();
        $params['id']=$userId;
        $result = $this->fetchOne($q,$params);
        if ( $result['startwork'] != "0000-00-00" ){
            $result['startwork'] = strtotime($result['startwork']);
        }

        if ( $result['endwork'] != "0000-00-00" ){
            $result['endwork'] = strtotime($result['endwork']);
        }

        if ( $result['birthday'] != "0000-00-00" ){
            $result['birthday'] = strtotime($result['birthday']);
        }
        return $result;
    }

    /**
     * Get user status by user_id
     * @param integer $id
     * @return array
     */
    public function getUserStatus($id){
        $q = "SELECT SUBSTRING( HEX(`logdata`) , 10, 1 ) as status
            FROM `tc-db-log`.`logs`
            JOIN `user` u
                ON u.id= :id
            WHERE emphint = u.personal_id
            AND logtime  >= NOW() - INTERVAL 1 DAY
            ORDER BY logtime DESC
            LIMIT 1";
        $params=array();
        $params['id']=$id;

        $result = $this->fetchOne($q,$params);
        if(isset($result['status'])){
        return $result['status'];
        }
        else{
            return 1;
        }
    }

    /**
     * Get all user statuses 
     * @return array
     */
    public function getUserStatuses(){
        $q = "SELECT * FROM status";
        $result = $this->fetchAll($q);
        return $result;
    }

    /**
     * Get permission for role by role_id
     * @param integer $roleId
     * @return array
     */
    public function getRolePermissions($roleId){
        $q = "SELECT p.key
                FROM roles_permissions rp
                INNER JOIN role r ON rp.role_id = r.id
                INNER JOIN permission p ON rp.permission_id = p.id
                WHERE role.id = :roleId";
        $params=array();
        $params['roleId']=$roleId;
        $result = $this->fetchAll($q,$params);
        return $result;
    }

    /**
     * Add timeoff for current user
     * @param integer $userId
     * @param integer $type
     * @param string $data
     * @return array
     */
    public function setTimeoffs($userId, $type, $data, $time){
        $q = 'INSERT INTO users_statuses(user_id, status_id, date, time) VALUES (:userId, :type, :date, :time) ';
        $params = array();
        $params['userId'] = $userId;
        $params['type'] = $type;
        $params['date'] = $data;
        $params['time'] = $time;
        $result = $this->execute($q, $params);
        return $result;
    }

    /**
     * Get timeoff for current user by id
     * @param integer $userId
     * @param string $date
     * @param integer $type
     * @return array
     */
    public function getTimeoffsByUserId($userId, $date, $type = 0){
        $numDays = date('t', strtotime($date)) - 1;
        $startDate = date("Y-m-d", strtotime($date));
        $endDate = date("Y-m-d", (strtotime($startDate) + $numDays*24*60*60 ));
        $params = array();
        $params['id'] = $userId;
        $params['date1'] = $startDate;
        $params['date2'] = $endDate;
        $q = "SELECT *
            FROM users_statuses AS u
        LEFT JOIN status AS s ON u.status_id = s.id
        WHERE u.user_id in
        (SELECT id FROM user WHERE id = :id)
        AND u.date
        BETWEEN :date1 AND :date2 ";

        if($type){
            $params['type'] = $type;
            $q = $q." AND u.status_id = :type";
        }
        
        $result = $this->fetchAll($q, $params);
        return $result;
    }

    /**
     * Edit current user
     * @param integer $id
     * @param string secondName
     * @param string firstName
     * @param string middleName
     * @param integer $position
     * @param string $email
     * @param integer $department
     * @param string $birthday
     * @param integer $phone
     * @param bool $is_shown
     * @return bool
     */
    public function editUser($id, $secondName, $firstName, $middleName, $position, $email, $department, $birthday, $startwork, $endwork, $phone, $is_shown, $halftime, $timesheetid){
        $params = array();
        $params['id'] = $id;
        $params['secondName'] = $secondName;
        $params['firstName'] = $firstName;
        $params['middleName'] = $middleName;
        $params['position'] = $position;
        $params['email'] = $email;
        $params['department'] = $department;
        $params['birthday'] = $birthday;
        $params['startwork'] = $startwork;
        $params['endwork'] = $endwork;
        $params['phone'] = $phone;
        $params['is_shown'] = $is_shown;
        $params['halftime'] = $halftime;
        $params['timesheetid'] = $timesheetid;
        $q= "UPDATE user
            SET position_id = (:position),
            second_name = (:secondName),
            first_name = (:firstName),
            middle_name = (:middleName),
            email = (:email),
            department_id = (:department),
            birthday = (:birthday),
            startwork = (:startwork),
            endwork = (:endwork),
            phone = (:phone),
            is_shown = (:is_shown),
            halftime = (:halftime),
            timesheetid = (:timesheetid)
            WHERE id = (:id)";
        $result = $this->execute($q, $params);
        return $result;
    }

    /**
     * Change password
     * @param integer $id
     * @param string $newPass
     * @return bool
     */
    public function editUserPass($id,$newPass,$salt){
        $params = array();
        $params['id'] = $id;
        $params['newPass'] = $newPass;
        $params['salt']=$salt;
        $q = "UPDATE user SET password = (:newPass), salt = (:salt) WHERE id = (:id)";
        $result = $this->execute($q, $params);
        return $result;
    }

    /**
     * Delete user by id
     * @param integer $id
     * @return bool
     */
    public function deleteUser($id){
      $params = array();
      $params['id'] = $id;
      $q = "DELETE FROM user WHERE id = (:id)";
      $result = $this->execute($q, $params);
      return $result;
    }

    /**
     * Get personal_id by id
     * @param integer $id
     * @return integer
     */
    public function getPersonalId($id){
        $params =array();
        $params['id'] = $id;
        $q = "SELECT personal_id FROM user WHERE id = :id";
        $result = $this->fetchOne($q, $params);
        if (isset($result['personal_id'])){
            return $result['personal_id'];
        } else {
            return false;
        }
    }

    /**
     * Get id by personal_id
     * @param integer $personalId
     * @return integer
     */
    public function getId($personalId){
        $params =array();
        $params['personalId'] = $personalId;
        $q = "SELECT id FROM user WHERE personal_id = :personalId";
        $result = $this->fetchOne($q, $params);
        if (isset($result['id'])){
            return $result['id'];
        } else {
            return false;
        }
    }

    public function getTimeOffByUserId($id){
        $params['id']=$id;
        $q= "SELECT date
            FROM users_statuses
            WHERE user_id=:id";
        $result = $this->fetchAll($q, $params);
        return $result;
    }
    
    /**
     * Get time by timeoffs id
     * @param integer $type
     * @return array time
     */
    public function getTimeByType($type)
    {
        $q="SELECT addtime FROM `status` WHERE type_id=:type";
        $params['type'] = $type;
        $result = $this->fetchOne($q, $params);
        return $result;
    }

    /**
     * Get all users for timesheet
     * @return array
    */
    public function getAllUsersForTimesheet(){
        $q = "SELECT `id`, `first_name`, `second_name`, `middle_name`, `position_id`,  `startwork`, `endwork`
            FROM `user` WHERE `is_shown` = 1";
        $result = $this->fetchAll($q);
        return $result;
    }

    /**
     * Get user timesheetId by userId
     * @return array
    */
    public function getUserTimesheetIdByUserId($userId)
    {
        $q = "SELECT timesheetid FROM user WHERE id = :user_id";
        $params['user_id'] = $userId;
        $result = $this->fetchOne($q, $params);
        return $result['timesheetid'];
    }
        
    /** 
     * Return department id and name for user
     * @param integer $userId
     * @return array department name and id
     */
    public function getDepartmentByUser($userId)
    {
        $query = "SELECT d.name, u.department_id
                    FROM  `user` AS u
                    JOIN department AS d ON d.id = u.department_id
                    WHERE u.id = ( :user_id )";
        $params['user_id'] = $userId;
        $result = $this->fetchOne($query, $params);
        
        return $result;
    }
    
    /**
     * Return department id, permission id and permission key  for user
     * @param integer $userId
     * @return array department id, permission id and permission key 
     */
    public function getUserDepartmentPermission($userId)
    {
        $query = "SELECT u.department_id, u.permission_id, p.key
                    FROM  `user_department_permission` AS u
                    JOIN `permission` AS p ON p.id = u.permission_id
                    WHERE u.user_id = ( :user_id )";
        $params['user_id'] = $userId;
        $result = $this->fetchAll($query, $params);
        
        return $result;
    }
    
    /**
     * Get all registered users in system from depatment
     * @param integer $depId
     * @return array
     */
    public function getDepUsers($depId)
    {
        $q= "SELECT
              u.id,
              t.id as personal_id,
              t.NAME as name,
              d.name as department,
              p.name as position,
              u.email as email
            FROM `user` u
            JOIN `tc-db-main`.`personal` t
              ON u.personal_id = t.id
            LEFT JOIN `position` p
              ON u.position_id = p.id
            LEFT JOIN `department` d
              ON u.department_id = d.id
            WHERE u.department_id = ".$depId."
            ORDER BY t.NAME ";

        $result = $this->fetchAll($q);
    }

    /**
     * Get last entries/exists
     * @param $time
     * @return array
     */
    public function getLastLogs($time)
    {
        $result = array();
        if ($time) {
            $q = "SELECT id, name, locationzone AS inside, UNIX_TIMESTAMP(locationact) AS time
                FROM `tc-db-main`.personal
                WHERE type = 'EMP' AND status = 'AVAILABLE' AND locationact > FROM_UNIXTIME(:time)
                ORDER BY locationact";
            $result = $this->fetchAll($q, array(
                'time' => $time
            ));
        }
        
        return $result;
    }
}
