<?php
namespace controllers;
use core\Acl;
use core\Controller;
use models\Users as UsersModel;
use models\Departments as DepartmentModel;
use core\FlashMessages;
use models\Reports as ReportsModel;
use controllers\Main as Time;
use models\Holidays;
use core\Utils;
use models\Status;
use models\StatusesType;
use models\Positions;

class Reports extends Controller {

    const FULLDAY = 8;
    const SHORTDAY = 7;
    const FULLDAYHALFWORK = 4;
    const SHORTDAYHALFWORK = 3;
    const NULLDAY = "";

    /**
    * Render page of reports by user or all users in current department
    * @return void
    */
    public function timeoffsAction() {
        if(!Acl::checkPermission('timeoffs_reports')){
            $this->render("errorAccess.tpl");
        }
        $timeoffs = array();
        $users = array();
        $reportAllDaysArray = array();
        $name = array();
        $totalDepInfo = array();

        $timeoffsAllUsers = array();
        $user = new UsersModel();
        $dep = new DepartmentModel();
        $date = date('m-Y');
        $id = '';
        if (isset($_GET['date']) && !empty($_GET['date'])){
            $date = $queryDate = $_GET['date'];
            $date = strtotime(strrev(strrev($date).'.10'));
            $date = date('Y-m', $date);
            if (isset($_GET['user_id']) && $_GET['user_id'] != 0 ){
                $reportAllDaysArray = $this->getMonthReport($_GET['user_id'], $date);
                $userInfo = $user->getUserInfo($_GET['user_id']);
                $name['user'] = $userInfo['name'];
                $id = $_GET['user_id'];
            }

            if (isset($_GET['dep_id']) && $_GET['dep_id'] != 0 ){
                $totalDepInfo['statuses'] = $user->getUserStatuses();
                $depInfo = $dep->getDepById($_GET['dep_id']);
                $name['dep'] = $depInfo['name'];
                $users = $dep->getUsers($_GET['dep_id']);
                foreach ($users as $currentUser) {
                    $totalUserStats[] = array(
                        'id' => $currentUser['id'],
                        'name' => $currentUser['name'],
                        'stats' => $this->totalSumReports($this->getMonthReport($currentUser['id'], $date))
                    );
                    $totalDepInfo['totalUserStats'] = $totalUserStats;
                    $totalDepInfo['date'] = $queryDate;
                }
            }
        }
        $allUsers = $user->getRegistered();
        $allDep = $dep->getMenuDepartments();
        $statuses = $user->getUserStatuses();
        $timeoffsAttr = array('date' => $date, 'name' => $name, 'id' => $id);
        $this->render("Reports/timeoffs_list.tpl" , array('statuses' => $statuses,
            'timeoffsAttr' => $timeoffsAttr,
            'allUsers' => $allUsers,
            'allDep'=>$allDep,
            'users'=>$users,
            'reportAllDaysArray' => $reportAllDaysArray,
            'name' => $name,
            'totalDepInfo' => $totalDepInfo));
    }

    /**
    * Render page of graph exits and entrances
    * @return void
    */
    public  function officeloadAction() {
        if(!Acl::checkPermission('officeload_reports')){
            $this->render("errorAccess.tpl");
        }
        $reportModel = new ReportsModel;

        if (isset($_GET['date'])) {
            $date = $_GET['date'];
        } else {
            $date = date('Y-m-d');
        }

        $desiredDate = $reportModel->getTimesList($date);
        $outDesireDate = $reportModel->getOutTimesList($date);

        $sortedTimes = array();
        $outSortedTimes = array();
        $stringForGraph = "";
        $outStringForGraph = "";
        foreach ($desiredDate as $hour) {
            $sortedTimes[$hour['hour']] = $hour['count'];
        }
        foreach ($outDesireDate as $hour) {
            $outSortedTimes[$hour['hour']] = $hour['count'];
        }
        for ($i = 0; $i <= 23; $i++) {
            if (isset($sortedTimes[$i])) {
                $entersCount = $sortedTimes[$i];
            } else {
                $entersCount = 0;
            }
            if(isset($outSortedTimes[$i])) {
                $outsCount = $outSortedTimes[$i];
            } else {
                $outsCount = 0;
            }
                
            $stringForGraph .= "[".$i.",".$entersCount."]".",";
            $outStringForGraph .= "[".$i.",".$outsCount."]".",";
        }
        $stringForGraph = "[".substr($stringForGraph, 0, -1)."]";
        $outStringForGraph = "[".substr($outStringForGraph, 0, -1)."]";
        $this->render("Reports/officeload.tpl", array('date' => $date,
                                                      'stringForGraph' => $stringForGraph, 'outStringForGraph'=> $outStringForGraph));
    }

