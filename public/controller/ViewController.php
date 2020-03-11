<?php


namespace controller;


use model\VideoDAO;

class ViewController{
    public function viewRouter($view) {
        if ($view== 'upload'){
                $dao = VideoDAO::getInstance();
                $categories = $dao->getCategories();
        }
        include_once "view/$view.php";
    }
}