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
                $dao = VideoDAO::getInstance();
                $categories = $dao->getCategories();
                include_once "view/upload.php";
                echo $msg;
            } else {
                $dao = VideoDAO::getInstance();
                $categoryExists = $dao->getCategoryById($postParams["category_id"]);
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
                $dao->add($video);
                include_once "view/main.php";
                echo "Upload successfull.";
            }
        } else {
            throw new InvalidArgumentException("Invalid arguments.");
        }
    }

    public function loadEdit($id=null)
    {
        $getParams = $this->request->getGetParams();
        if (isset($getParams['id'])) {
            $id = $getParams['id'];
        }
        if (empty($id)) {
            throw new InvalidArgumentException("Invalid arguments.");
        }
        $dao = VideoDAO::getInstance();
        $video = $dao->getById($id);
        if (empty($video)) {
            throw new InvalidArgumentException("Invalid video.");
        }
        if ($video["owner_id"] != $_SESSION["logged_user"]["id"]) {
            throw new AuthorizationException("Unauthorized user.");
        }
        $categories = $dao->getCategories();
        include_once "view/editVideo.php";
    }

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
                $dao = VideoDAO::getInstance();
                $video = $dao->getById($postParams["id"]);
                $categories = $dao->getCategories();
                include_once "view/editVideo.php";
                echo $msg;
            }
            if (!$error) {
                $dao = VideoDAO::getInstance();
                $categoryExists = $dao->getCategoryById($postParams["category_id"]);
                if (!$categoryExists) {
                    throw new InvalidArgumentException("Invalid category.");
                }
                $getvideo = $dao->getById($postParams["id"]);
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
                $dao->edit($video);
                include_once "view/main.php";
                echo "Edit successfull.";
            }
        } else {
            throw new InvalidArgumentException("Invalid arguments.");
        }
    }

    public function delete()
    {
        $getParams = $this->request->getGetParams();
        if (isset($getParams['id'])) {
            $id = $getParams['id'];
        }
        $owner_id = $_SESSION["logged_user"]["id"];
        if (empty($id)) {
            throw new InvalidArgumentException("Invalid arguments.");
        }
        $dao = VideoDAO::getInstance();
        $video = $dao->getById($id);
        if (empty($video)) {
            throw new InvalidArgumentException("Invalid video.");
        }
        if ($video["owner_id"] != $_SESSION["logged_user"]["id"]) {
            throw new AuthorizationException("Unauthorized user.");
        }
        $dao->delete($id, $owner_id);
        include_once "view/main.php";
        echo "Delete successful.";
    }

    public function getByOwnerId()
    {
        $owner_id = $_SESSION["logged_user"]["id"];
        if (empty($owner_id)) {
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
            $dao = VideoDAO::getInstance();
            $videos = $dao->getByOwnerId($owner_id, $orderby);
            $action = "getByOwnerId";
            $orderby = true;
            include_once "view/main.php";
        }
    }

    public function getById()
    {
        $getParams = $this->request->getGetParams();
        if (isset($getParams['id'])) {
            $id = $getParams['id'];
        }

        if (empty($id)) {
            throw new InvalidArgumentException("Invalid arguments.");
        }
        $videodao = VideoDAO::getInstance();
        $video = $videodao->getById($id);
        if (empty($video)) {
            throw new InvalidArgumentException("Invalid video.");
        }
        $videodao->updateViews($id);
        $video["likes"] = $videodao->getReactions($id, 1);
        $video["dislikes"] = $videodao->getReactions($id, 0);
        $comments = $videodao->getComments($id);
        $userdao = UserDAO::getInstance();
        $user_id = $_SESSION["logged_user"]["id"];
        $userdao->addToHistory($id, $user_id, date("Y-m-d H:i:s"));
        $video["isFollowed"] = $userdao->isFollowing($user_id, $video["owner_id"]);
        $video["isReacting"] = $userdao->isReacting($user_id, $id);
        include_once "view/video.php";
    }

    public function getAll() {
        $orderby = null;
        if (isset($_GET["orderby"])) {
            switch ($_GET["orderby"]) {
                case "date": $orderby = "ORDER BY date_uploaded";
                break;
                case "likes": $orderby = "ORDER BY likes";
                break;
            }
            if (isset($_GET["desc"]) && $orderby) {
                $orderby .= " DESC";
            }
        }
        $dao = VideoDAO::getInstance();
        $videos = $dao->getAll($orderby);
        $action = "getAll";
        $orderby = true;
        include_once "view/main.php";
    }

    public function getTrending()
    {
        $dao = VideoDAO::getInstance();
        $videos = $dao->getMostWatched();
        include_once "view/main.php";
    }

    public function getHistory()
    {
        $user_id = $_SESSION["logged_user"]["id"];
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
            $dao = VideoDAO::getInstance();
            $videos = $dao->getHistory($user_id, $orderby);
            include_once "view/main.php";
        $action = "getHistory";
        $orderby = true;
    }

    public function getWatchLater()
    {
        $user_id = $_SESSION["logged_user"]["id"];
        $dao = PlaylistDAO::getInstance();
        $videos = $dao->getWatchLater($user_id);
        include_once "view/main.php";
        $action = "getWatchLater";
    }

    public function getLikedVideos()
    {
        if (isset($_SESSION["logged_user"]["id"])) {
            $user_id = $_SESSION["logged_user"]["id"];
            $orderby = null;
            if (isset($_GET["orderby"])) {
                switch ($_GET["orderby"]) {
                    case "date": $orderby = "ORDER BY date_uploaded";
                        break;
                    case "likes": $orderby = "ORDER BY likes";
                        break;
                }
                if (isset($_GET["desc"]) && $orderby) {
                    $orderby .= " DESC";
                }
            }
            $dao = VideoDAO::getInstance();
            $videos = $dao->getLikedVideos($user_id, $orderby);
            include_once "view/main.php";
        } else {
            include_once "view/main.php";
            echo "<h3>Login to like videos!</h3>";
        }
        $action = "getLikedVideos";
        $orderby = true;
    }
}