    /**
     * Render page for download
     * @return void
     */
    public function downloadAction(){
        $user = new UsersModel();
        $dep = new DepartmentModel();
        $reports = array();
        if(isset($_GET['date'])){
            $date=$_GET['date'];
            if(isset($_GET['user_id'])){
                $userId=$_GET['user_id'];
                $infoUser=$user->getUserInfo($userId);
                $reports[]= array(
                    'reports' => $this->getMonthReport($userId, $date),
                    'name' => $infoUser['name']
                );
            }
            else if(isset($_GET['dep_id'])){
                $depId = $_GET['dep_id'];
                $users = $dep->getUsers($depId);
                $depName = $dep->getDepById($depId);
                foreach($users as $currentUser)
                $reports[] = array(
                    'reports' => $this->getMonthReport($currentUser['id'], $date),
                    'id' => $currentUser['id'],
                    'name' => $currentUser['name'],
                    'depName'=>$depName['name']
                );
            }
            $utils = new Utils();
            $utils->tabletoxls($reports);
        }
    }

    /**
    * Gets total time and count timeoffs type
    * @param array $report
    * @return array
    */
    public function totalSumReports($report){
        $user = new UsersModel();
        $total = array();
        $total['time'] = 0;
        $statuses = $user->getUserStatuses();
        foreach ($statuses as $status) {
            $total[$status['id']] = 0;
        }

        foreach ($report as $currentDay) {
            $total['time'] += $currentDay['time'];
            if ( isset($total[$currentDay['timeoffType']]) ){
                $total[$currentDay['timeoffType']] ++;
            }   
        }
        $hour = floor($total['time']/3600);
        $total['time'] = $total['time'] - $hour*3600;
        $min = floor($total['time']/60);
        $total['time'] = $hour.'ч '.$min.'м';
        return $total;
    }

    /**
    * Generates a report by user_id
    * @param integer $id
    * @param string $selectedDate
    * @param integer $timeoffType
    * @return array
    */
    public function getMonthReport($id, $selectedDate, $timeoffType = 0){
        $user = new UsersModel();
        $dep = new DepartmentModel();
        $monthTime = new Time();
        $holidays = new Holidays();

        $timeoffsArray = array();
        $userMonthTimeArray = array();
        $reportAllDaysArray = array();
        $vacation = array();
        $currVacation = array();

        $firstMonthDay = strtotime($selectedDate);
        $lastMonthDay = strtotime($selectedDate) + date("t", strtotime($selectedDate))*24*60*60 ;
        $vacation = $holidays->getAllDays($selectedDate);

        $timeoffs = $user->getTimeoffsByUserId($id, $selectedDate, $timeoffType);
        foreach ($timeoffs as $timeOff) {
            $timeoffsArray[$timeOff['date']]['name'] = $timeOff['name'];
            $timeoffsArray[$timeOff['date']]['type'] = $timeOff['id'];
            $timeoffsArray[$timeOff['date']]['time'] = $timeOff['addtime']*3600;
        }

        foreach ($vacation as $curr) {
            $currVacation[date('Y-m-d', strtotime($curr['date']))] = $curr;
        }

        $personalId = $user->getPersonalId($id);
        if ($personalId){
            $userMonthTime = $monthTime->getMonthInfo($personalId, $selectedDate);
            if (isset($userMonthTime['days'])){
                $userMonthTime = $userMonthTime['days'];
                $workDays = array_keys($userMonthTime);
                foreach ($workDays as $workDay) {
                    $userMonthTimeArray[$workDay]['time'] = $userMonthTime[$workDay]['sum'];
                }
            }
            for ($date = $firstMonthDay; $date < $lastMonthDay; $date += 86400) {
                $currentDate = date('Y-m-d', $date);
                $oneDay = array('date'=> $currentDate,
                    'dayName' => Utils::$daysFullNames[date("N", $date)-1],
                    'timeoffName' => '',
                    'time' => 0,
                    'timeoffType'=>0,
                    'dayType' => (int)$currVacation[$currentDate]['type']);
                if(isset($timeoffsArray[$currentDate])){
                    $oneDay['timeoffName'] = $timeoffsArray[$currentDate]['name'];
                    $oneDay['dayType'] = (int)$currVacation[$currentDate]['type'];
                    $oneDay['timeoffType'] = $timeoffsArray[$currentDate]['type'];
                    $oneDay['time'] = $timeoffsArray[$currentDate]['time'];
                } else if (isset($userMonthTimeArray[$currentDate])) {
                    $oneDay['timeoffName'] = '--';
                    $oneDay['time'] = $userMonthTimeArray[$currentDate]['time'];
                }
                    $reportAllDaysArray[$currentDate] = $oneDay;
            } 
        }
    return $reportAllDaysArray;
    }

