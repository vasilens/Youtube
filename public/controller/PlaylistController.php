<?php

namespace controller;

use exceptions\AuthorizationException;
use exceptions\InvalidArgumentException;
use model\Playlist;
use model\PlaylistDAO;

class PlaylistController extends AbstractController
{
    /**
     * @throws AuthorizationException
     * @throws InvalidArgumentException
     */
    public function create()
    {
        $postParams = $this->request->getPostParams();
        if (isset($postParams['create'])) {
            $error = false;
            $msg = "";
            if (!isset($postParams['title']) || empty(trim($postParams["title"]))) {

                include_once "view/createPlaylist.php";

                echo "Title is empty";

                return;
            }
            if (!isset($postParams["owner_id"]) || empty($postParams["owner_id"])) {
                throw new InvalidArgumentException("Invalid arguments.");
            }
            if ($postParams["owner_id"] != $_SESSION["logged_user"]["id"]) {
                throw new AuthorizationException("Unauthorized user.");
            }
            $playlist = new Playlist();
            $title = $postParams['title'];
            $ownerId = $postParams['owner_id'];
            $date_created = date("Y-m-d H:i:s");
            $playlist->setTitle($title);
            $playlist->setOwnerId($ownerId);
            $playlist->setDateCreated($date_created);
            $playlistDao = new PlaylistDAO();
            $playlistDao->create($playlist);

            include_once "view/playlists.php";

            echo "Created successfully!";
        } else {
            throw new InvalidArgumentException("Invalid arguments.");
        }
    }

    public function getMyPlaylists()
    {
        $ownerId = $_SESSION["logged_user"]["id"];
        $playlistDao = new PlaylistDAO();
        $playlists = $playlistDao->getAllByUserId($ownerId);

        include_once "view/playlists.php";
    }

    /**
     * @throws InvalidArgumentException
     */
    public function clickedPlaylist()
    {
        $getParams = $this->request->getGetParams();
        if (isset($getParams['id'])) {
            $playlistId = $getParams['id'];
        }
        if (empty($playlistId)) {
            throw new InvalidArgumentException("Invalid arguments.");
        }
        $playlistDao = new PlaylistDAO();
        $exists = $playlistDao->findAllAssoc(['id' => $playlistId]);
        if (!$exists) {
            throw new InvalidArgumentException("Invalid playlist.");
        }
        $videos = $playlistDao->getVideosFromPlaylist($playlistId);

        include_once "view/playlists.php";
    }

    /**
     * @throws AuthorizationException
     * @throws InvalidArgumentException
     */
    public function addToPlaylist()
    {
        $getParams = $this->request->getGetParams();
        if (isset($getParams["playlist_id"]) && isset($getParams["video_id"])) {
            $playlistId = $getParams["playlist_id"];
            $videoId = $getParams["video_id"];
        }
        if (empty($playlistId) || empty($videoId)) {
            throw new InvalidArgumentException("Invalid arguments.");
        }
        $playlistDao = new PlaylistDAO();
        $playlist = $playlistDao->existsPlaylist($playlistId);
        if (!$playlist) {
            throw new InvalidArgumentException("Invalid playlist.");
            }
        if ($playlist["owner_id"] != $_SESSION["logged_user"]["id"]) {
            throw new AuthorizationException("Unauthorized user.");
        }
        $existsVideo = $playlistDao->existsVideo($videoId);
        if (!$existsVideo) {
            throw new InvalidArgumentException("Invalid video.");
        }
        $date = date("Y-m-d H:i:s");
        $existsRecord = $playlistDao->existsRecord($playlistId, $videoId);
        if ($existsRecord) {
            $playlistDao->updateRecord($playlistId, $videoId, $date);
        } else {
            $playlistDao->addToPlaylist($playlistId, $videoId, $date);
        }
    }

    public function getMyPlaylistsJSON()
    {
        $ownerId = $_SESSION["logged_user"]["id"];
        $playlistDao = new PlaylistDAO();
        $playlists = $playlistDao->getAllByUserId($ownerId);

        echo json_encode($playlists);
    }
}