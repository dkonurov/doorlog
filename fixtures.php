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

    echo "start".PHP_EOL;

    $objects = $auto->loadClassForDir('fixtures');

    if (!empty($objects)) {
        $dif = 100 / count($objects);
        $process = 0;
        echo $process . "%" . PHP_EOL;
        foreach ($objects as $object) {
            $object->update();
            $process += $dif;
            echo $process . "%" . PHP_EOL;
        }
        echo "done" . PHP_EOL;
    } else {
        echo "error" . PHP_EOL;
    }