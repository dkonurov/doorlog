<?php

namespace controllers;

use core\Controller;
use core\Utils;
/**
 * Description of NewsContoller
 *
 * @author adrenaline
 */
class News extends Controller {
    function oldAction(){
        
         Utils::log('time', '13.00');
        $this->render("Mail/index.tpl" , array('value' => 2) );
       

    }
}

?>
