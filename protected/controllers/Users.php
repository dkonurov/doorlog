<?php

namespace controllers;

use core\Controller;
use models\Users as UsersModel;
use models\Positions;
use models\Departments;
use models\StatusesType;
use core\Utils;
use core\FlashMessages;
use core\Authentication;
use core\Registry;
use core\Db;
use models\Roles as RolesModel;
use core\Acl;

class Users extends Controller {
    /**
     * Render page for all users
     * @return void
     */
    public function indexAction() {
        if(!Acl::checkPermission('users_view')){
            $this->render("errorAccess.tpl");
        }
        $users = new UsersModel();
        $firstElement = 0;
        $val = Registry::getValue('config');
        $elementsCount = $val['items_per_page'];

        if (isset($_GET['page']) && $_GET['page'] != 1) {
            $firstElement = ($_GET['page'] - 1) * $elementsCount;
            $currentPage = $_GET['page'];
        } else {
            $currentPage = 1;
        }

        $registeredCount = $users->getAllRegisteredCount();
        $pagesCount = ceil($registeredCount['count'] / $elementsCount);

        $registeredUsers = $users->getRegistered($firstElement, $elementsCount);

        $this->render("Users/index.tpl", array(
            'users' => $registeredUsers,
            'pagesCount' => $pagesCount,
            'currentPage' => $currentPage));
    }

    /**
     * If logout redirect in root
     * @return void
     */
    public function logoutAction() {
        $auth = new Authentication;
        $auth->logout();
        Utils::redirect('/');
    }

    /**
     * Shows login form
     * @return void
     */
    public function loginAction() {
        if (isset($_POST['login']) && $_POST['login'] && isset($_POST['password']) && $_POST['password']) {
            $usersModel = new UsersModel();
            if (filter_var($_POST['login'], FILTER_VALIDATE_EMAIL)) {
                $userInfo = $usersModel->getInfoByEmail($_POST['login']);
            } else {
                $userInfo = $usersModel->getInfoByCodeKey((int) $_POST['login']);
            }
            if ($userInfo) {
                $hash = $this->generateHash($_POST['password'], $userInfo['salt']);

                if ($hash == $userInfo['password']) {
                    $auth = new Authentication();
                    $auth->grantAccess($userInfo['id'], $hash);
                    Utils::redirect('/');
                } else {
                    FlashMessages::addMessage("Неверный пароль.", "error");
                }
            } else {
                FlashMessages::addMessage("Неверный пользователь.", "error");
            }
        }

        $this->render("Users/login.tpl");
    }

    /**
     * This function is generate hash
     * @param string $password
     * @param string $salt
     * @return string|bool
     */
    public function generateHash($password, $salt) {
        return sha1($salt . $password);
    }

    /**
     * This function is help search
     * @return void
     */
    public function autocompleteAction(){
        $autocomplete = new UsersModel;
        $name = $_GET['name'];
        $result = $autocomplete->searchByName($name);
        echo (json_encode($result));
    }

    /**
     * Render page for this user
     * @return void
     */
    public function showAction() {
        if(!Acl::checkPermission('users_view')){
            $this->render("errorAccess.tpl");
        }
        $timeoffs = array();
        $user = new UsersModel();
        $name = "";
        $date = date('Y-m');
        $id = '';

        if (isset($_GET['date']) && isset($_GET['id']) && !empty($_GET['date']) && !empty($_GET['id']) ){
            $timeoffs = $user->getTimeoffsByUserId($_GET['id'], $_GET['date'], $_GET['type']);
            $date = $_GET['date'];
            $userInfo = $user->getUserInfo($_GET['id']);
            if ($userInfo) {
                $name = $userInfo['name'];
                $id = $_GET['id'];
            } else {
                FlashMessages::addMessage("Неверный id пользователя", "error");
            }
        }

        $statuses = $user->getUserStatuses();
        $timeoffsAttr = array('date' => $date, 'name' => $name, 'id' => $id);

        $userInfo = null;

        if(isset($_GET['id'])){
            $id = $_GET['id'];
            $getUser = new UsersModel;
            $userInfo = $getUser->getUserInfo($id);
        }

        if ($userInfo) {
            $userStatus = $getUser->getUserStatus($userInfo['personal_id']);
            $userInfo['status'] = $userStatus['status'];
        } else {
            FlashMessages::addMessage("Неверный id пользователя", "error");
        }
        $permission = null;
        if ($_GET['id']==$_COOKIE['id']){
            $permission = 1;
        }
        $this->render("Users/show.tpl", array('userInfo' => $userInfo, 'statuses'=> $statuses, 'id' => $id, 'timeoffs' => $timeoffs, 'timeoffsAttr' => $timeoffsAttr, 'permission' => $permission));
    }

