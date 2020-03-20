<?php

namespace controller;

use exceptions\InvalidArgumentException;
use model\SearchDAO;

class SearchController extends AbstractController
{
    /**
     * @throws InvalidArgumentException
     */
    public function search()
    {
        $postParams = $this->request->getPostParams();
        if (isset($postParams['search'])) {
            if (empty(trim($postParams['search_query']))) {

                include_once "view/main.php";

                echo "<h3>Search field is empty.</h3>";

                return;
            }
                $searchQuery = htmlentities($postParams['search_query']);
                $searchDao = SearchDAO::getInstance();
                $videos = $searchDao->getSearchedVideos($searchQuery);
                $playlists = $searchDao->getSearchedPlaylists($searchQuery);
                $users = $searchDao->getSearchedUsers($searchQuery);

                include_once "view/main.php";
        } else {
            throw new InvalidArgumentException("Invalid arguments.");
        }
    }
}