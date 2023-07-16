<?php

namespace App\Controllers;

use \Core\View;
use App\Controllers\Authenticated;
use App\Models\Earning;
use App\Flash;
use App\Controllers\TimeAndDate;


class Income extends Authenticated{
    
    public $incomeCategories = [];

    public function newAction(){
      
        $this->incomeCategories = Earning::getDefaultIncomeCategories();
        View::renderTemplate('Income/new.html', [
            'categories' => $this->incomeCategories,
            'presentDate' => TimeAndDate::getPresentDate()
        ]);
    }

    public function createAction(){

        $income = new Earning($_POST);
     
        $this->incomeCategories = Earning::getDefaultIncomeCategories();


        if(! Earning::checkIfUserHasDefaultCategories()){
            $income->saveToAssignedCategories($this->incomeCategories);
        } 

        if($income->saveToIncomes() === true){
            Flash::addMessages('Superb!', 'success');
            $this->redirect('/Income/success');
        } else {
            $errorMessage = implode(" ", $income->saveToIncomes());
            Flash::addMessages($errorMessage, 'warning');
            View::renderTemplate('Income/new.html', [
                'categories' => $this->incomeCategories,
                'presentDate' => TimeAndDate::getPresentDate()
            ]);
        }
    }

    public function successAction(){
        View::renderTemplate('Income/success.html');
    }

}

?>