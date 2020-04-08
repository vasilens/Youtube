<?php

namespace services;

use model\PlaylistDAO;
use model\UserDAO;
use model\VideoDAO;

class SearchService
{
    /**
     * @param array $postParams
     *
     * @return void
     */
    public function search(array $postParams)
    {
        $searchQuery = htmlentities($postParams['search_query']);
        $userDao = new UserDAO();
        $videoDao = new VideoDAO();
        $playlistDao = new PlaylistDAO();
        $videos = $videoDao->getSearchedVideos($searchQuery);
        $playlists = $playlistDao->getSearchedPlaylists($searchQuery);
        $users = $userDao->getSearchedUsers($searchQuery);

        include_once "view/main.php";
    }
}