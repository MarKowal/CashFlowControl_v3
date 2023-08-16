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

    public static function checkIfUserHasDefaultExpenseCategories(){

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

        if(self::checkIfUserHasDefaultExpenseCategories() == false){
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

        $sql = 'SELECT id, name FROM expenses_category_assigned_to_users WHERE user_id = :id ORDER BY name ASC';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        return $stmt->fetchAll();
    }


    public static function getExpenseCategoryNameAssignedToUser(){

        $sql = 'SELECT name FROM expenses_category_assigned_to_users WHERE user_id = :id ORDER BY name ASC';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public static function getPaymentCategoryNameAssignedToUser(){

        $sql = 'SELECT name FROM payment_methods_assigned_to_users WHERE user_id = :id ORDER BY name ASC';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function addNewExpenseCategory($newExpenseName){

        if ($this->validateNewExpenseCategoryName($newExpenseName)){
            if($this->addNewExpenseCategoryInDB($newExpenseName)){
                return true;
            }
        } else {
            return $this->errorMessage;
        }
    }

    private function validateNewExpenseCategoryName($newExpenseName){

        if (strlen($newExpenseName) < 2){
            $this->errorMessage = 'Category name must have at least 2 characters.';
            return false;
        }        

        if (preg_match('/[A-Z]+/', $newExpenseName) == 1){
            $this->errorMessage = 'Category cannot include big letters.';
            return false;
        }

        if (preg_match('/[\d]/', $newExpenseName) == 1){
            $this->errorMessage = 'Category cannot include numbers.';
            return false;
        }

        if (preg_match('/\`|\~|\!|\@|\#|\$|\%|\^|\&|\*|\(|\)|\+|\=|\[|\{|\]|\}|\||\\|\'|\<|\,|\.|\>|\?|\/|\""|\;|\:/', $newExpenseName) == 1){
            $this->errorMessage = 'Category cannot include special characters.';
            return false;
        }

        return true;
    }

    private function addNewExpenseCategoryInDB($newExpenseName){

        $sql = 'INSERT INTO expenses_category_assigned_to_users (user_id, name) 
        VALUES (:user_id, :name)';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':name', $newExpenseName, PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function addNewPaymentCategory($newPaymentName){

        if ($this->validateNewExpenseCategoryName($newPaymentName)){
            if($this->addNewPaymentCategoryInDB($newPaymentName)){
                return true;
            }
        } else {
            return $this->errorMessage;
        }
    }

    private function addNewPaymentCategoryInDB($newPaymentName){

        $sql = 'INSERT INTO payment_methods_assigned_to_users (user_id, name) 
        VALUES (:user_id, :name)';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':name', $newPaymentName, PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function renameExpenseCategory($oldExpenseName, $newExpenseName){

        if ($this->validateNewExpenseCategoryName($newExpenseName)){
            if($this->updateNewExpenseCategoryInDB($oldExpenseName, $newExpenseName)){
                return true;
            }
        } else {
            return $this->errorMessage;
        }
    }

    private function updateNewExpenseCategoryInDB($oldExpenseName, $newExpenseName){

        $sql = 'UPDATE expenses_category_assigned_to_users SET 
                name = :newExpenseName
                WHERE user_id = :user_id AND name = :oldExpenseName';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':newExpenseName', $newExpenseName, PDO::PARAM_STR);
        $stmt->bindValue(':oldExpenseName', $oldExpenseName, PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function renamePaymentCategory($oldPaymentName, $newPaymentName){

        if ($this->validateNewExpenseCategoryName($newPaymentName)){
            if($this->updateNewPaymentCategoryInDB($oldPaymentName, $newPaymentName)){
                return true;
            }
        } else {
            return $this->errorMessage;
        }
    }

    private function updateNewPaymentCategoryInDB($oldPaymentName, $newPaymentName){

        $sql = 'UPDATE payment_methods_assigned_to_users SET 
                name = :newPaymentName
                WHERE user_id = :user_id AND name = :oldPaymentName';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':newPaymentName', $newPaymentName, PDO::PARAM_STR);
        $stmt->bindValue(':oldPaymentName', $oldPaymentName, PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function deleteExpenseCategory($expenseName){

        $this->deleteAllExpensesFromGivenCategory($expenseName);

        $sql = 'DELETE FROM expenses_category_assigned_to_users  
                WHERE user_id = :user_id AND name = :expenseName';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':expenseName', $expenseName, PDO::PARAM_STR);

        return $stmt->execute();
    }

    private function deleteAllExpensesFromGivenCategory($expenseName){

        $expenseID = $this->getByNameExpenseCategoryIdAssignedToUser($expenseName);
        
        $sql = 'DELETE FROM expenses  
        WHERE user_id = :user_id AND exp_cat_assigned_user_id = :expenseID';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':expenseID', $expenseID, PDO::PARAM_INT);

        return $stmt->execute();
    }

    private function getByNameExpenseCategoryIdAssignedToUser($expenseName){

        $sql = 'SELECT id FROM expenses_category_assigned_to_users WHERE user_id = :id AND name = :expenseCategory';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':expenseCategory', $expenseName, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_COLUMN, 0);
    }

    public function deletePaymentCategory($paymentName){

        $this->deleteAllPaymentsFromGivenCategory($paymentName);

        $sql = 'DELETE FROM payment_methods_assigned_to_users  
                WHERE user_id = :user_id AND name = :paymentName';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':paymentName', $paymentName, PDO::PARAM_STR);

        return $stmt->execute();
    }

    private function deleteAllPaymentsFromGivenCategory($paymentName){

        $paymentID = $this->getByNamePaymentCategoryIdAssignedToUser($paymentName);
        
        $sql = 'DELETE FROM expenses  
        WHERE user_id = :user_id AND pay_meth_assigned_user_id = :paymentID';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':paymentID', $paymentID, PDO::PARAM_INT);

        return $stmt->execute();
    }

    private function getByNamePaymentCategoryIdAssignedToUser($paymentName){

        $sql = 'SELECT id FROM payment_methods_assigned_to_users WHERE user_id = :id AND name = :paymentName';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':paymentName', $paymentName, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_COLUMN, 0);
    }
}

?>