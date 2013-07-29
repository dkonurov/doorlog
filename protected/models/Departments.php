<?php

namespace models;
use core\Db;
use core\Model;

class Departments extends Model {

        /**
         * Get all departments(id, name) and amount users
         * @return array
         */
        public function getAll(){
            $q = "SELECT
                d.id,
                d.name,
                count(u.id) as total_users
                FROM department as d
                LEFT JOIN user as u ON u.department_id = d.id
                LEFT JOIN `tc-db-main`.personal as t ON t.id = u.personal_id
                GROUP BY d.id";
            $result = $this->fetchAll($q);
        return $result;
        }

        /**
         * Get all depatments name and id
         * @return array
         */
        public function getMenuDepartments(){
            $q = "SELECT name, id
                FROM department";
                $result = $this->fetchAll($q);
            return $result;
        }

        /**
         * Get department name by id
         * @param integer $id
         * @return array
         */
        public function getDepById($id){
            $q = "SELECT * FROM department WHERE id = (:id)";
            $params = array();
            $params['id'] = $id;
            $result = $this->fetchOne($q, $params);
            return $result;
        }

        /**
         * Add new departament
         * @param string $depName
         * @return bool
         */
        public function createDep($depName){
            $q = "INSERT INTO department(name) VALUES(:depName)";
            $params = array();
            $params['depName'] = $depName;
            $result = $this->execute($q, $params);
            return $result;
            }

        /**
         * Delete departament
         * @param integer $id
         * @return bool
         */
        public function dellDep($id){
            $params = array();
            $params['id'] = $id;
            $q = "DELETE FROM department WHERE id = (:id)";
            $q1 = "UPDATE user SET department_id = '0' WHERE department_id = (:id) ";
            $result = $this->execute($q, $params);
            $result1 = $this->execute($q1, $params);
            return $result;
        }

        /**
         * Edit departament
         * @param string $newname
         * @param integer $id
         * @return bool
         */
        public function editDep($newname, $id){
            $params = array();
            $params['id'] = $id;
            $params['newname'] = $newname;
            $q = "UPDATE department SET name = (:newname) WHERE id = (:id) ";
            $result = $this->execute($q, $params);
            return $result;
        }

        /**
         * Get all users in current departament
         * @param integer $depId
         * @return array
         */
        public function getUsers($depId){
            $attr = array();
            $q = "SELECT p.name , pos.name as position, u.personal_id, u.id
                FROM `tc-db-main`.personal as p
                LEFT JOIN `savage-db`.user as u
                ON u.personal_id = p.id
                LEFT JOIN `savage-db`.position as pos
                ON u.position_id = pos.id
                WHERE u.department_id = :depId";
            $attr['depId'] = $depId;
            $result = $this->fetchAll($q, $attr);
            return $result;
        }

        public function getTotalUsers($id){
            $params = array();
            $params['id'] = $id;
            $q = "SELECT count(id) as total_users
                FROM user
                WHERE department_id = :id";
            $result = $this->fetchOne($q, $params);
            return $result;
        }

        /**
         * Add permission for user in department
         * @param integer $userId
         * @param integer $permissionId
         * @param integer $departmentId
         * @return boolean result
         */
        public function insertPermissions($userId, $permissionId, $departmentId)
        { 
            $query = "INSERT INTO  user_department_permission(user_id, department_id, permission_id) 
                VALUES(:user_id, :department_id, :permission_id)";
            $params['user_id'] = $userId;
            $params['permission_id'] = $permissionId;
            $params['department_id'] = $departmentId;
            $result = $this->execute($query, $params);
            return $result;
        }

        /**
         * Delete from base permissions for users
         * @param integer $userId
         * @param integer $permissionId
         * @return boolean result
         */
        public function deletePermission($userId, $permissionId)
        {
            $query = "DELETE FROM user_department_permission
                WHERE user_id = (:user_id) AND
                permission_id = (:permission_id)";
            $params['user_id'] = $userId;
            $params['permission_id'] = $permissionId;
            $result = $this->execute($query, $params);
            return $result;
        }

        /**
         * Select from base all users in departments and their permissions
         * @param integer $departmentId
         * @return array of users permissions
         */
        public function getUserDepartmentPermission($userId)
        {
            $query = "SELECT * FROM user_department_permission
                WHERE user_id = :user_id";
            $params['user_id'] = $userId;
            $result = $this->fetchAll($query, $params);
            return $result;
        }

        /**
         * Return all permissions for departments
         * @return array permissions
         */
        public function getDepartmentPermissions()
        {
            $query = "SELECT * FROM permission WHERE department_permission = 1";
            $result = $this->fetchAll($query);
            return $result;
        }        

        /**
         * Delete all permissions for user
         * @param integer $userId
         * @return boolean result
         */
        public function deleteAllPermissionsForUser($userId)
        {
            $query = "DELETE FROM user_department_permission
                WHERE user_id = (:user_id)";
            $params['user_id'] = $userId;
            $result = $this->execute($query, $params);
            return $result;
        }
}