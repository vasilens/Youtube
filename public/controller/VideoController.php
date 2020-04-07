<?php

namespace controller;

include_once "fileHandler.php";

use exceptions\AuthorizationException;
use exceptions\InvalidArgumentException;
use model\CategoryDAO;
use model\UserDAO;
use model\UsersFollowUsersDAO;
use model\UsersReactCommentsDAO;
use model\UsersReactVideosDAO;
use model\Video;
use model\VideoDAO;
use exceptions\InvalidFileException;

class VideoController extends AbstractController
{
    /**
     * @return void
     *
     * @throws AuthorizationException
     * @throws InvalidArgumentException
     * @throws InvalidFileException
     */
    public function upload()
    {
        $postParams = $this->request->getPostParams();
        if (isset($postParams["upload"])) {
            $error = false;
            $msg = "";
            if (!isset($postParams["title"]) || empty(trim($postParams["title"]))) {
                $msg = "Title is empty";
                $error = true;
            } elseif (!isset($postParams["description"]) || empty(trim($postParams["description"]))) {
                $msg = "Description is empty";
                $error = true;
            } elseif (!isset($postParams["category_id"]) || empty($postParams["category_id"])) {
                $msg = "Category is empty";
                $error = true;
            } elseif (!isset($postParams["owner_id"]) || empty($postParams["owner_id"])) {
                throw new InvalidArgumentException("Invalid arguments.");
            } elseif ($postParams["owner_id"]) {
                throw new AuthorizationException("Unauthorized user.");
            } elseif (!isset($_FILES["video"]["tmp_name"])) {
                $msg = "Video not uploaded";
                $error = true;
            }
            $videoDao = new VideoDAO();
            $categoryDao = new CategoryDAO();
            if ($error) {
                $categories = $categoryDao->findAll();

                include_once "view/upload.php";

                echo $msg;
            } else {

                $categoryExists = $categoryDao->find($postParams['category_id']);
                if (empty($categoryExists)) {
                    throw new InvalidArgumentException("Invalid category.");
                }
                $video = new Video();
                $video->setTitle($postParams["title"]);
                $video->setDescription($postParams["description"]);
                $video->setDateUploaded(date("Y-m-d H:i:s"));
                $video->setOwnerId($postParams["owner_id"]);
                $video->setCategoryId($postParams["category_id"]);
                $video->setDuration(0);
                $video->setVideoUrl(uploadVideo("video", $_SESSION["logged_user"]["username"]));
                $video->setThumbnailUrl(uploadImage("thumbnail", $_SESSION["logged_user"]["username"]));
                $params = [
                    'title'         => $video->getTitle(),
                    'description'   => $video->getDescription(),
                    'date_uploaded' => $video->getDateUploaded(),
                    'owner_id'      => $video->getOwnerId(),
                    'category_id'   => $video->getCategoryId(),
                    'video_url'     => $video->getVideoUrl(),
                    'duration'      => $video->getDuration(),
                    'thumbnail_url' => $video->getThumbnailUrl()
                ];
                $video_id = $videoDao->insert($params);
                $video->setId($video_id);

                include_once "view/main.php";

                echo "Upload successfull.";
            }
        } else {
            throw new InvalidArgumentException("Invalid arguments.");
        }
    }

    /**
     * @param int | null $id
     *
     * @return void
     *
     * @throws AuthorizationException
     * @throws InvalidArgumentException
     */
    public function loadEdit($id=null)
    {
        $getParams = $this->request->getGetParams();
        if (isset($getParams['id'])) {
            $id = $getParams['id'];
        }
        if (empty($id)) {
            throw new InvalidArgumentException("Invalid arguments.");
        }
        $categoryDao = new CategoryDAO();
        $videoDao = new VideoDAO();
        $video = $videoDao->getById($id);
        if (empty($video)) {
            throw new InvalidArgumentException("Invalid video.");
        }
        if ($video["owner_id"] != $_SESSION["logged_user"]["id"]) {
            throw new AuthorizationException("Unauthorized user.");
        }
        $categories = $categoryDao->findAll();

        include_once "view/editVideo.php";
    }

