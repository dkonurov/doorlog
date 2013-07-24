<?php
namespace core;

class Utils{
    static $daysFullNames = array('Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Cуббота', 'Воскресенье');

    /**
     * Logs data to db
     * @param string $type f.e. 'USER_ADD'
     * @param string $data
     * @return bool
     */
    public static function log($type, $data){
        $db = Db::getInstance();
        $result = $db->query("INSERT INTO log(type, data)
            VALUES ('$type', '$data')");

        return $result;
    }

    /**
     * Shows exception
     * @param object $e
     */
    public static function showException($e) {
        echo('Message: ' . $e->getMessage());
        echo('<br>' . 'File: ' . $e->getFile());
        echo('<br>' . 'Line: ' . $e->getLine());
        if($e->getCode()){
            echo('<br>' . 'Code: ' . $e->getCode());
        }
        echo('<br>' . 'Trace: ' . '<pre>' . $e->getTraceAsString() . '</pre>');
    }

    /**
     * Generates random string (for salt, password)
     * @param int $minchar
     * @param int $maxchar
     * @return string
     */
    public static function createRandomString($minchar = 5, $maxchar=10){
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890abcdefghijklmnopqrstuvwxyz";
        $length = mt_rand($minchar,$maxchar);
        $randomString = '';
        $charsCount = strlen($chars) - 1;
        for($i = 0; $i < $length; $i++){
            $randomString .= $chars[mt_rand(1, $charsCount)];
        }

        return $randomString;
    }

    /**
     * Sends letter to specified email
     * @param string $email
     * @param string $subject
     * @param string $message
     * @return bool
     */
    public static function sendMail($email, $subject, $message) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $headers  = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
            $headers .= 'From: Opensoft Savage' . "\r\n";
            return mail($email, $subject, $message, $headers);
        }

