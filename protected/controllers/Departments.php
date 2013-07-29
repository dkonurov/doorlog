<?php
namespace controllers;

use core\Acl;
use core\Controller;
use core\FlashMessages;
use models\Departments as DepartmentModel;
use models\Users as UserModel;
use controllers\Main as Time;
use core\Utils;

class Departments extends Controller {

    /**
     * Shows all departments
     * @return void
     */
    public function indexAction() {
        if(!Acl::checkPermission('departments_view')){
                $this->render("errorAccess.tpl");
        }
        $departmentsModel =  new DepartmentModel();
        $departments =  $departmentsModel->getAll();

        $this->render("Departments/index.tpl" , array('departments' => $departments));
    }

    /**
     * Shows department add form
     * @return void
     */
    public function addAction() {
        if(!Acl::checkPermission('departments_add')){
            $this->render("errorAccess.tpl");
        }
        $departmentsModel =  new DepartmentModel();
        if(isset($_POST['depName']) && $_POST['depName']){
            $depName = $_POST['depName'];
            if ($departmentsModel->createDep($depName)){
                FlashMessages::addMessage("Отдел успешно добавлен.", "success");
            } else {
                FlashMessages::addMessage("Произошла ошибка. Отдел не был добавлен.", "error");
            }
        }
        Utils::redirect("/departments");
    }

    /**
     * Shows department edit form
     * @return void
     */
    public function editAction() {
        if(!Acl::checkPermission('departments_edit')){
                $this->render("errorAccess.tpl");
        }
        $departmentId = $_GET['id'];
        $departmentsModel =  new DepartmentModel();
        if ((isset($_POST['depName']) && $_POST['depName'])) {
            $depName = $_POST['depName'];
            $users = $departmentsModel->getUsers($departmentId);
            $departmentUsersPermission = array();
            $departmentsPermissions = $departmentsModel->getDepartmentPermissions();
            foreach ($users as $user) {
                foreach ($departmentsPermissions as $permission) {
                    if (isset($_POST["{$permission['key']}_{$user['id']}"])) {
                        $departmentUsersPermission[$user['id']][$permission['key']] = $_POST["{$permission['key']}_{$user['id']}"];
                    }
                }
                $departmentUsersPermission[$user['id']]['null'] = null;
            }
            $departmentsModel->startTransaction();
            try {
                $departmentsModel->editDep($depName, $departmentId);
                foreach ($departmentUsersPermission as $userId => $userPermission) {
                    $permissions = $departmentsModel->getUserDepartmentPermission($userId);
                    $permissionUsersDepartment = array();
                    foreach ($permissions as  $permissionsForDepartment) {
                        foreach ($departmentsPermissions as $permission) {
                            if ($permissionsForDepartment['permission_id'] == $permission['id'])
                                $permissionUsersDepartment[$permissionsForDepartment['user_id']][$permission['key']] = $permissionsForDepartment['permission_id'];
                        }
                    }
                    if (!empty($permissions)) {
                        foreach ($permissionUsersDepartment as $permissionsForDepartment) {
                            foreach ($departmentsPermissions as $permission) {
                                if (isset($userPermission[$permission['key']])) {
                                    if (!isset($permissionsForDepartment[$permission['key']])) {
                                        $departmentsModel->insertPermissions($userId, $permission['id'], $departmentId);
                                    }
                                } else {
                                    if (isset($permissionsForDepartment[$permission['key']])) {
                                        $departmentsModel->deletePermission($userId, $permission['id']);
                                    }
                                }
                            }
                        }
                    } else {
                        foreach ($departmentsPermissions as $permission) {
                            if (isset($userPermission[$permission['key']])) {
                                $departmentsModel->insertPermissions($userId, $permission['id'], $departmentId);
                            }
                        }
                    }
                }
                $departmentsModel->commit();
                FlashMessages::addMessage("Отдел успешно отредактирован.", "success");
            } catch (\Exception $e) {
                $departmentsModel->rollBack();
                FlashMessages::addMessage("Отдел не был отредактирован", "error");
            }
            Utils::redirect("/departments");
        } else {
            $departments = $departmentsModel->getDepById($departmentId);
            $users = $departmentsModel->getUsers($departmentId);

            $sortedUsers = array();
            $permissionForDepartments = $departmentsModel->getDepartmentPermissions();
            foreach ($users as $user) {
                $sortedUsers[$user['id']]['name'] = $user['name'];
                $permissions = $departmentsModel->getUserDepartmentPermission($user['id']);
                foreach ($permissions as $permission) {
                    foreach ($permissionForDepartments as $departmentPermision){
                        if ($departmentPermision['id'] == $permission['permission_id']){
                            $sortedUsers[$user['id']][$departmentPermision['key']] = $permission['permission_id'];
                        }
                    }
                }
            }
            $this->render("Departments/edit.tpl" , array('departments' => $departments, 'users' => $sortedUsers, 'permissions' => $permissionForDepartments));
        }
    }

    /**
     * Delete department
     * @return void
     */
    public function deleteAction(){
        if(!Acl::checkPermission('departments_delete')){
            $this->render("errorAccess.tpl");
        }
        $id = $_POST['id'];
        $departmentsModel =  new DepartmentModel();
        $totalUsers =  $departmentsModel->getTotalUsers($id);
        if($totalUsers['total_users']==0){
            $delete = $departmentsModel->dellDep($id);
            if ($delete) {
                FlashMessages::addMessage("Отдел успешно удален.", "success");
                Utils::redirect("/departments");
            } else FlashMessages::addMessage("При удалении отдела произошла ошибка.", "error");
        }
        else {
            FlashMessages::addMessage("Отдел не может быть удален, пока в нём есть пользователи.", "error");
            Utils::redirect("/departments/edit?id=$id");
        }
    }

    /**
     * Shows one department with all of it users
     * @return void
     */
    public function showAction(){
        $time  = new Time();
        $department =  new DepartmentModel();
        $userModel = new UserModel();
        if(isset($_GET['id']) && $_GET['id']){
            $depId = $_GET['id'];
        }
        $users = $department->getUsers($depId);
        sort($users);
        foreach($users as &$user) {
            $userId = $user['id'];
            $userPersonalId=$user['personal_id'];
            $weekTime = $userModel->getUserStatus($userId);
            $user['status'] = $weekTime;
            $user['time'] = $time->getWeekInfo($userPersonalId, date('Y-m-d'));
        }
        $name = $department->getDepById($depId);
        $userId = $_COOKIE['id'];
        $this->render("Departments/show.tpl" , array('users' => $users, 'depName' => $name, 'userId'=>$userId));
    }
}
