<?php
    
    use core\Utils;
    use core\Registry;
    
    ini_set('display_errors', true);
    ini_set('error_reporting',  E_ALL);
    setlocale(LC_ALL, "ru_RU.UTF-8");
    header('Content-Type: text/html; charset=utf-8');

    require 'protected/core/Autoloader.php';
    require 'protected/vendor/autoload.php';
    
    $auto = new Autoloader();
    $auto->register();
    
    $cfg = require 'protected/config/config.php';
    Registry::setValue('config', $cfg);

    $example = new fixtures\StatusesFixtures();
    $example->update();

    