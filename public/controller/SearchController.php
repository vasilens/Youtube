<?php

namespace controller;

use exceptions\InvalidArgumentException;
use model\PlaylistDAO;
use model\UserDAO;
use model\VideoDAO;

class SearchController extends AbstractController
{
    /**
     * @return void
     *
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
            $userDao = new UserDAO();
            $videoDao = new VideoDAO();
            $playlistDao = new PlaylistDAO();
            $videos = $videoDao->getSearchedVideos($searchQuery);
            $playlists = $playlistDao->getSearchedPlaylists($searchQuery);
            $users = $userDao->getSearchedUsers($searchQuery);

            include_once "view/main.php";
        } else {
            throw new InvalidArgumentException("Invalid arguments.");
        }
    }
}