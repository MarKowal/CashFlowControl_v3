<?php

namespace App\Controllers;

use \Core\View;
use App\Controllers\Authenticated;
use App\Models\Expenditure;
use App\Flash;

class Expense extends Authenticated{

    public $expenseCategories = [];
    public $paymentCategories = [];

    public function newAction(){
      
        $this->expenseCategories = Expenditure::getDefaultExpenseCategories();
        View::renderTemplate('Expense/new.html', [
            'categories' => $this->expenseCategories,
            'presentDate' => $this->getPresentDate()
        ]);
    }

    protected function getPresentDate(){
        return date("Y-m-d"); 
    }

}

?>