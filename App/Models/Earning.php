<?php

namespace App\Models;

use PDO;

class Earning extends \Core\Model{

    public $errors = []; 

    public function __construct($data = []){
         foreach($data as $key => $value){
            $this->$key = $value;
         };
    }

    public static function getDefaultIncomeCategories(){

        $sql = 'SELECT * FROM incomes_category_default';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 1);
    }

    public function saveToAssignedCategories($categories){

        for($i=0; $i<count($categories); $i++){
            $sql = 'INSERT INTO incomes_category_assigned_to_users (user_id, name) 
                    VALUES (:user_id, :name)';
            
            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(':name', $categories[$i], PDO::PARAM_STR);
            $stmt->execute();
        }
    }

    public static function checkIfUserHasDefaultCategories(){

        $sql = 'SELECT * FROM incomes_category_assigned_to_users WHERE user_id = :id';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_INT);
        
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();

        return $stmt->fetch();
    }

    protected function getIncomeCategoryIdAssignedToUser(){

        $sql = 'SELECT * FROM incomes_category_assigned_to_users WHERE user_id = :id AND name = :incomeCategory';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':incomeCategory', $this->incomeCategory, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_COLUMN, 0);
    }


    public function saveToIncomes(){

        $this->validate();

        if (empty($this->errors)){

            $sql = 'INSERT INTO incomes (user_id, inc_cat_assigned_user_id, amount, date_of_income, income_comment) 
                    VALUES (:user_id, :inc_cat_assigned_user_id, :amount, :date_of_income, :income_comment)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(':inc_cat_assigned_user_id', $this->getIncomeCategoryIdAssignedToUser(), PDO::PARAM_INT);
            $stmt->bindValue(':amount', $this->amount, PDO::PARAM_INT);
            $stmt->bindValue(':date_of_income', $this->date, PDO::PARAM_STR);
            $stmt->bindValue(':income_comment', $this->incomeComment, PDO::PARAM_STR);

            return $stmt->execute();

        }
        return false;
    }

    protected function validate(){
        
        if ($this->amount == ''){
            $this->errors[] = 'Amount is required.';
        }

        if ($this->date == ''){
            $this->errors[] = 'Date is required.';
        }

        if ($this->incomeCategory == ''){
            $this->errors[] = 'Income category is required.';
        }

        if ((int)$this->amount <= 0){
            $this->errors[] = 'Amount must be more than zero.';
        }

        if (! strtotime($this->date)){
            $this->errors[] = 'Date must be a date-type';
        }

    }

    /*
    query("SELECT incomes.inc_cat_assigned_user_id, SUM(incomes.amount) 
    AS amountOfIncomesByCategoryAndPeriodOfTime FROM incomes, incomes_category_assigned_to_users 
    WHERE incomes.user_id = '$userID' AND incomes.date_of_income BETWEEN '$selectedStartDateString' AND '$selectedEndDateString' AND incomes.inc_cat_assigned_user_id = incomes_category_assigned_to_users.id 
    GROUP BY incomes.inc_cat_assigned_user_id ORDER BY amountOfIncomesByCategoryAndPeriodOfTime DESC");
    */

    public function getIncomesResult($startDate, $endDate){

        echo 'start date from Earning model = '.$startDate.'<br>';
        echo 'end date from Earning model = '.$endDate.'<br>';
        $user_id = $_SESSION['user_id'];

        $sql = "SELECT incomes.inc_cat_assigned_user_id, SUM(incomes.amount) 
        AS amountOfIncomesByCategoryAndPeriodOfTime FROM incomes, incomes_category_assigned_to_users 
        WHERE incomes.user_id = :user_id AND incomes.date_of_income BETWEEN :startDate AND :endDate 
        AND incomes.inc_cat_assigned_user_id = incomes_category_assigned_to_users.id 
        GROUP BY incomes.inc_cat_assigned_user_id ORDER BY amountOfIncomesByCategoryAndPeriodOfTime DESC"; 

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':startDate', $startDate, PDO::PARAM_STR);
        $stmt->bindParam(':endDate', $endDate, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetchAll();
        

    }


}