    /**
     * Render page for add time offs
     * @return void
     */
    public function vacationAction(){
        if (!Acl::checkPermission('timeoffs_add')) {
            $this->render("errorAccess.tpl");
        }
        $userModel = new UsersModel();
        $userId = $_POST['id'];

        if (isset($_POST['from']) && isset($_POST['to']) && $_POST['from'] && $_POST['to']) {
            $from = $_POST['from'];
            $to = $_POST['to'];
            $type = $_POST['vtype'];
            if ($_POST['other_office']) {
                $time = $_POST['other_office'];
            } else {
                $addtime = $userModel->getTimeByType($type);
                $time = $addtime['addtime'];
            }
            $dateStart = strtotime($from);
            $dateFinish = strtotime($to);
            $sumDays = floor(($dateFinish - $dateStart) / (3600 * 24));

            $checkTime = true;
            $timeOffDate = $userModel->getTimeOffByUserId($userId);
            if (isset($timeOffDate)) {
                for ($i = 0; $i <= $sumDays; $i++) {
                    for ($k = 0; $k <= count($timeOffDate); $k++) {
                        $date =  date("o-m-d", $dateStart+((3600*24)*$i));
                        if ($timeOffDate[$k]['date'] == $date) {
                            $checkTime = false;
                            break;
                        }
                    }
                    if (!$checkTime) {
                        break;
                    }
                }
            }

            if ($checkTime) {
                $userModel->startTransaction();
                try {
                for ($i = 0; $i <= $sumDays; $i++) {
                    $date =  date("o-m-d", $dateStart+((3600*24)*$i));
                    $res = $userModel->setTimeoffs($userId, $type, $date, $time);
                }
                
                $userModel->commit();
                FlashMessages::addMessage("Отгул добавлен.", "success");
                } catch(\Exception $e) {
                    $userModel->rollBack();
                    FlashMessages::addMessage("Произошла ошибка. Отгул не был добавлен.", "error");
                }
            } else {
                FlashMessages::addMessage("На данный день(дни) отгул уже был добавлен. Отгул не был добавлен.", "error");
            }
        } else {
            FlashMessages::addMessage("Ошибка заполнения. Отгул не был добавлен.", "error");
        }
        Utils::redirect('/users/profile?id='.$userId);
    }

    /**
     * Render page for search users
     * @return void
     */
    public function searchAction(){
        if(isset($_GET['id']) && $_GET['id']){
            $id = $_GET['id'];
            Utils::redirect("/users/profile?id=$id");
        } else {
            $users = new UsersModel;
            $search = $users->searchByName($_GET['text']);
            foreach ($search as &$user) {
                $user['status'] = $users->getUserStatus($user['id']);
            }
            $this->render("Users/search.tpl", array('search' => $search, 'text' => $_GET['text']));
        }
    }

