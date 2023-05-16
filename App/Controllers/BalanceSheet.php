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

    public function newAction(){
        View::renderTemplate('BalanceSheet/new.html');
        $this->indicateTimePeriod();
    }

    protected function indicateTimePeriod(){

        $this->timePeriod = $_POST['timePeriod'] ?? NULL;

        $this->selectedStartDate = new DateTime(); 
        $this->selectedEndDate = new DateTime();

        if(isset($_POST['dateStart'])){
            echo $this->selectedStartDateString = $_POST['dateStart'].'<br>';
            echo $this->selectedEndDateString = $_POST['dateEnd'].'<br>';

            if($this->validateStartAndEndDates() == true){
                echo 'both the Other Dates are good';
                exit();
                //teraz mam dwie zwalidowane daty od usera - początkową i końcową
            }
        }

        if($this->timePeriod != NULL){
            $this->transferTimePeriodIntoDate();
        }

        echo $this->selectedStartDateString.'<br>';
        echo $this->selectedEndDateString;
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
}

?>