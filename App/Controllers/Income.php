<?php

namespace App\Controllers;

use \Core\View;
use App\Controllers\Authenticated;

class Income extends Authenticated{

    public function newAction(){
        View::renderTemplate('Income/new.html');
    }

}

?>