    /**
     * Render page for manage user
     * @return void
     */
    public function manageAction() {
        $editActionFlag = false;
        if(isset($_GET['id']) && $_GET['id']) {
            if (!Acl::checkPermission('users_edit')) {
                $this->render("errorAccess.tpl");
            }
            $editActionFlag = true;
        } else {
            if (!Acl::checkPermission('users_add')) {
                $this->render("errorAccess.tpl");
            }
        }
        
        $users = new UsersModel();
        $roles = new RolesModel();
        $departmentModel = new Departments();

        if (isset($_POST['position'])) {
            $workerType = $_POST['workertype'];
            $position = $_POST['position'];
            $firstName = $_POST['firstName'];
            $secondName = $_POST['secondName'];
            $middleName = $_POST['middleName'];
            $department = $_POST['department'];
            $timesheetId = $_POST['timesheetid'];
            $role = $_POST['role'];
            $email = $_POST['email'];
            $phone = $_POST['phone'];

            $birthday = $_POST['birthday'];
            if ($birthday != ""){
                $birthday = date('Y-m-d', strtotime($birthday));
            } else {
                $birthday = "0000-00-00";
            }

            $startWork = $_POST['startwork'];
            if ($startWork != ""){
                $startWork = date('Y-m-d', strtotime($startWork));
            } else {
                $startWork = "0000-00-00";
            }

            $endWork = $_POST['endwork'];
            if ($endWork != ""){
                $endWork = date('Y-m-d', strtotime($endWork));
            } else {
                $endWork = "0000-00-00";
            }

            if (isset($_POST['is_shown'])){
                $isShown = $_POST['is_shown'];
            } else {
                $isShown = 0;
            }

            if (isset($_POST['halftime'])){
                $halfTime = $_POST['halftime'];
            } else {
                $halfTime = 0;
            }
            $inputErrors = array();
            switch ($workerType) {
                case 1:
                    $inputErrors = $users->checkUserAttr($email, $position, $department, $timesheetId);
                    break;
                case 2:
                    $isShown = 0;
                    $timesheetId = 0;
                    $inputErrors = $users->checkUserAttr($email, $position, $department);
                    break;
                case 3:
                    $department = NULL;
                    $timesheetId = 0;
                    $isShown = 0;
                    $halfTime = 0;
                    break;
            }
            if ($inputErrors){
                $errorString = 'Ошибка заполнения поля: ' . implode(', ', $inputErrors).'.';
                FlashMessages::addMessage($errorString, "error");
            } else {
                if(isset($_GET['id']) && $_GET['id']){
                    $id = $_GET['id'];
                    $this->update($id, $secondName, $firstName, $middleName, $position, $role, $email, $department, $birthday, $startWork, $endWork ,$phone, $isShown, $halfTime, $timesheetId);
                } else {
                    if(isset($_POST['userId'])){
                        $user = $_POST['userId'];
                        $this->add($user, $secondName, $firstName, $middleName, $email, $position, $role, $department, $birthday, $startWork, $endWork ,$phone, $isShown, $halfTime, $timesheetId);
                    }
                }
            }
        }

        $unregisteredUsers = $users->getAllUnregistered();
        $posList = $users->getPositionsList();
        $sortedUsers = array();
        $depList = $users->getDepartmentsList();
        $rolesList = $roles->getAll();

        $sortedDepartments = array();
        foreach ($depList as $department) {
            $sortedDepartments[$department['id']] = $department['name'];
        }

        $sortedPositions = array();
        foreach ($posList as $position) {
            $sortedPositions[$position['id']] = $position['name'];
        }

        foreach ($unregisteredUsers as $user) {
            $sortedUsers[$user['id']] = $user['name'];
        }

        $sortedRoles = array();
        foreach ($rolesList as $role) {
            $sortedRoles[$role['id']] = $role['name'];
        }

        if($editActionFlag){
            $id = $_GET['id'];
            $userInfo = $users->getUserInfo($id);
            $userRole = $users->getUserRoles($id);
            $this->render("Users/manage.tpl", array(
                'userId' => $id,
                'userInfo' => $userInfo,
                'positions' => $sortedPositions,
                'departments' => $sortedDepartments,
                'roles' => $sortedRoles,
                'userRole' => $userRole
            ));
        } if(!$editActionFlag) {
            $this->render("Users/manage.tpl", array(
                'users' => $sortedUsers,
                'positions' => $sortedPositions,
                'departments' => $sortedDepartments,
                'roles' => $sortedRoles
            ));
        }
    }

    /**
     * This function add user in base
     * @param integer $user
     * @param string secondName
     * @param string firstName
     * @param string middleName
     * @param string $email
     * @param integer $position
     * @param integer $role
     * @param integer $department
     * @param string $birthday
     * @param string $phone
     * @param boolean $isShown
     * @return void
     */
    public function add($user, $secondName, $firstName, $middleName, $email, $position, $role, $department, $birthday, $startWork, $endWork ,$phone, $isShown, $halfTime, $timesheetId){

        $users = new UsersModel();
        $roles = new RolesModel();
        $pos = new Positions();

        $salt = Utils::createRandomString(5, 5);
        $password = Utils::createRandomString(8, 10);
        $hash = $this->generateHash($password, $salt);

        if (($users->insertUsers($user, $secondName, $firstName, $middleName, $email, $hash, $salt, $position, $department, $phone, $birthday, $startWork, $endWork, $isShown, $halfTime, $timesheetId))
            && ($roles->insertUserRole($users->getId($user), $role))
            && ($pos->savePositionToHistory($users->getId($user), $position))) {

            FlashMessages::addMessage("Пользователь успешно добавлен.", "info");
        } else {
            FlashMessages::addMessage("Произошла ошибка. Пользователь не был добавлен.", "error");
        }
        Utils::sendMail($email, "Создан аккаунт в системе Opensoft Savage", "Ваш пароль: $password");
    }