    /**
     * @return void
     *
     * @throws AuthorizationException
     * @throws InvalidArgumentException
     * @throws InvalidFileException
     */
    public function edit()
    {
        $postParams = $this->request->getPostParams();
        if (isset($postParams['edit'])) {
            $error = false;
            $msg = "";
            if (!isset($postParams["id"]) || empty($postParams["id"])) {
                throw new InvalidArgumentException("Invalid arguments.");
            } elseif (!isset($postParams["title"]) || empty(trim($postParams["title"]))) {
                $msg = "Title is empty";
                $error = true;
            } elseif (!isset($postParams["description"]) || empty(trim($postParams["description"]))) {
                $msg = "Description is empty";
                $error = true;
            } elseif (!isset($postParams["category_id"]) || empty($postParams["category_id"])) {
                $msg = "Category is empty";
                $error = true;
            } elseif (!isset($postParams["owner_id"]) || empty($postParams["owner_id"])) {
                throw new InvalidArgumentException("Invalid arguments.");
            } elseif ($postParams["owner_id"] != $_SESSION["logged_user"]["id"]) {
                throw new AuthorizationException("Unauthorized user.");
            }
            $videoDao = new VideoDAO();
            $categoryDao = new CategoryDAO();
            if ($error) {
                $video = $videoDao->getById($postParams["id"]);
                $categories = $categoryDao->findAll();

                include_once "view/editVideo.php";

                echo $msg;
            }
            if (!$error) {
                $videoDao = new VideoDAO();
                $categoryExists = $categoryDao->find($postParams["category_id"]);
                if (!$categoryExists) {
                    throw new InvalidArgumentException("Invalid category.");
                }
                $getvideo = $videoDao->getById($postParams["id"]);
                if (empty($getvideo)) {
                    throw new InvalidArgumentException("Invalid video.");
                }
                $video = new Video();
                $video->setId($postParams["id"]);
                $video->setTitle($postParams["title"]);
                $video->setDescription($postParams["description"]);
                $video->setCategoryId($postParams["category_id"]);
                if (isset($_FILES["thumbnail"])) {
                    $video->setThumbnailUrl(uploadImage("thumbnail", $_SESSION["logged_user"]["username"]));
                }
                if (!($video->getThumbnailUrl())) {
                    $video->setThumbnailUrl($postParams["thumbnail_url"]);
                }
                $params = [
                    'title'         => $video->getTitle(),
                    'description'   => $video->getDescription(),
                    'category_id'   => $video->getCategoryId(),
                    'thumbnail_url' => $video->getThumbnailUrl()
                ];
                $conditions = [
                    'id' => $video->getId()
                ];
                $videoDao->update($params, $conditions);

                include_once "view/main.php";

                echo "Edit successfull.";
            }
        } else {
            throw new InvalidArgumentException("Invalid arguments.");
        }
    }

    /**
     * @return void
     *
     * @throws AuthorizationException
     * @throws InvalidArgumentException
     */
    public function delete()
    {
        $getParams = $this->request->getGetParams();
        if (isset($getParams['id'])) {
            $id = $getParams['id'];
        }
        $ownerId = $_SESSION["logged_user"]["id"];
        if (empty($id)) {
            throw new InvalidArgumentException("Invalid arguments.");
        }
        $videoDao = new VideoDAO();
        $video = $videoDao->getById($id);
        if (empty($video)) {
            throw new InvalidArgumentException("Invalid video.");
        }
        if ($video["owner_id"] != $_SESSION["logged_user"]["id"]) {
            throw new AuthorizationException("Unauthorized user.");
        }
        $params = [
            'id' => $id,
            'owner_id' => $ownerId
        ];
        $videoDao->delete($params);

        include_once "view/main.php";

        echo "Delete successful.";
    }

    /**
     * @return void
     */
    public function getByOwnerId()
    {
        $ownerId = $_SESSION["logged_user"]["id"];
        if (empty($ownerId)) {

            include_once "view/main.php";

            echo "<h3>Login to like videos!</h3>";
        } else {
            $orderby = null;
            if (isset($_GET["orderby"])) {
                switch ($_GET["orderby"]) {
                    case "date":
                        $orderby = "ORDER BY date_uploaded";
                        break;
                    case "likes":
                        $orderby = "ORDER BY likes";
                        break;
                }
                if (isset($_GET["desc"]) && $orderby) {
                    $orderby .= " DESC";
                }
            }
            $videoDao = new VideoDAO();
            $videos = $videoDao->getByOwnerId($ownerId, $orderby);
            $action = "getByOwnerId";
            $orderby = true;

            include_once "view/main.php";
        }
    }

