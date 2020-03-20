<?php

namespace controller;

include_once "fileHandler.php";

use exceptions\AuthorizationException;
use exceptions\InvalidArgumentException;
use model\PlaylistDAO;
use model\UserDAO;
use model\Video;
use model\VideoDAO;

class VideoController extends AbstractController
{
    /**
     * @throws AuthorizationException
     * @throws InvalidArgumentException
     * @throws \exceptions\InvalidFileException
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
            if ($error) {
                $videoDao = VideoDAO::getInstance();
                $categories = $videoDao->getCategories();

                include_once "view/upload.php";

                echo $msg;
            } else {
                $videoDao = VideoDAO::getInstance();
                $categoryExists = $videoDao->getCategoryById($postParams["category_id"]);
                if (!$categoryExists) {
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
                $videoDao->add($video);

                include_once "view/main.php";

                echo "Upload successfull.";
            }
        } else {
            throw new InvalidArgumentException("Invalid arguments.");
        }
    }

    /**
     * @param null $id
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
        $videoDao = VideoDAO::getInstance();
        $video = $videoDao->getById($id);
        if (empty($video)) {
            throw new InvalidArgumentException("Invalid video.");
        }
        if ($video["owner_id"] != $_SESSION["logged_user"]["id"]) {
            throw new AuthorizationException("Unauthorized user.");
        }
        $categories = $videoDao->getCategories();

        include_once "view/editVideo.php";
    }

    /**
     * @throws AuthorizationException
     * @throws InvalidArgumentException
     * @throws \exceptions\InvalidFileException
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
            if ($error) {
                $videoDao = VideoDAO::getInstance();
                $video = $videoDao->getById($postParams["id"]);
                $categories = $videoDao->getCategories();

                include_once "view/editVideo.php";

                echo $msg;
            }
            if (!$error) {
                $videoDao = VideoDAO::getInstance();
                $categoryExists = $videoDao->getCategoryById($postParams["category_id"]);
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
                $videoDao->edit($video);

                include_once "view/main.php";

                echo "Edit successfull.";
            }
        } else {
            throw new InvalidArgumentException("Invalid arguments.");
        }
    }

    /**
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
        $videoDao = VideoDAO::getInstance();
        $video = $videoDao->getById($id);
        if (empty($video)) {
            throw new InvalidArgumentException("Invalid video.");
        }
        if ($video["owner_id"] != $_SESSION["logged_user"]["id"]) {
            throw new AuthorizationException("Unauthorized user.");
        }
        $videoDao->delete($id, $ownerId);

        include_once "view/main.php";

        echo "Delete successful.";
    }

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
            $videoDao = VideoDAO::getInstance();
            $videos = $videoDao->getByOwnerId($ownerId, $orderby);
            $action = "getByOwnerId";
            $orderby = true;

            include_once "view/main.php";
        }
    }

    /**
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
        $videoDao = VideoDAO::getInstance();
        $video = $videoDao->getById($id);
        if (empty($video)) {
            throw new InvalidArgumentException("Invalid video.");
        }
        $videoDao->updateViews($id);
        $video["likes"] = $videoDao->getReactions($id, 1);
        $video["dislikes"] = $videoDao->getReactions($id, 0);
        $comments = $videoDao->getComments($id);
        $userDao = UserDAO::getInstance();
        if (isset($_SESSION['logged_user'])) {
            $userId = $_SESSION["logged_user"]["id"];
            $userDao->addToHistory($id, $userId, date("Y-m-d H:i:s"));
        }
        $video["isFollowed"] = $userDao->isFollowing(null,$video["owner_id"]);
        $video["isReacting"] = $userDao->isReacting(null, $id);

        include_once "view/video.php";
    }

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
        $dao = VideoDAO::getInstance();
        $videos = $dao->getAll($orderBy);
        $action = "getAll";
        $orderBy = true;

        include_once "view/main.php";
    }

    public function getTrending()
    {
        $videoDao = VideoDAO::getInstance();
        $videos = $videoDao->getMostWatched();

        include_once "view/main.php";
    }

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
            $videoDao = VideoDAO::getInstance();
            $videos = $videoDao->getHistory($userId, $orderBy);

            include_once "view/main.php";
        $action = "getHistory";
        $orderBy = true;
    }

    public function getWatchLater()
    {
        $userId = $_SESSION["logged_user"]["id"];
        $videoDao = VideoDAO::getInstance();
        $videos = $videoDao->getWatchLater($userId);

        include_once "view/main.php";
        $action = "getWatchLater";
    }

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
            $videoDao = VideoDAO::getInstance();
            $videos = $videoDao->getLikedVideos($userId, $orderBy);

            include_once "view/main.php";
        $action = "getLikedVideos";
        $orderBy = true;
    }
}