<?php

namespace core;

use models\Users;
use models\Roles;

class Acl {
    static function getUserRoles($userId){
        $userRoles = array();
        $obj = new Users();
        $result = $obj->getUserRoles($userId);

        if (!empty( $result ) ){
            foreach ($result as $row) {
                $userRoles[$row['name']] = $row['id'];
            }
            return $userRoles;
        }

        return false;
    }

    static function getRolePermissions($roleId){
        $rolePermissions = Array();
        $obj = new Users();
        $result = $obj->getRolePermissions($roleId);

        if (!empty( $result ) ){
            foreach ($result as $row) {
                $rolePermissions[] = $row;
            }

            return $rolePermissions;

        }

        return false;
    }

    static function getUserPermissions($user){
        $permissionsArr = array();
        $rolePermissions = array();
        $userRoles = self::getUserRoles($user);

        if ($userRoles){
            $obj = new Roles();

            foreach ($userRoles as $role) {
            $rolePermissions[] = $obj->getRolePermissions($role);
            }

            foreach($rolePermissions as $permissions){
                foreach($permissions as $permission){
                    $permissionsArr[] = $permission['key'];
                }
            }

            $permissionsArr = array_unique($permissionsArr);
            return $permissionsArr;
        }

        return false;
    }
    
    static function getUserDepartmentPermissions($userId){
        $userDP = array();
        $obj = new Users();
        $result = $obj->getUserDepartmentPermission($userId);
    
        if (!empty( $result ) ){
            foreach ($result as $key => $row) {
                $userDP['permissions'][$key] = $row['key'];
            }
            $userDP['department_id'] = $result[0]['department_id'];
        } else {
            $userDP['permissions'] = array();
        }
    
        return $userDP;
    }

    static function checkPermission($permission){
        $userInfo = Registry::getValue('user');
                        
        $checkPermissions = self::checkRolePermission($permission);
        $checkDepartmentPermissions = isset($userInfo['department_permissions']) && is_array($userInfo['department_permissions'])&& in_array($permission, $userInfo['department_permissions']['permissions']);
        
        $isSuccess = $checkPermissions || $checkDepartmentPermissions;
        return $isSuccess;
    }
    
    static function checkRolePermission($permission){
        $userInfo = Registry::getValue('user');
    
        $isSuccess = isset($userInfo['permissions']) &&
            is_array($userInfo['permissions']) &&
            in_array($permission, $userInfo['permissions']);
            
        return $isSuccess;
    }
    
    static function checkDepartmentPermission($permission, $departmentId){
        $userInfo = Registry::getValue('user');
    
        $isSuccess = isset($userInfo['department_permissions']) &&
            is_array($userInfo['department_permissions']) &&
            in_array($permission, $userInfo['department_permissions']['permissions']) &&
            ($userInfo['department_permissions']['department_id'] == $departmentId);

        return $isSuccess;
    }
}

?>
