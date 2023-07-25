<?php

namespace App\Controllers;

use \Core\View;
use App\Models\IncomesForBalanceSheet;
use App\Models\ExpensesForBalanceSheet;
use App\Controllers\TimeAndDate;


class Balancesheet extends Authenticated{
    
    private $goodBalanceMessage;
    private $balanceMessage;
    private $balance;

    public function newAction(){
        View::renderTemplate('Balancesheet/new.html');
    }

    public function showAction(){

        $incomes = new IncomesForBalanceSheet();
        $expenses = new ExpensesForBalanceSheet();
        $time = new TimeAndDate();

        $this->getDiffBetweenIncomesAndExpenses($incomes->sumUpIncomes(), $expenses->sumUpExpenses());

        View::renderTemplate('Balancesheet/show.html', [
            'sumOfIncomes' => $incomes->sumUpIncomes(),
            'balanceOfIncomes' => $incomes->makeIncomesBalanceSheet(),
            'sumOfExpenses' => $expenses->sumUpExpenses(),
            'balanceOfExpenses' => $expenses->makeExpensesBalanceSheet(),
            'goodBalanceMessage' => $this->goodBalanceMessage,
            'balanceMessage' => $this->balanceMessage,
            'startDate' => $time->getStartDate(),
            'endDate' => $time->getEndDate(),
            'balance' => $this->balance
        ]);
    }

    private function getDiffBetweenIncomesAndExpenses($sumOfIncomes, $sumOfExpenses){

        $difference = $sumOfIncomes - $sumOfExpenses;

        if($difference > 0) {
            $this->goodBalanceMessage = true;
            $this->balanceMessage = 'Great, you have savings:';
            $this->balance = number_format((float)$difference, 2, '.', ' ');
        } else {
            $this->goodBalanceMessage = false;
            $this->balanceMessage = 'Sorry, you have debts:';
            $this->balance = number_format((float)$difference, 2, '.', ' ');
        }
    }
}

?>