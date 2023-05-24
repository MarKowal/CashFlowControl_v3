<?php

namespace App\Controllers;

use \Core\View;
use App\Models\Earning;
use App\Models\Expenditure;
use DateTime; //używam wbudowanej w PHP klasy DateTime()
use App\Flash;


class BalanceSheet extends Authenticated{
    
    public $timePeriod;
    protected $selectedStartDate;
    protected $selectedEndDate;
    public $selectedStartDateString;
    public $selectedEndDateString;
    public $namesOfIncomes = [];
    public $amountOfIncomes = [];

    public function newAction(){
        View::renderTemplate('BalanceSheet/new.html');
        $this->indicateTimePeriod();

        echo '<br>the namesOfIncomes table:<br>';
        print_r($this->namesOfIncomes);

        echo '<br>the amountOfIncomes table:<br>';
        print_r($this->amountOfIncomes);
    }

    protected function indicateTimePeriod(){

        $this->timePeriod = $_POST['timePeriod'] ?? NULL;

        $this->selectedStartDate = new DateTime(); 
        $this->selectedEndDate = new DateTime();

        $incomes = new Earning();
        $sumOfIncomes = NULL;
        echo '<pre>';          

        if(isset($_POST['dateStart'])){
            $this->selectedStartDateString = $_POST['dateStart'];
            $this->selectedEndDateString = $_POST['dateEnd'];

            if($this->validateStartAndEndDates() == true){     
                $this->matchIncomeIdWithCategoryName($this->selectedStartDateString, $this->selectedEndDateString);
            }
        }

        if($this->timePeriod != NULL){
            $this->transferTimePeriodIntoDate();
            $this->matchIncomeIdWithCategoryName($this->selectedStartDateString, $this->selectedEndDateString);
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

            $this->redirect('/BalanceSheet/getOtherTime');
           
        }

        $this->selectedStartDateString = $this->selectedStartDate->format('Y-m-d');
        $this->selectedEndDateString = $this->selectedEndDate->format('Y-m-d');
    }
    
    public function getOtherTime(){
        View::renderTemplate('BalanceSheet/new.html', [
            'choosenOtherTime' => 1
        ]);
    }

    protected function validateStartAndEndDates(){
        if($this->selectedStartDateString < $this->selectedEndDateString){
            return true;
        } else {
            Flash::addMessages('Sorry, start-date cannot be later than end-date. Try again.', 'warning');
            $this->redirect('/BalanceSheet/new');
        }
    }

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
        /*
        echo '<br>the namesOfIncomes table:<br>';
        print_r($namesOfIncomes);

        echo '<br>the amountOfIncomes table:<br>';
        print_r($amountOfIncomes);
        */
    }
}

?>