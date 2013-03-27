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
                $userRoles[] = $row['name'];
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
        $permissionsArr = Array();
        $rolePermissions = Array();
        $userRoles = self::getUserRoles($user);

        if ($userRoles){
            $obj = new Roles();

            foreach ($userRoles as $role) {
            $rolePermissions[] = $obj->getRolePermissions($role);
            }

            foreach($rolePermissions as $permissions){
                foreach($permissions as $permission){
                    $permissionsArr[] = $permission;
                }               
            }

            $permissionsArr = array_unique($permissionsArr);
            return $permissionsArr;
        }

        return false;
    }

    static function checkPermission($permission){
        $userInfo = Registry::getValue('user');

        if(isset($userInfo['permissions']) && in_array($permission, $userInfo['permissions'])){
            return true;
        }

            return false;
    }
}

?>
