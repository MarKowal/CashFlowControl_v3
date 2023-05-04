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
        $this->paymentCategories = Expenditure::getDefaultPaymentCategories();

        View::renderTemplate('Expense/new.html', [
            'categories' => $this->expenseCategories,
            'presentDate' => $this->getPresentDate(),
            'payments' => $this->paymentCategories
        ]);
    }

    protected function getPresentDate(){
        return date("Y-m-d"); 
    }

}

?>