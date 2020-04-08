<?php

namespace controller;

use components\router\http\Request;
use exceptions\AuthorizationException;
use exceptions\InvalidArgumentException;
use services\PlaylistService;

class PlaylistController extends AbstractController
{
    /**
     * @var PlaylistService
     */
    private $playlistService;

    /**
     * PlaylistController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->playlistService = new PlaylistService();
    }

    /**
     * @return void
     *
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
            $this->playlistService->create($postParams);
        } else {
            throw new InvalidArgumentException("Invalid arguments.");
        }
    }

    /**
     * @return void
     */
    public function getMyPlaylists()
    {
        $this->playlistService->getMyPlaylists();
    }

    /**
     * @return void
     *
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
        $this->playlistService->clickedPlaylist($getParams);
    }

    /**
     * @return void
     *
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
        $this->playlistService->addToPlaylist($getParams);
    }

    /**
     * @return void
     */
    public function getMyPlaylistsJSON()
    {
        $this->playlistService->getMyPlalistsJSON();
    }
}