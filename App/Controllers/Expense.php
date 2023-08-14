<?php

namespace App\Controllers;

use \Core\View;
use App\Controllers\Authenticated;
use App\Models\Expenditure;
use App\Flash;
use App\Controllers\TimeAndDate;


class Expense extends Authenticated{

    public function newAction(){

        if(Expenditure::checkIfUserHasDefaultExpenseCategories()){
            View::renderTemplate('Expense/new.html', [
                'categories' => Expenditure::getExpenseCategoryNameAssignedToUser(),
                'presentDate' => TimeAndDate::getPresentDate(),
                'payments' => Expenditure::getPaymentCategoryNameAssignedToUser()
            ]);
        } else {
            View::renderTemplate('Expense/new.html', [
                'categories' => Expenditure::getDefaultExpenseCategories(),
                'presentDate' => TimeAndDate::getPresentDate(),
                'payments' => Expenditure::getDefaultPaymentCategories()
            ]);
        }
    }

    public function createAction(){

        $expense = new Expenditure($_POST);
     
        if($expense->saveToExpenses()){
            Flash::addMessages('Superb!', 'success');
            $this->redirect('/Expense/success');
        } else {
            Flash::addMessages($expense->errorMessage, 'warning');
            View::renderTemplate('Expense/new.html', [
                'categories' => Expenditure::getDefaultExpenseCategories(),
                'presentDate' => TimeAndDate::getPresentDate(),
                'payments' => Expenditure::getDefaultPaymentCategories()
            ]);
        }
    }
        
    public function successAction(){
        View::renderTemplate('Expense/success.html');
    }
}

?>