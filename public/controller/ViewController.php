<?php

namespace controller;

use model\VideoDAO;

class ViewController
{
    /**
     * @param $view
     */
    public function viewRouter($view)
    {
        if ($view== 'upload') {
            $videoDao = VideoDAO::getInstance();
            $categories = $videoDao->getCategories();
        }

        include_once "view/$view.php";
    }
}