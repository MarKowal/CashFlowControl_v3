<?php

namespace App\Controllers;

use \Core\View;
use App\Models\Earning;
use App\Models\Expenditure;
use DateTime; 
use App\Flash;
use App\Controllers\TimeAndDate;
use App\Controllers\Income;


class BalanceSheet extends Authenticated{
    

    private $dateStart;
    private $dateEnd;
    //private $timePeriod;
   // private $selectedStartDate;
   // private $selectedEndDate;
   // private $selectedStartDateString;
   // private $selectedEndDateString;
    private $namesOfIncomes = [];
    private $amountOfIncomes;
    private $namesOfExpenses = [];
    private $amountOfExpenses = [];
    private $numberOfIncomes = [];
    private $numberOfExpenses = [];


    private $sumOfIncomes;
    private $sumOfExpenses;
    private $balanceOfIncomes;
    private $balanceOfExpenses;
    private $flagForBalanceMessage;
    private $balanceMessage;


    public function newAction(){
        View::renderTemplate('BalanceSheet/new.html');
    }

    public function showAction(){

        $this->indicateStartAndEndDates();

     //   echo $this->dateStart."<br>";
      //  echo $this->dateEnd;

       
        /*
        $this->matchIncomeIdWithCategoryName($this->dateStart, $this->dateEnd);
        $this->numberOfIncomes = $this->countNumberOfItems($this->namesOfIncomes);
        $this->sumOfIncomes = $this->sumUpCashFlow($this->amountOfIncomes);
        $this->balanceOfIncomes = $this->makeBalanceSheet($this->numberOfIncomes, $this->namesOfIncomes, $this->amountOfIncomes);

        $this->matchExpenseIdWithCategoryName($this->dateStart, $this->dateEnd);
        $this->numberOfExpenses = $this->countNumberOfItems($this->namesOfExpenses);
        $this->sumOfExpenses = $this->sumUpCashFlow($this->amountOfExpenses);
        $this->balanceOfExpenses = $this->makeBalanceSheet($this->numberOfExpenses, $this->namesOfExpenses, $this->amountOfExpenses);

        $this->makeMessageAfterBalance();

        View::renderTemplate('BalanceSheet/show.html', [
            'sumOfIncomes' => $this->sumOfIncomes,
            'balanceOfIncomes' => $this->balanceOfIncomes,
            'sumOfExpenses' => $this->sumOfExpenses,
            'balanceOfExpenses' => $this->balanceOfExpenses,
            'flagForBalanceMessage' => $this->flagForBalanceMessage,
            'balanceMessage' => $this->balanceMessage
        ]);

        */


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


        /*
        if($this->indicateStartDateAndEndDate()){

            $this->matchIncomeIdWithCategoryName($this->selectedStartDateString, $this->selectedEndDateString);
            $this->numberOfIncomes = $this->countNumberOfItems($this->namesOfIncomes);
            $this->sumOfIncomes = $this->sumUpCashFlow($this->amountOfIncomes);
            $this->balanceOfIncomes = $this->makeBalanceSheet($this->numberOfIncomes, $this->namesOfIncomes, $this->amountOfIncomes);

            $this->matchExpenseIdWithCategoryName($this->selectedStartDateString, $this->selectedEndDateString);
            $this->numberOfExpenses = $this->countNumberOfItems($this->namesOfExpenses);
            $this->sumOfExpenses = $this->sumUpCashFlow($this->amountOfExpenses);
            $this->balanceOfExpenses = $this->makeBalanceSheet($this->numberOfExpenses, $this->namesOfExpenses, $this->amountOfExpenses);

            $this->makeMessageAfterBalance();

            View::renderTemplate('BalanceSheet/show.html', [
                'sumOfIncomes' => $this->sumOfIncomes,
                'balanceOfIncomes' => $this->balanceOfIncomes,
                'sumOfExpenses' => $this->sumOfExpenses,
                'balanceOfExpenses' => $this->balanceOfExpenses,
                'flagForBalanceMessage' => $this->flagForBalanceMessage,
                'balanceMessage' => $this->balanceMessage
            ]);
        }
        */
    

    /*
    protected function indicateStartDateAndEndDate(){

        $this->timePeriod = $_POST['timePeriod'] ?? NULL;

        $this->selectedStartDate = new DateTime(); 
        $this->selectedEndDate = new DateTime();

        $incomes = new Earning();

        if(isset($_POST['dateStart'])){
            $this->selectedStartDateString = $_POST['dateStart'];
            $this->selectedEndDateString = $_POST['dateEnd'];

            if($this->validateStartAndEndDates() == true){     
                return true;
            } else {
                return false;
            }
        }

        if($this->timePeriod != NULL){
            $this->transferTimePeriodIntoDate();
            return true;
        } else {
            return false;
        }
    }

    protected function transferTimePeriodIntoDate(){

        $this->selectedEndDate->modify('now');

        if($this->timePeriod == 'presentMonth'){

            $this->selectedStartDate->modify('first day of this month');

        } elseif($this->timePeriod == 'previousMonth') {

            $this->selectedStartDate->modify('first day of last month');
            $this->selectedEndDate->modify('last day of last month');

        } elseif($this->timePeriod == 'presentYear'){

            $this->selectedStartDate->modify('1 January this year');

        } elseif($this->timePeriod == 'otherTime'){

            $this->redirect('/BalanceSheet/getTheOtherTime');
           
        }

        $this->selectedStartDateString = $this->selectedStartDate->format('Y-m-d');
        $this->selectedEndDateString = $this->selectedEndDate->format('Y-m-d');
    }
    
    public function getTheOtherTime(){
        View::renderTemplate('BalanceSheet/new.html', [
            'choosenTheOtherTime' => 1
        ]);
    }

    protected function validateStartAndEndDates(){
        if($this->selectedStartDateString < $this->selectedEndDateString){
            return true;
        } else {
            Flash::addMessages('Start date cannot be later than End date. Try again.', 'warning');
            $this->redirect('/BalanceSheet/new');
        }
    }*/

    protected function matchIncomeIdWithCategoryName($dateStart, $dateEnd){

        $incomes = new Earning();

        $sumOfIncomes = $incomes->getIncomesResult($dateStart, $dateEnd);
        $allIncomeCategoryNames = $incomes->getIncomeCategoryNames();
        
        for($i = 0; $i<count($sumOfIncomes); $i++){
            for($n = 0; $n<count($allIncomeCategoryNames); $n++){ 
                if($sumOfIncomes[$i]['inc_cat_assigned_user_id'] == $allIncomeCategoryNames[$n]['id']){
                    $this->namesOfIncomes[$i] = $allIncomeCategoryNames[$n]['name'];
                    $this->amountOfIncomes[$i] = $sumOfIncomes[$i]['amountOfIncomesByCategoryAndPeriodOfTime'];
                    break;
                } 
            }
        }
    }

    protected function matchExpenseIdWithCategoryName($dateStart, $dateEnd){

        $expenses = new Expenditure();

        $sumOfExpenses = $expenses->getExpenseResult($dateStart, $dateEnd);
        $allExpenseCategoryNames = $expenses->getExpenseCategoryNames();

        for($i = 0; $i<count($sumOfExpenses); $i++){
            for($n = 0; $n<count($allExpenseCategoryNames); $n++){ 
                if($sumOfExpenses[$i]['exp_cat_assigned_user_id'] == $allExpenseCategoryNames[$n]['id']){
                    $this->namesOfExpenses[$i] = $allExpenseCategoryNames[$n]['name'];
                    $this->amountOfExpenses[$i] = $sumOfExpenses[$i]['amountOfExpensesByCategoryAndPeriodOfTime'];
                    break;
                } 
            }
        }
    }

    protected function countNumberOfItems($names){
        $i = NULL;
        $numberOfItems = [];

        for($i = 0; $i<count($names); $i++){
            $numberOfItems[$i] = $i+1;
        }

        return  $numberOfItems;
    }
    
    protected function sumUpCashFlow($amounts){
        $cashFlow = [];
        $cashFlow = array_sum($amounts);
        return $cashFlow;
    }

    protected function makeBalanceSheet($number, $names, $amounts){
    
        $balance = [];
        $x = 0;
        $y = 0;

        while($y < count($number)){
      
             $balance[$x] = $number[$y];
             $balance[$x+1] = $names[$y];
             $balance[$x+2] = $amounts[$y];
             $y++;
             $x = $x + 3;
        }          

        return  $balance;
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