        return false;
    }

    /**
     * Transforms unixtime to hours and minutes
     * @param int $unixtime
     * @return string
     */
    public static function formatDate($unixtime) {
        $h = floor($unixtime / 3600);
        $m = floor($unixtime % 3600 / 60);

        $h = str_pad($h, 2, "0", STR_PAD_LEFT);
        $m = str_pad($m, 2, "0", STR_PAD_LEFT);

        $str = $h . ' ч ' . $m . ' м';

        return $str;
    }

    /**
     * Returns all days of the week which includes specified date
     * @param $date
     * @return array
     */
    public static function getWeekDays($date) {
        $ts = strtotime($date);
        $dayOfWeek = date('w', $ts);
        $offset = $dayOfWeek - 1;
        if ($offset < 0) {
            $offset = 6;
        }
        $ts -= $offset * 24 * 60 * 60;

        $weekDays = array();
        foreach (self::$daysFullNames as $name) {
            $date = date("Y-m-d", $ts);
            $weekDays[] = array(
                'date' => $date,
                'name' => $name,
            );
            $ts += 24 * 60 * 60;
        }

        return $weekDays;
    }

    /**
     * Returns first day of the month which includes specified day
     * @param int $ut day unixtime
     * @return int unixtime
     */
    public static function getWeekFirstDay($ut) {
        $dayNum = date('w', $ut);

        if ($dayNum == 0) {
            $firstDay = strtotime('Monday last week', $ut);
        } else {
            $firstDay = strtotime('Monday this week', $ut);
        }

        return $firstDay;
    }

    public static function redirect($url = '/') {
        $cfg = Registry::getValue('config');
        header('Location: ' . $cfg['root'] . $url);
        die();
    }

    /**
     * Create *.xls file
     * @param array $report
     * @return void
     */
    public function tabletoxls($report){
        if ($report){
            $pExcel = new \PHPExcel();
            $pExcel->setActiveSheetIndex(0);
            $aSheet = $pExcel->getActiveSheet();
            //style
            $aSheet->getColumnDimension('A')->setWidth(15);
            $aSheet->getColumnDimension('B')->setWidth(10);
            $aSheet->getColumnDimension('D')->setWidth(10);

            $iter = 1;
            $countUser = count($report);
            for ($usr=0; $usr < $countUser; $usr++) {
                $aSheet->mergeCells("A$iter:D$iter");
                $aSheet->setCellValueByColumnAndRow(0,$iter, $report[$usr]['name']);
                $iter++;
                $aSheet->setCellValueByColumnAndRow(0,$iter,'День недели');
                $aSheet->setCellValueByColumnAndRow(1,$iter,'Дата');
                $aSheet->setCellValueByColumnAndRow(2,$iter,'Время');
                $aSheet->setCellValueByColumnAndRow(3,$iter,'Тип отгула');
                $iter++;

                foreach ($report[$usr]['reports'] as $curr) {
                    $aSheet->setCellValueByColumnAndRow(0, $iter, $curr['dayName']);
                    $aSheet->setCellValueByColumnAndRow(1, $iter, $curr['date']);
                    $aSheet->setCellValueByColumnAndRow(2, $iter, $curr['time']);
                    $aSheet->setCellValueByColumnAndRow(3, $iter, $curr['timeoffName']);
                    $iter++;
                }
            }
            $objWriter = new \PHPExcel_Writer_Excel5($pExcel);
            if ($countUser>1){
                $name = $report[0]['depName'].'.xls';
            } else {
                $name = $report[0]['name'].'.xls';
            }
            header('Content-Disposition: attachment; filename='.$name);
            $objWriter->save('php://output');
        }
    }

    public static function timesheetsave($report){
        //Загружаем шаблон
        $objReader = \PHPExcel_IOFactory::createReader('Excel5');
        $objPHPExcel = $objReader->load('template.xls');

        $date = array_keys($report[0]['report']);
        $dateStart = "";
        $dateEnd = "";
        $isFirstWorkDay = true;

        $mergeABCcol = 3;
        $startRow = 26;

        $holidaystartRow = 'D';
        $holidaystartRow2 = 'D';
        $holidaystartCow = 21;
        $holidaystartCow2 = 23;
        $r = 0; 
        foreach ($report[0]['report'] as $key => $value) {
            $objPHPExcel->setActiveSheetIndex(0);
                if ($r < 15){
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("$holidaystartRow$holidaystartCow", $key[8].$key[9]);
                    if ( $value['dayType'] == 1 ){
                        $objPHPExcel
                            ->getActiveSheet()
                            ->getStyle("$holidaystartRow$holidaystartCow:$holidaystartRow".($holidaystartCow+1))
                            ->getFill()
                            ->getStartColor()
                            ->setRGB('FF0000');
                    }
                } else {
                    if ($r >= 15){
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("$holidaystartRow2$holidaystartCow2", $key[8].$key[9]);
                        if ($value['dayType'] == 1 ){
                            $objPHPExcel
                                ->getActiveSheet()
                                ->getStyle("$holidaystartRow2$holidaystartCow2:$holidaystartRow2".($holidaystartCow2 + 1))
                                ->getFill()
                                ->getStartColor()
                                ->setRGB('FF0000');
                        }
                        $holidaystartRow2++;
                        
                    }
                    

                }
                $holidaystartRow++;
                $r++;
            }

        for ($i=0, $arrSize = count($report); $i < $arrSize; $i++) {

            //Столбец номера по порядку
            $objPHPExcel->getActiveSheet(0)->mergeCells('A'.$startRow.':A'.($startRow + $mergeABCcol));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A$startRow", $i+1);

            //столбец имени и уровня
            $objPHPExcel->getActiveSheet(0)->mergeCells('B'.$startRow.':B'.($startRow + $mergeABCcol));
            $name = $report[$i]['name'].', '.$report[$i]['position'];
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("B$startRow", $name);

            //cтолбец табельный номер
            $objPHPExcel->getActiveSheet(0)->mergeCells('C'.$startRow.':C'.($startRow + $mergeABCcol));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("C$startRow", $i+1);

            $objPHPExcel->getActiveSheet(0)->mergeCells('U'.$startRow.':U'.($startRow + 1));
            $objPHPExcel->getActiveSheet(0)->mergeCells('V'.$startRow.':V'.($startRow + 1));
            $objPHPExcel->getActiveSheet(0)->mergeCells('W'.$startRow.':W'.($startRow + 1));

            $objPHPExcel->getActiveSheet(0)->mergeCells('U'.($startRow + 2).':U'.($startRow + 3));
            $objPHPExcel->getActiveSheet(0)->mergeCells('V'.($startRow + 2).':V'.($startRow + 3));
            $objPHPExcel->getActiveSheet(0)->mergeCells('W'.($startRow + 2).':W'.($startRow + 3));

            $startCol = 'D';
            $startCol2 = 'D';
            $halfMonthTime1 = '';
            $halfMonthTime2 = '';
            $workDayCount1 = '';
            $workDayCount2 = '';
            //время
            foreach ($report[$i]['report'] as $keyDate => $valDate) {

                if ( (int)$keyDate[8].$keyDate[9] <= 15){

                    //время за полмесяца
                    if ( $valDate['time'] != 0) $halfMonthTime1 += $valDate['time'];

                    // кол-во рабочих дней месяца
                    if ( $valDate['status_name'] == 'Я' || $valDate['status_name'] == 'К' ){
                        $workDayCount1++;
                    }

                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("$startCol$startRow", $valDate['status_name']);
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("$startCol".($startRow + 1), $valDate['time']);

                    //Время за полмесяца
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("T$startRow", $workDayCount1);
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("T".($startRow + 1), $halfMonthTime1);

                    //Закрашиваем выходные
                    if ($valDate['dayType'] == 1){
                        $objPHPExcel
                        ->getActiveSheet()
                        ->getStyle("$startCol$startRow:$startCol".($startRow + 1))
                        ->getFill()
                        ->getStartColor()
                        ->setRGB('FF0000');
                    }

                    if ( $valDate['status_name'] == 'Я' || $valDate['status_name'] == 'К' ){
                        $workDayCount2++;
                    }
                    if ( $valDate['dayType'] != 1 ) {
                        $dateEnd = $keyDate;
                        if ($isFirstWorkDay){
                            $dateStart = $keyDate;
                            $isFirstWorkDay = false;
                        }
                    }

                    $startCol++;

                } else {

                    //время за полмесяца
                    if ( $valDate['time'] != 0) $halfMonthTime2 += $valDate['time'];

                    // кол-во рабочих дней месяца
                    if ( $valDate['status_name'] == 'Я' || $valDate['status_name'] == 'К' ){
                        $workDayCount2++;
                    }
                    if ( $valDate['dayType'] != 1 ) {
                        $dateEnd = $keyDate;
                        if ($isFirstWorkDay){
                            $dateStart = $keyDate;
                            $isFirstWorkDay = false;
                        }
                    }

                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("$startCol2".($startRow + 2), $valDate['status_name']);
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("$startCol2".($startRow + 3), $valDate['time']);

                    //Время за полмесяца
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("T".($startRow + 2), $workDayCount2);
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("T".($startRow + 3), $halfMonthTime2);

                    //Закрашиваем выходные
                    if ($valDate['dayType'] == 1){
                        $objPHPExcel
                        ->getActiveSheet()
                        ->getStyle("$startCol2".($startRow + 2).":$startCol2".($startRow + 3))
                        ->getFill()
                        ->getStartColor()
                        ->setRGB('FF0000');
                    }
                    $startCol2++;
                }
            }

            $startRow += 4;
            $dateEnd = date('d.m.Y', strtotime($dateEnd));
            $dateStart = date('d.m.Y', strtotime($dateStart));
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("V15", $dateEnd);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("P15", $dateEnd);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("T15", $dateStart);
        }

        //Столбец номера по порядку
        $objPHPExcel->getActiveSheet(0)->mergeCells('A'.$startRow.':A'.($startRow + $mergeABCcol));

        //столбец имени и уровня
        $objPHPExcel->getActiveSheet(0)->mergeCells('B'.$startRow.':B'.($startRow + $mergeABCcol));

        //cтолбец табельный номер
        $objPHPExcel->getActiveSheet(0)->mergeCells('C'.$startRow.':C'.($startRow + $mergeABCcol));

        $objPHPExcel->getActiveSheet(0)->mergeCells('U'.$startRow.':U'.($startRow + 1));
        $objPHPExcel->getActiveSheet(0)->mergeCells('V'.$startRow.':V'.($startRow + 1));
        $objPHPExcel->getActiveSheet(0)->mergeCells('W'.$startRow.':W'.($startRow + 1));

        $objPHPExcel->getActiveSheet(0)->mergeCells('U'.($startRow + 2).':U'.($startRow + 3));
        $objPHPExcel->getActiveSheet(0)->mergeCells('V'.($startRow + 2).':V'.($startRow + 3));
        $objPHPExcel->getActiveSheet(0)->mergeCells('W'.($startRow + 2).':W'.($startRow + 3));

        

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A$startRow", 'Работник кадровой службы');
        $objPHPExcel->getActiveSheet()->getStyle("A".($startRow))->getFont()->setSize(8);

        $startRow += 4;
        $styleArray = array(
          'borders' => array(
              'allborders' => array(
                  'style' => \PHPExcel_Style_Border::BORDER_NONE
              )
          )
        );

        $objPHPExcel->getActiveSheet()->getStyle("A$startRow:AA".($startRow + 6))->applyFromArray($styleArray);

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("C".($startRow), 'должность');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("E".($startRow), 'личная подпись');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("J".($startRow), 'расшифровка подписи');

        $objPHPExcel->getActiveSheet()->getStyle("C".($startRow + 2).':M'.($startRow + 2))->getBorders()->getBottom()
        ->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("E".($startRow + 2), '/');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("G".($startRow + 2), '/');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("N".($startRow + 2), '«')
        ->getStyle("N".($startRow + 2))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("P".($startRow + 2), '»');

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("T".($startRow + 2), substr($dateStart,6 ,10).'г.');
        $objPHPExcel->getActiveSheet()->getStyle("T".($startRow + 2))->getFont()->setSize(8);

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("N15", substr($dateStart,4 ,2));

        $objPHPExcel->getActiveSheet()->getStyle('O'.($startRow + 2))->getBorders()->getBottom()
        ->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle("Q".($startRow + 2).':R'.($startRow + 2))->getBorders()->getBottom()
        ->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("C".($startRow + 3), 'должность');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("E".($startRow + 3), 'личная подпись');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("J".($startRow + 3), 'расшифровка подписи');

        $objPHPExcel->getActiveSheet()->getStyle("A".($startRow).":AA".($startRow + 6))->getFont()->setSize(7);
        $objPHPExcel->getActiveSheet()->getStyle("A".($startRow + 4).":AA".($startRow + 1000))->applyFromArray($styleArray);

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

        header('Content-Disposition: attachment; filename='.substr($dateStart,3 ,10).'.xlsx');
        $objWriter->save('php://output');
    }
}