<?php

namespace App\Controllers;

use \Core\View;
use App\Models\Earning;
use App\Models\Expenditure;

class BalanceSheet extends Authenticated{
    

    public function newAction(){
        View::renderTemplate('BalanceSheet/new.html');
        echo '<pre>';
        var_dump($_POST);
    }


}

?>