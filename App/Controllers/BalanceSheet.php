<?php

namespace App\Controllers;

use \Core\View;
use App\Models\Earning;
use App\Models\Expenditure;
use DateTime; 
use App\Flash;
use App\Controllers\TimeAndDate;


class BalanceSheet extends Authenticated{
    
    private $dateStart;
    private $dateEnd;
    private $sumOfIncomes;
    private $balanceOfIncomes;
    private $sumOfExpenses;
    private $balanceOfExpenses;
    private $flagForBalanceMessage;
    private $balanceMessage;


    public function newAction(){
        View::renderTemplate('BalanceSheet/new.html');
    }

    public function prepareIncomesAndExpensesToShow(){

        $this->indicateStartAndEndDates();

        $incomes = new IncomesForBalanceSheet($this->dateStart, $this->dateEnd);
        if($incomes->makeIncomesBalanceSheet()){
            $this->balanceOfIncomes = $incomes->makeIncomesBalanceSheet();
            $this->sumOfIncomes = $incomes->sumUpIncomes();
        } else {
            $this->balanceOfIncomes = ["-","none","-"];
            $this->sumOfIncomes = 0;
        }

        $expenses = new ExpensesForBalanceSheet($this->dateStart, $this->dateEnd);
        if($expenses->makeExpensesBalanceSheet()){
            $this->balanceOfExpenses = $expenses->makeExpensesBalanceSheet();
            $this->sumOfExpenses = $expenses->sumUpExpenses();
        } else {
            $this->balanceOfExpenses = ["-","none","-"];
            $this->sumOfExpenses = 0;
        }

        $this->makeMessageAfterBalance();
        $this->show();
    }

    public function showAction(){
        
        View::renderTemplate('BalanceSheet/show.html', [
            'sumOfIncomes' => $this->sumOfIncomes,
            'balanceOfIncomes' => $this->balanceOfIncomes,
            'sumOfExpenses' => $this->sumOfExpenses,
            'balanceOfExpenses' => $this->balanceOfExpenses,
            'flagForBalanceMessage' => $this->flagForBalanceMessage,
            'balanceMessage' => $this->balanceMessage
        ]);


    }

    private function indicateStartAndEndDates(){

        $date = new TimeAndDate();

        if(isset($_POST['dateStartFromUser'])){
            if ($date->validateDatesFromUser()){
                $this->dateStart = $_POST['dateStartFromUser'];
                $this->dateEnd = $_POST['dateEndFromUser'];
            }
        } else {
            $this->dateStart = $date->indicateStartDate();
            $this->dateEnd = $date->indicateEndDate();
        }
    }

    private function makeMessageAfterBalance(){

        if ($this->sumOfIncomes > $this->sumOfExpenses){
            $this->flagForBalanceMessage = '1';
            $this->balanceMessage = 'Very Good! You have savings.';
        } elseif ($this->sumOfIncomes <= $this->sumOfExpenses){
            $this->balanceMessage = 'Sorry! Could be better.';
        } 
    }
    
}

?>