<?php

namespace App\Controllers;

use \Core\View;

class Edit extends Authenticated{

    public function newAction(){

        //trzeba pobrać i wyświetlić kategorie przychodów danego usera z ich ID

        View::renderTemplate('Edit/new.html');
    }

    public function addIncomeCatAction(){
        echo "Add category";
    }

    public function rewriteIncomeCatAction(){
        echo "Rewrite category";
    }

    public function deleteIncomeCatAction(){
        echo "Delete category";
    }


}