<?php

namespace App\Controllers;

use \Core\View;
use App\Controllers\Authenticated;
use App\Models\Earning;
use App\Flash;
use App\Controllers\TimeAndDate;


class Income extends Authenticated{
    
    public function newAction(){
    
        if(Earning::checkIfUserHasDefaultCategories()){
            View::renderTemplate('Income/new.html', [
                'categories' => Earning::getIncomeCategoryNameAssignedToUser(),
                'presentDate' => TimeAndDate::getPresentDate()
            ]);
        } else {
            View::renderTemplate('Income/new.html', [
                'categories' => Earning::getDefaultIncomeCategories(),
                'presentDate' => TimeAndDate::getPresentDate()
            ]);
        }
    }

    public function createAction(){

        $income = new Earning($_POST);
     
        if($income->saveToIncomes()){
            Flash::addMessages('Superb!', 'success');
            $this->redirect('/Income/success');
        } else {
            Flash::addMessages($income->errorMessage, 'warning');
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