    public function timesheetAction(){
        if(!Acl::checkPermission('watch_timesheet')){
            $this->render("errorAccess.tpl");
        }
        $timesheet = array();
        $date = date('Y-m');
        if (isset($_GET['date']) && $_GET['date']){
            $date = '01.'.$_GET['date'];
            $date = date('Y-m', strtotime($date));
        }
        $dayCount = date("t", strtotime($date));
        $timesheet = $this->getTimesheet($date);
        $holidays = new Holidays();
        $allHolidays = $holidays->getAllDays($date);
        $days = array();
        foreach ($allHolidays as $oneDay) {
            $days[] = $oneDay['type'];
        }
        $date = date('m.Y', strtotime($date));
        $this->render("Reports/timesheet.tpl" , array('timesheet' => $timesheet,'days'=> $days,'date'=> $date, 'dayCount' => $dayCount));
    }

    public function timesheetsaveAction(){
        if(!Acl::checkPermission('watch_timesheet')){
            $this->render("errorAccess.tpl");
        }
        if (isset($_GET['date'])){
            $date = date('Y-m', strtotime('01.'.$_GET['date']));
        } else {
            $date = date('Y-m');
        }
        $report = $this->getTimesheet($date);
        $util = new Utils();
        $util->timesheetsave($report);
        $date = date('m.Y', strtotime($date.'-01'));
        Utils::redirect("/reports/timesheet?date=$date");
    }
    private function getTimesheet($date){
        $user = new UsersModel();
        $dep = new DepartmentModel();
        $pos = new Positions();
        $timesheet = array();

        $allPositions = $user->getPositionsList();
        $posNames = array();

        for ($p = 0, $arrSize = count($allPositions); $p < $arrSize; $p++){
            $posNames[$allPositions[$p]['id']] = $allPositions[$p]['name'];
        }

        $allUsers = $user->getAllUsersForTimesheet();
        for ($i = 0, $arrSize = count($allUsers); $i < $arrSize; $i++){

            $isShowInThisMonth = false;

            if ( $allUsers[$i]['startwork'] == '0000-00-00'){
                $isShowInThisMonth = false;
            } elseif( $allUsers[$i]['endwork'] == '0000-00-00' 
                && strtotime(substr($allUsers[$i]['startwork'], 0, 7).'-01') <= strtotime($date.'-01') ){
                $isShowInThisMonth = true;
            } elseif( strtotime(substr($allUsers[$i]['startwork'], 0, 7).'-01') <= strtotime($date.'-01') && 
                strtotime($date.'-01') <= strtotime(substr($allUsers[$i]['endwork'], 0, 7).'-01') ) {
                $isShowInThisMonth = true;
            } else {
                $isShowInThisMonth = false;
            }

            if ( $allUsers[$i]['endwork'] == '0000-00-00' && $allUsers[$i]['startwork'] == '0000-00-00' ){
                $isShowInThisMonth = false;
            }

            if ( $isShowInThisMonth ){

                $timesheet[$i]['user_id'] = $allUsers[$i]['id'];

                $firstName = $allUsers[$i]['first_name'];
                $secondName = $allUsers[$i]['second_name'];
                $middleName = $allUsers[$i]['middle_name'];
                $fullName = $secondName .' '.substr($firstName, 0, 2).'.'.substr($middleName, 0,2).'.';

                $timesheet[$i]['name'] = $fullName;
                $timesheet[$i]['report'] = $this->getOfficalTimeForTimesheet($allUsers[$i]['id'], $date);

                if ($pos->getLatestActualPositionForCurrMonth($allUsers[$i]['id'], $date) != 0){
                    $actualPos = $pos->getLatestActualPositionForCurrMonth($allUsers[$i]['id'], $date);
                } else {
                    $actualPos = $allUsers[$i]['position_id'];
                }
                $timesheet[$i]['position'] = $posNames[$actualPos];
            }
        }
        return $timesheet;
    }

