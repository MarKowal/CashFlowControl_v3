<?php

namespace App\Models;

use PDO;

class Earning extends \Core\Model{

    public static function getDefaultIncomeCategories(){

        $sql = 'SELECT * FROM incomes_category_default';
        //$sql = 'SELECT name_income FROM incomes_category_default';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        
        //echo "<pre>"; 
        //var_dump($stmt->fetchAll());

        return $stmt->fetchAll(PDO::FETCH_COLUMN, 1);

    }

}