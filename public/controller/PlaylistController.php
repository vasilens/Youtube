<?php

namespace controller;

use exceptions\AuthorizationException;
use exceptions\InvalidArgumentException;
use model\Playlist;
use model\PlaylistDAO;

class PlaylistController extends AbstractController
{
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
            $owner_id = $postParams['owner_id'];
            $date_created = date("Y-m-d H:i:s");
            $playlist->setTitle($title);
            $playlist->setOwnerId($owner_id);
            $playlist->setDateCreated($date_created);
            $dao = PlaylistDAO::getInstance();
            $dao->create($playlist);
            include_once "view/playlists.php";
            echo "Created successfully!";
        } else {
            throw new InvalidArgumentException("Invalid arguments.");
        }
    }

    public function getMyPlaylists()
    {
        if (isset($_SESSION["logged_user"]["id"])) {
            $owner_id = $_SESSION["logged_user"]["id"];
            $dao = PlaylistDAO::getInstance();
            $playlists = $dao->getAllByUserId($owner_id);
            include_once "view/playlists.php";
        } else {
            include_once "view/playlists.php";
            echo "<h3>Login to view playlists!</h3>";
        }
    }

    public function clickedPlaylist()
    {
        $getParams = $this->request->getGetParams();
        if (isset($getParams['id'])) {
            $playlist_id = $getParams['id'];
        }
        if (empty($playlist_id)) {
            throw new InvalidArgumentException("Invalid arguments.");
        }
        $dao = PlaylistDAO::getInstance();
        $exists = $dao->existsPlaylist($playlist_id);
        if (!$exists) {
            throw new InvalidArgumentException("Invalid playlist.");
        }
        $videos = $dao->getVideosFromPlaylist($playlist_id);
        include_once "view/playlists.php";
    }

    public function addToPlaylist()
    {
        $getParams = $this->request->getGetParams();
        if (isset($getParams["playlist_id"]) && isset($getParams["video_id"])) {
            $playlist_id = $getParams["playlist_id"];
            $video_id = $getParams["video_id"];
        }
        if (empty($playlist_id) || empty($video_id)) {
            throw new InvalidArgumentException("Invalid arguments.");
        }
        $dao = PlaylistDAO::getInstance();
        $playlist = $dao->existsPlaylist($playlist_id);
        if (!$playlist) {
            throw new InvalidArgumentException("Invalid playlist.");
            }
        if ($playlist["owner_id"] != $_SESSION["logged_user"]["id"]) {
            throw new AuthorizationException("Unauthorized user.");
        }
        $existsVideo = $dao->existsVideo($video_id);
        if (!$existsVideo) {
            throw new InvalidArgumentException("Invalid video.");
        }
        $date = date("Y-m-d H:i:s");
        $existsRecord = $dao->existsRecord($playlist_id, $video_id);
        if ($existsRecord) {
            $dao->updateRecord($playlist_id, $video_id, $date);
        } else {
            $dao->addToPlaylist($playlist_id, $video_id, $date);
        }
    }

    public function getMyPlaylistsJSON()
    {
        $getParams = $this->request->getGetParams();
        if (isset($getParams["owner_id"])) {
            $owner_id = $getParams["owner_id"];
        } else {
            if (isset($_SESSION["logged_user"]["id"])) {
                $owner_id = $_SESSION["logged_user"]["id"];
            }
        }
        if (!empty($owner_id)) {
            $dao = PlaylistDAO::getInstance();
            $playlists = $dao->getAllByUserId($owner_id);
            echo json_encode($playlists);
        } else {
            echo "<h3>Login to view playlists!</h3>";
        }
    }
}