    /**
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public function getById()
    {
        $getParams = $this->request->getGetParams();
        if (isset($getParams['id'])) {
            $id = $getParams['id'];
        }

        if (empty($id)) {
            throw new InvalidArgumentException("Invalid arguments.");
        }
        $videoDao = new VideoDAO();
        $commentDao = new UsersReactCommentsDAO();
        $video = $videoDao->getById($id);
        if (empty($video)) {
            throw new InvalidArgumentException("Invalid video.");
        }
        $videoDao->updateViews($id);
        $video["likes"] = $videoDao->getReactions($id, 1);
        $video["dislikes"] = $videoDao->getReactions($id, 0);
        $comments = $commentDao->getComments($id);
        $userDao = new UserDAO();
        $usersReactVideos = new UsersReactVideosDAO();
        $usersFollowUsersDao = new UsersFollowUsersDAO();
        if (isset($_SESSION['logged_user'])) {
            $userId = $_SESSION["logged_user"]["id"];
            $userDao->addToHistory($id, $userId, date("Y-m-d H:i:s"));
        }
        $params = [
            'follower_id' => null,
            'followed_id' => $video['owner_id']
        ];
        $video['isFollowed'] = $usersFollowUsersDao->findBy($params, true);
        $params = [
            'user_id'  => null,
            'video_id' => $id
        ];
        $video['isReacting'] = $usersReactVideos->findBy($params, true);

        include_once "view/video.php";
    }

    /**
     * @return void
     */
    public function getAll() {
        $orderBy = null;
        if (isset($_GET["orderby"])) {
            switch ($_GET["orderby"]) {
                case "date": $orderBy = "ORDER BY date_uploaded";
                break;
                case "likes": $orderBy = "ORDER BY likes";
                break;
            }
            if (isset($_GET["desc"]) && $orderBy) {
                $orderBy .= " DESC";
            }
        }
        $videoDao = new VideoDAO();
        $videos = $videoDao->getAll($orderBy);
        $action = "getAll";
        $orderBy = true;

        include_once "view/main.php";
    }

    /**
     * @return void
     */
    public function getTrending()
    {
        $videoDao = new VideoDAO();
        $videos = $videoDao->getMostWatched();

        include_once "view/main.php";
    }

    /**
     * @return void
     */
    public function getHistory()
    {
        $userId = $_SESSION["logged_user"]["id"];
        $orderBy = null;
        if (isset($_GET["orderby"])) {
            switch ($_GET["orderby"]) {
                case "date":
                        $orderBy = "ORDER BY date_uploaded";
                        break;
                case "likes":
                        $orderBy = "ORDER BY likes";
                        break;
                }
                if (isset($_GET["desc"]) && $orderBy) {
                    $orderBy .= " DESC";
                }
            }
            $videoDao = new VideoDAO();
            $videos = $videoDao->getHistory($userId, $orderBy);

            include_once "view/main.php";
        $action = "getHistory";
        $orderBy = true;
    }

    /**
     * @return void
     */
    public function getWatchLater()
    {
        $userId = $_SESSION["logged_user"]["id"];
        $videoDao = new VideoDAO();
        $videos = $videoDao->getWatchLater($userId);

        include_once "view/main.php";
        $action = "getWatchLater";
    }

    /**
     * @return void
     */
    public function getLikedVideos()
    {
        $userId = $_SESSION["logged_user"]["id"];
        $orderBy = null;
        if (isset($_GET["orderby"])) {
            switch ($_GET["orderby"]) {
                case "date":
                    $orderBy = "ORDER BY date_uploaded";
                    break;
                case "likes":
                    $orderBy = "ORDER BY likes";
                    break;
                }
                if (isset($_GET["desc"]) && $orderBy) {
                    $orderBy .= " DESC";
                }
            }
            $videoDao = new VideoDAO();
            $videos = $videoDao->getLikedVideos($userId, $orderBy);

            include_once "view/main.php";
        $action = "getLikedVideos";
        $orderBy = true;
    }
}