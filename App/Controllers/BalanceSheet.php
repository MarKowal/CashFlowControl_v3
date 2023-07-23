<?php

namespace App\Controllers;

use \Core\View;
use App\Models\IncomesForBalanceSheet;
use App\Models\ExpensesForBalanceSheet;

class BalanceSheet extends Authenticated{
    
    private $goodBalanceMessage;
    private $balanceMessage;

    public function newAction(){
        View::renderTemplate('BalanceSheet/new.html');
    }

    public function showAction(){

        $incomes = new IncomesForBalanceSheet();
        $expenses = new ExpensesForBalanceSheet();

        if ($incomes->sumUpIncomes() > $expenses->sumUpExpenses()){
            $this->goodBalanceMessage = true;
            $this->balanceMessage = 'Very Good! You have savings.';
        } else {
            $this->balanceMessage = 'Sorry! Could be better.';
        } 
        
        View::renderTemplate('BalanceSheet/show.html', [
            'sumOfIncomes' => $incomes->sumUpIncomes(),
            'balanceOfIncomes' => $incomes->makeIncomesBalanceSheet(),
            'sumOfExpenses' => $expenses->sumUpExpenses(),
            'balanceOfExpenses' => $expenses->makeExpensesBalanceSheet(),
            'goodBalanceMessage' => $this->goodBalanceMessage,
            'balanceMessage' => $this->balanceMessage
        ]);
    }
}

?>