    /**
     * This function update user in base
     * @param integer $id
     * @param string secondName
     * @param string firstName
     * @param string middleName
     * @param integer $position
     * @param integer $role
     * @param string $email
     * @param integer $department
     * @param string $birthday
     * @param string $phone
     * @param string $newPass
     * @param integer $isShown
     * @return void
     */
    public function update($id, $secondName, $firstName, $middleName, $position, $role, $email, $department, $birthday, $startWork, $endWork ,$phone, $isShown, $halfTime, $timesheetId){
        $users = new UsersModel;
        $roles = new RolesModel();
        $pos = new Positions();
        $user = $users->getUserInfo($id);
        if (($user['position_id'] != $position)){
            $pos->savePositionToHistory($id, $position);
        }
        if(($users->editUser($id, $secondName, $firstName, $middleName, $position, $email, $department, $birthday, $startWork, $endWork, $phone, $isShown, $halfTime, $timesheetId))
            && ($roles->editUserRole($id, $role))){
            FlashMessages::addMessage("Пользователь успешно отредактирован.", "success");
        } else {
            FlashMessages::addMessage("Произошла ошибка. Пользователь не был отредактирован", "error");
        }
        Utils::redirect("/users");
    }

    /**
     * Delete user from base and redirect on index page
     * @return void
     */
    public function deleteAction(){
        if(!Acl::checkPermission('users_delete')){
            $this->render("errorAccess.tpl");
        }
        $id = $_POST['id'];
        $user =  new UsersModel();
        $delete = $user->deleteUser($id);
        if ($delete) {
            FlashMessages::addMessage("Пользователь успешно удален.", "success");
            Utils::redirect("/users");
        } else FlashMessages::addMessage("При удалении пользователя произошла ошибка.", "error");
    }

    /**
     *If you forgot password this function send message for mail and redirect
     * on index page
     * @return void
     */
    public function forgotPasswordAction(){
        if(isset($_POST['loginForForgotPassword']) && $_POST['loginForForgotPassword']){
            $login = $_POST['loginForForgotPassword'];
            $usersModel = new UsersModel();
            $salt = Utils::createRandomString(5, 5);
            $password = Utils::createRandomString(8, 10);
            $hash = $this->generateHash($password, $salt);

            if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
                $user = $usersModel->getInfoByEmail($login);
                if($user){
                    $email = $login;
                } else {
                    FlashMessages::addMessage("Не правильно введен Email", "error");
                }
            } else {
                $user = $usersModel->getInfoByCodeKey((int) $login);
                if($user){
                    $email = $user['email'];
                } else {
                    FlashMessages::addMessage("Не правильно введен номер карты", "error");
                }
            }

            if(isset($email)){
                if(Utils::sendMail($email, "Ваш новый пароль в системе Opensoft Savage", "Ваш пароль: $password")){
                    $usersModel->editUserPass($user['id'], $hash, $salt);
                    FlashMessages::addMessage("Ваш новый пароль отправлен вам на почту", "success");
                    Utils::redirect("/users/login");
                } else {
                    FlashMessages::addMessage("Произошла ошибка. Пароль отправлен не был.", "error");
                }
            }
        }
        $this->render("Users/forgotPassword.tpl");
    }

    public function profileAction(){
        $userInfo = Registry::getValue('user');
        $userId = $userInfo['id'];
       
        if (!isset($_GET['id']) || ($_GET['id'] == $userId)) {
            $profileUserId = $userId;
            $isOwner = true;
        } else {
            $profileUserId = $_GET['id'];
            $isOwner = false;
        }
        if(Acl::checkPermission('users_profile')|| $isOwner){
            $user = new UsersModel();
            $profileUserInfo = $user->getUserInfo($profileUserId);
            $userStatus = $user->getUserStatus($profileUserId);
            $profileUserInfo['status'] = $userStatus;
            if (isset($_POST['oldPass']) && $_POST['oldPass'] && isset($_POST['newPass']) && $_POST['newPass']){
                $oldPass = $_POST['oldPass'];
                $newPass = $_POST['newPass'];
                $info = $user->getUserInfo($profileUserId);
                $hash = $this->generateHash($oldPass, $info['salt']);
                if($hash == $info['password']){
                    $salt = Utils::createRandomString(5, 5);
                    $newHash = $this->generateHash($newPass, $salt);
                    $user->editUserPass($profileUserId, $newHash,$salt);
                    FlashMessages::addMessage("Пароль успешно изменен.", "success");
                } else {
                    FlashMessages::addMessage("Старый пароль введен не верно и изменен не был.", "error");
                }
            }
            $statuses = $user->getUserStatuses();
            $this->render("Users/profile.tpl", array('userInfo' => $profileUserInfo, 'isOwner' => $isOwner, 'statuses' => $statuses, 'id' => $profileUserId, 'otherOffice' => StatusesType::OTHER_OFFICE));
        } else {
            $this->render("errorAccess.tpl");
        }
    }

    /**
     * Get last entries/exists for notifications
     * @return void
     */
    public function getNotificationsAction()
    {
        $userModel = new UsersModel();
        $lastTime = $_GET['lastTime'];
        $result = $userModel->getLastLogs($lastTime);
        echo json_encode($result);
    }

}