    private function getOfficalTimeForTimesheet($id, $date){
        $report = array();
        $statusesTime = array();
        $holiday = new Holidays();
        $user = new UsersModel();
        $statuses = new Status();

        $monthDay = $holiday->getMonthDays($date);//Y-m
        $monthHoliday = $holiday->getAllDays($date);//Y-m
        $totalUserInfo = $user->getUserInfo($id);
        $holidayTimeMinus = $holiday->getAllType();
        $monthTimeOffs = $user->getTimeoffsByUserId($id, $date);//Y-m
        $allStatuses = $statuses->getAllTypeFullInfo();

        $expectedTime = $this->getMonthReport($id, $date);//Y-m был в офисе

        //Время для ув причин
        for ($i=0, $arrSize = count($allStatuses); $i < $arrSize; $i++) {
            $statusesTime[$allStatuses[$i]['type_id']] = $allStatuses[$i]['addtime'];
        }

        $isHalfWork = $totalUserInfo['halftime'];
        $startWork = $totalUserInfo['startwork'];
        if ($startWork != '0000-00-00'){
            $startWork = date('Y-m-d', $startWork);
        }
        $endWork = $totalUserInfo['endwork'];
        if ($endWork != '0000-00-00'){
            $endWork = date('Y-m-d', $endWork);
        }

        //Массив с ключами-датами хранит время и тип дня (если чел заходил в офис ставим 8 или 4)
        for ($i=0, $arrSize = count($monthDay); $i < $arrSize; $i++) {
            $date = date('Y-m-d', strtotime($monthDay[$i]['date']));
            $report[$date]['dayType'] = 0;
            $report[$date]['status_id'] = 0;
            if ( $expectedTime[$date]['time'] != 0 and !$isHalfWork ) {
                $report[$date]['time'] = self::FULLDAY;
                $report[$date]['status_name'] = 'Я';
            } elseif ( $expectedTime[$date]['time'] != 0 and $isHalfWork ) {
                $report[$date]['time'] = self::FULLDAYHALFWORK;
                $report[$date]['status_name'] = 'Я';
            } else {
                $report[$date]['time'] = self::NULLDAY;
                $report[$date]['status_name'] = 'Н';
            }
        }

        $statusesType = new StatusesType();

        //Проставляем отгулы и командировки (отсутствие по ув причине), тип отгула
        for ($i=0, $arrSize = count($monthTimeOffs); $i < $arrSize; $i++) {
            $correctDate = $monthTimeOffs[$i]['date'];
            $report[$correctDate]['time'] = "";
            if ($statusesTime[$monthTimeOffs[$i]['status_id']] != 0){
                if (!$isHalfWork){
                    $report[$correctDate]['time'] = $statusesTime[$monthTimeOffs[$i]['status_id']];
                } else {
                    $report[$correctDate]['time'] = $statusesTime[$monthTimeOffs[$i]['status_id']]/2;
                }
                
            }
            switch ($monthTimeOffs[$i]['status_id']) {
                case StatusesType::SICK:
                    $report[$correctDate]['status_name'] = 'Б';
                    break;
                case StatusesType::VACATION:
                    $report[$correctDate]['status_name'] = 'От';
                    break;
                case StatusesType::TRIP:
                    $report[$correctDate]['status_name'] = 'К';
                    break;
                default:
                    break;
            }
            $report[$correctDate]['status_id'] = $monthTimeOffs[$i]['status_id'];
        }
        
        //Проставляем выходные и короткие дни
        for ($i=0, $arrSize = count($monthHoliday); $i < $arrSize; $i++) {
            $correctDate = date('Y-m-d',strtotime($monthHoliday[$i]['date']));
            switch ($monthHoliday[$i]['type']) {
                case 1:
                    $report[$correctDate]['time'] = self::NULLDAY;
                    $report[$correctDate]['dayType'] = 1;
                    $report[$correctDate]['status_name'] = "В";
                    break;
                case 2:
                    if (!$isHalfWork and $report[$correctDate]['time'] != 0){
                        $report[$correctDate]['time'] = self::SHORTDAY;
                    } elseif ($isHalfWork and $report[$correctDate]['time'] != 0) {
                        $report[$correctDate]['time'] = self::SHORTDAYHALFWORK;
                    }
                    break;
                default:
                    break;
            }
        }

        //Удаление инфы из массива если даты устройства или увольнения в месяце отчета
        foreach ($report as $currDate => $valDate) {
            if ( strtotime($currDate) < strtotime($startWork) && $startWork != '0000-00-00' ){
                $report[$currDate]['status_name'] = '';
                $report[$currDate]['time'] = '';
                $report[$currDate]['status_id'] = '';
            }
        }

        foreach ($report as $currDate => $valDate) {
            if ( strtotime($currDate) > strtotime($endWork) && $endWork != '0000-00-00' ){
                $report[$currDate]['status_name'] = '';
                $report[$currDate]['time'] = '';
                $report[$currDate]['status_id'] = '';
            }
        }
        return $report;
    }
}
