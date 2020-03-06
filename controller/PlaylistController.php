<?php


namespace controller;

use exceptions\AuthorizationException;
use exceptions\InvalidArgumentException;
use model\Playlist;
use model\PlaylistDAO;

class PlaylistController
{
    public function create()
    {
        if (isset($_POST['create'])) {
            $error = false;
            $msg = "";
            if (!isset($_POST['title']) || empty(trim($_POST["title"]))) {
                include_once "view/createPlaylist.php";
                echo "Title is empty";
                return;
            }
            if (!isset($_POST["owner_id"]) || empty($_POST["owner_id"])) {
                throw new InvalidArgumentException("Invalid arguments.");
            }
            if ($_POST["owner_id"] != $_SESSION["logged_user"]["id"]) {
                throw new AuthorizationException("Unauthorized user.");
            }
            $playlist = new Playlist();
            $title = $_POST['title'];
            $owner_id = $_POST['owner_id'];
            $date_created = date("Y-m-d H:i:s");
            $playlist->setTitle($title);
            $playlist->setOwnerId($owner_id);
            $playlist->setDateCreated($date_created);
            $dao = PlaylistDAO::getInstance();
            $dao->create($playlist);
            include_once "view/playlists.php";
            echo "Created successfully!";
        }
        else {
            throw new InvalidArgumentException("Invalid arguments.");
        }
    }

    public function getMyPlaylists()
    {
        if (isset($_SESSION["logged_user"]["id"])){
            $owner_id = $_SESSION["logged_user"]["id"];
            $dao = PlaylistDAO::getInstance();
            $playlists = $dao->getAllByUserId($owner_id);
            include_once "view/playlists.php";
        }
        else {
            include_once "view/playlists.php";
            echo "<h3>Login to view playlists!</h3>";
        }
    }

    public function clickedPlaylist()
    {
        if (isset($_GET['id'])) {
            $playlist_id = $_GET['id'];
        }
        if (empty($playlist_id)){
            throw new InvalidArgumentException("Invalid arguments.");
        }
        $dao = PlaylistDAO::getInstance();
        $exists = $dao->existsPlaylist($playlist_id);
        if (!$exists){
            throw new InvalidArgumentException("Invalid playlist.");
        }
        $videos = $dao->getVideosFromPlaylist($playlist_id);
        include_once "view/playlists.php";
    }

    public function addToPlaylist()
    {
        $playlist_id = $_GET['playlist_id'];
        $video_id = $_GET['video_id'];
        if (empty($playlist_id) || empty($video_id)) {
            throw new InvalidArgumentException("Invalid arguments.");
        }
        $dao = PlaylistDAO::getInstance();
        $playlist = $dao->existsPlaylist($playlist_id);
        if (!$playlist){
            throw new InvalidArgumentException("Invalid playlist.");
            }
        if ($playlist["owner_id"] != $_SESSION["logged_user"]["id"]){
            throw new AuthorizationException("Unauthorized user.");
        }
        $existsVideo = $dao->existsVideo($video_id);
        if (!$existsVideo){
            throw new InvalidArgumentException("Invalid video.");
        }
        $date = date("Y-m-d H:i:s");
        $existsRecord = $dao->existsRecord($playlist_id, $video_id);
        if ($existsRecord){
            $dao->updateRecord($playlist_id, $video_id, $date);
        }
        else {
            $dao->addToPlaylist($playlist_id, $video_id, $date);
        }
    }

    public function getMyPlaylistsJSON()
    {
        if (isset($_GET["owner_id"])) {
            $owner_id = $_GET["owner_id"];
        }
        else {
            if (isset($_SESSION["logged_user"]["id"])){
                $owner_id = $_SESSION["logged_user"]["id"];
            }
        }
        if (!empty($owner_id)) {
            $dao = PlaylistDAO::getInstance();
            $playlists = $dao->getAllByUserId($owner_id);
            echo json_encode($playlists);
        }
        else {
            echo "<h3>Login to view playlists!</h3>";
        }
    }
}