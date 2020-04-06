<?php

namespace controller;

use exceptions\AuthorizationException;
use exceptions\InvalidArgumentException;
use model\AddedToPlaylistDAO;
use model\Playlist;
use model\PlaylistDAO;
use model\VideoDAO;

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
            $params = [
                'playlist_title' => $playlist->getTitle(),
                'owner_id'       => $playlist->getOwnerId(),
                'date_created'   => $playlist->getDateCreated()
            ];
            $playlistDao->insert($params);

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
        $params = [
            'owner_id' => $ownerId
        ];
        $playlists = $playlistDao->findBy($params);

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
        $exists = $playlistDao->find($playlistId);
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
        $addedToPlaylistDao = new AddedToPlaylistDAO();
        $videoDao = new VideoDAO();
        $playlist = $playlistDao->find($playlistId);
        if (empty($playlist)) {
            throw new InvalidArgumentException("Invalid playlist.");
            }
        if ($playlist["owner_id"] != $_SESSION["logged_user"]["id"]) {
            throw new AuthorizationException("Unauthorized user.");
        }
        $existsVideo = $videoDao->find($videoId);
        if (empty($existsVideo)) {
            throw new InvalidArgumentException("Invalid video.");
        }
        $date = date("Y-m-d H:i:s");
        $params = [
            'playlist_id' => $playlistId,
            'video_id'    => $videoId
        ];
        $existsRecord = $addedToPlaylistDao->findBy($params);
        if ($existsRecord) {
            $params = [
                'date_added' => $date
            ];
            $conditions = [
                'playlist_id' => $playlistId,
                'video_id'    => $videoId
            ];
            $addedToPlaylistDao->update($params, $conditions);
        } else {
            $params = [
                'playlist_id' => $playlistId,
                'video_id'    => $videoId,
                'date_added'  => $date
            ];
            $addedToPlaylistDao->insert($params);
        }
    }

    public function getMyPlaylistsJSON()
    {
        $ownerId = $_SESSION["logged_user"]["id"];
        $playlistDao = new PlaylistDAO();
        $params = [
            'owner_id' => $ownerId
        ];
        $playlists = $playlistDao->findBy($params);

        echo json_encode($playlists);
    }
}