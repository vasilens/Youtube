<?php

namespace services;

use exceptions\AuthorizationException;
use exceptions\InvalidArgumentException;
use model\AddedToPlaylistDAO;
use model\Playlist;
use model\PlaylistDAO;
use model\VideoDAO;

class PlaylistService extends AbstractService
{

    /**
     * @return void
     */
    protected function setDao()
    {
        $this->dao = new PlaylistDAO();
    }

    /**
     * @param array $postParams
     *
     * @return void
     */
    public function create(array $postParams)
    {
        $playlist = new Playlist();
        $title = $postParams['title'];
        $ownerId = $postParams['owner_id'];
        $date_created = date("Y-m-d H:i:s");
        $playlist->setTitle($title);
        $playlist->setOwnerId($ownerId);
        $playlist->setDateCreated($date_created);
        $params = [
            'playlist_title' => $playlist->getTitle(),
            'owner_id'       => $playlist->getOwnerId(),
            'date_created'   => $playlist->getDateCreated()
        ];
        $this->dao->insert($params);

        include_once "view/playlists.php";

        echo "Created successfully!";
    }

    /**
     * @return void
     */
    public function getMyPlaylists()
    {
        $ownerId = $_SESSION["logged_user"]["id"];
        $params = [
            'owner_id' => $ownerId
        ];
        $playlists = $this->dao->findBy($params);

        include_once "view/playlists.php";
    }

    /**
     * @param array $getParams
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public function clickedPlaylist(array $getParams)
    {
        $playlistId = $getParams['id'];
        $exists = $this->dao->find($playlistId);
        if (!$exists) {
            throw new InvalidArgumentException("Invalid playlist.");
        }
        $videos = $this->dao->getVideosFromPlaylist($playlistId);

        include_once "view/playlists.php";
    }

    /**
     * @param array $getParams
     *
     * @return void
     *
     * @throws AuthorizationException
     * @throws InvalidArgumentException
     */
    public function addToPlaylist(array $getParams)
    {
        $playlistId = $getParams["playlist_id"];
        $videoId = $getParams["video_id"];
        $addedToPlaylistDao = new AddedToPlaylistDAO();
        $videoDao = new VideoDAO();
        $playlist = $this->dao->find($playlistId);
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

    /**
     * @return void
     */
    public function getMyPlalistsJSON()
    {
        $ownerId = $_SESSION["logged_user"]["id"];
        $params = [
            'owner_id' => $ownerId
        ];
        $playlists = $this->dao->findBy($params);

        echo json_encode($playlists);
    }
}