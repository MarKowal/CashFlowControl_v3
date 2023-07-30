<?php

namespace App\Models;

use PDO;

class Expenditure extends \Core\Model{

    private $amount;
    private $date;
    private $paymentCategory;
    private $expenseCategory;
    private $expenseComment;
    private $id;
    private $user_id;
    private $name;
    public $errorMessage; 

    public function __construct($data = []){
         foreach($data as $key => $value){
            $this->$key = $value;
         };
    }

    public static function getDefaultExpenseCategories(){

        $sql = 'SELECT name FROM expenses_category_default ORDER BY name ASC';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    
    public static function getDefaultPaymentCategories(){

        $sql = 'SELECT name FROM payment_methods_default ORDER BY name ASC';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    private function checkIfUserHasDefaultExpenseCategories(){

        $sql = 'SELECT name FROM expenses_category_assigned_to_users WHERE user_id = :id';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();

        if($stmt->fetch(PDO::FETCH_COLUMN, 1)){
            return true;
        } 
        return false;
    }

    private function saveExpensesToAssignedCategories(){
        
        $db = static::getDB();
        $sql = 'INSERT INTO expenses_category_assigned_to_users (user_id, name) 
                SELECT :user_id, name FROM expenses_category_default ORDER BY name ASC';
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();
    }

    private function  checkIfUserHasDefaultPaymentCategories(){

        $sql = 'SELECT name FROM payment_methods_assigned_to_users WHERE user_id = :user_id';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();

        if($stmt->fetch(PDO::FETCH_COLUMN, 1)){
            return true;
        } 
        return false;
    }

    private function savePaymentsToAssignedCategories(){
        
        $db = static::getDB();
        $sql = 'INSERT INTO payment_methods_assigned_to_users (user_id, name) 
                SELECT :id, name FROM payment_methods_default';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();
    }
   
    private function validate(){
        
        if (! isset($this->amount)){
            $this->errorMessage = 'Amount is required.';
            return false;
        }
        elseif ((int)$this->amount <= 0){
            $this->errorMessage = 'Amount must be more than zero.';
            return false;
        }
        elseif  (! isset($this->date)){
            $this->errorMessage = 'Date is required.';
            return false;
        }
        elseif  (! isset($this->paymentCategory)){
            $this->errorMessage = 'Payment category is required.';
            return false;
        }
        elseif  (! isset($this->expenseCategory)){
            $this->errorMessage = 'Expense category is required.';
            return false;
        }
        elseif (! strtotime($this->date)){
            $this->errorMessage = 'Date must be a date-type';
            return false;
        }
        return true;
    }

    private function getExpenseCategoryIdAssignedToUser(){

        $sql = 'SELECT id FROM expenses_category_assigned_to_users WHERE user_id = :id AND name = :expenseCategory';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':expenseCategory', $this->expenseCategory, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_COLUMN, 0);
    }

    private function getPaymentCategoryIdAssignedToUser(){

        $sql = 'SELECT id FROM payment_methods_assigned_to_users WHERE user_id = :id AND name = :paymentCategory';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':paymentCategory', $this->paymentCategory, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_COLUMN, 0);
    }


    public function saveToExpenses(){

        if($this->checkIfUserHasDefaultExpenseCategories() == false){
            $this->saveExpensesToAssignedCategories();
        } 
        if($this->checkIfUserHasDefaultPaymentCategories() == false){
            $this->savePaymentsToAssignedCategories();
        } 

        if ($this->validate()){

            $sql = 'INSERT INTO expenses (user_id, exp_cat_assigned_user_id, pay_meth_assigned_user_id, 
                    amount, date_of_expense, expense_comment) 
                    VALUES (:user_id, :exp_cat_assigned_user_id, :pay_meth_assigned_user_id, 
                    :amount, :date_of_expense, :expense_comment)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(':exp_cat_assigned_user_id', $this->getExpenseCategoryIdAssignedToUser(), PDO::PARAM_INT);
            $stmt->bindValue(':pay_meth_assigned_user_id', $this->getPaymentCategoryIdAssignedToUser(), PDO::PARAM_INT);
            $stmt->bindValue(':amount', $this->amount, PDO::PARAM_INT);
            $stmt->bindValue(':date_of_expense', $this->date, PDO::PARAM_STR);
            $stmt->bindValue(':expense_comment', $this->expenseComment, PDO::PARAM_STR);

            return $stmt->execute();

        } else {
            return false;
        }
    }

    public function getExpenseResult($startDate, $endDate){

        $user_id = $_SESSION['user_id'];

        $sql = "SELECT expenses.exp_cat_assigned_user_id, SUM(expenses.amount) 
                AS amountOfExpensesByCategoryAndPeriodOfTime FROM expenses, expenses_category_assigned_to_users 
                WHERE expenses.user_id = :user_id AND expenses.date_of_expense BETWEEN :startDate AND :endDate 
                AND expenses.exp_cat_assigned_user_id = expenses_category_assigned_to_users.id
                GROUP BY expenses.exp_cat_assigned_user_id ORDER BY amountOfExpensesByCategoryAndPeriodOfTime DESC"; 

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':startDate', $startDate, PDO::PARAM_STR);
        $stmt->bindParam(':endDate', $endDate, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getExpenseCategoryNames(){

        $sql = 'SELECT id, name FROM expenses_category_assigned_to_users WHERE user_id = :id';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        return $stmt->fetchAll();
    }


    public function getExpenseCategoryNameAssignedToUser(){

        $sql = 'SELECT name FROM expenses_category_assigned_to_users WHERE user_id = :id';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPaymentCategoryNameAssignedToUser(){

        $sql = 'SELECT name FROM payment_methods_assigned_to_users WHERE user_id = :id';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>