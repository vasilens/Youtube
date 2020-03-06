<?php


namespace controller;


use exceptions\InvalidArgumentException;
use model\SearchDAO;
use model\UserDAO;

class SearchController{
    public function search(){
        if(isset($_POST['search'])){
            if(empty(trim($_POST['search_query']))) {
                include_once "view/main.php";
                echo "<h3>Search field is empty.</h3>";
                return;
            }
                $searchQuery = htmlentities($_POST['search_query']);
                $dao = SearchDAO::getInstance();
                $videos = $dao->getSearchedVideos($searchQuery);
                $playlists = $dao->getSearchedPlaylists($searchQuery);
                $users = $dao->getSearchedUsers($searchQuery);
                include_once "view/main.php";
        }
        else {
            throw new InvalidArgumentException("Invalid arguments.");
        }
    }
}