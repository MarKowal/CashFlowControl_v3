<?php

namespace App\Controllers;

use \Core\View;
use App\Controllers\Authenticated;
use App\Models\Earning;
use App\Flash;
use App\Controllers\TimeAndDate;


class Income extends Authenticated{
    
    public function newAction(){
      
        View::renderTemplate('Income/new.html', [
            'categories' => Earning::getDefaultIncomeCategories(),
            'presentDate' => TimeAndDate::getPresentDate()
        ]);
    }

    public function createAction(){

        $income = new Earning($_POST);
     
        if($income->saveToIncomes() === true){
            Flash::addMessages('Superb!', 'success');
            $this->redirect('/Income/success');
        } else {
            $errorMessage = implode(" ", $income->saveToIncomes());
            Flash::addMessages($errorMessage, 'warning');
            View::renderTemplate('Income/new.html', [
                'categories' => Earning::getDefaultIncomeCategories(),
                'presentDate' => TimeAndDate::getPresentDate()
            ]);
        }
    }

    public function successAction(){
        View::renderTemplate('Income/success.html');
    }

}

?>