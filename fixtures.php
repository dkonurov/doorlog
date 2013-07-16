<?php
    
    use core\Utils;
    use core\Registry;
    
    ini_set('display_errors', true);
    ini_set('error_reporting',  E_ALL);

    require 'protected/core/Autoloader.php';
    require 'protected/vendor/autoload.php';
    
    $auto = new Autoloader();
    $auto->register();
    
    $cfg = require 'protected/config/config.php';
    Registry::setValue('config', $cfg);

    $objects = $auto->loadClassForDir('protected/fixtures/');
    foreach ($objects as $object) {
        $object->update();
    }
    

    