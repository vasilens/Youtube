<?php
namespace controller;
include_once "fileHandler.php";

use exceptions\AuthorizationException;
use exceptions\InvalidArgumentException;
use model\PlaylistDAO;
use model\UserDAO;
use model\Video;
use model\VideoDAO;

class VideoController{
    public function upload(){
        if(isset($_POST["upload"])) {
            $error = false;
            $msg = "";
            if (!isset($_POST["title"]) || empty(trim($_POST["title"]))) {
                $msg = "Title is empty";
                $error = true;
            }
            elseif (!isset($_POST["description"]) || empty(trim($_POST["description"]))) {
                $msg = "Description is empty";
                $error = true;
            }
            elseif (!isset($_POST["category_id"]) || empty($_POST["category_id"])) {
                $msg = "Category is empty";
                $error = true;
            }
            elseif (!isset($_POST["owner_id"]) || empty($_POST["owner_id"])) {
                throw new InvalidArgumentException("Invalid arguments.");
            }
            elseif ($_POST["owner_id"] != $_SESSION["logged_user"]["id"]) {
                throw new AuthorizationException("Unauthorized user.");
            }
            elseif (!isset($_FILES["video"]["tmp_name"])) {
                $msg = "Video not uploaded";
                $error = true;
            }
            if ($error) {
                $dao = VideoDAO::getInstance();
                $categories = $dao->getCategories();
                include_once "view/upload.php";
                echo $msg;
            }
            else {
                $dao = VideoDAO::getInstance();
                $categoryExists = $dao->getCategoryById($_POST["category_id"]);
                if (!$categoryExists){
                    throw new InvalidArgumentException("Invalid category.");
                }
                $video = new Video();
                $video->setTitle($_POST["title"]);
                $video->setDescription($_POST["description"]);
                $video->setDateUploaded(date("Y-m-d H:i:s"));
                $video->setOwnerId($_POST["owner_id"]);
                $video->setCategoryId($_POST["category_id"]);
                $video->setDuration(0);
                $video->setVideoUrl(uploadVideo("video", $_SESSION["logged_user"]["username"]));
                $video->setThumbnailUrl(uploadImage("thumbnail", $_SESSION["logged_user"]["username"]));
                $dao->add($video);
                include_once "view/main.php";
                echo "Upload successfull.";
            }
        }
        else {
            throw new InvalidArgumentException("Invalid arguments.");
        }
    }

    public function loadEdit($id=null)
    {
        if (isset($_GET["id"])) {
            $id = $_GET["id"];
        }
        if (empty($id)) {
            throw new InvalidArgumentException("Invalid arguments.");
        }
        $dao = VideoDAO::getInstance();
        $video = $dao->getById($id);
        if (empty($video)){
            throw new InvalidArgumentException("Invalid video.");
        }
        if ($video["owner_id"] != $_SESSION["logged_user"]["id"]){
            throw new AuthorizationException("Unauthorized user.");
        }
        $categories = $dao->getCategories();
        include_once "view/editVideo.php";
    }

    public function edit($id=null){
        if(isset($_POST["edit"])) {
            $error = false;
            $msg = "";
            if (!isset($_POST["id"]) || empty($_POST["id"])) {
                throw new InvalidArgumentException("Invalid arguments.");
            }
            elseif (!isset($_POST["title"]) || empty(trim($_POST["title"]))) {
                $msg = "Title is empty";
                $error = true;
            }
            elseif (!isset($_POST["description"]) || empty(trim($_POST["description"]))) {
                $msg = "Description is empty";
                $error = true;
            }
            elseif (!isset($_POST["category_id"]) || empty($_POST["category_id"])) {
                $msg = "Category is empty";
                $error = true;
            }
            elseif (!isset($_POST["owner_id"]) || empty($_POST["owner_id"])) {
                throw new InvalidArgumentException("Invalid arguments.");
            }
            elseif ($_POST["owner_id"] != $_SESSION["logged_user"]["id"]) {
                throw new AuthorizationException("Unauthorized user.");
            }
            if ($error) {
                $dao = VideoDAO::getInstance();
                $video = $dao->getById($_POST["id"]);
                $categories = $dao->getCategories();
                include_once "view/editVideo.php";
                echo $msg;
            }
            if (!$error) {
                $dao = VideoDAO::getInstance();
                $categoryExists = $dao->getCategoryById($_POST["category_id"]);
                if (!$categoryExists){
                    throw new InvalidArgumentException("Invalid category.");
                }
                $getvideo = $dao->getById($_POST["id"]);
                if (empty($getvideo)){
                    throw new InvalidArgumentException("Invalid video.");
                }
                $video = new Video();
                $video->setId($_POST["id"]);
                $video->setTitle($_POST["title"]);
                $video->setDescription($_POST["description"]);
                $video->setCategoryId($_POST["category_id"]);
                if (isset($_FILES["thumbnail"])) {
                    $video->setThumbnailUrl(uploadImage("thumbnail", $_SESSION["logged_user"]["username"]));
                }
                if (!($video->getThumbnailUrl())) {
                    $video->setThumbnailUrl($_POST["thumbnail_url"]);
                }
                $dao->edit($video);
                include_once "view/main.php";
                echo "Edit successfull.";
            }
        }
        else {
            throw new InvalidArgumentException("Invalid arguments.");
        }
    }

    public function delete($id=null){
        if (isset($_GET["id"])){
            $id = $_GET["id"];
        }
        $owner_id = $_SESSION["logged_user"]["id"];
        if (empty($id)){
            throw new InvalidArgumentException("Invalid arguments.");
        }
        $dao = VideoDAO::getInstance();
        $video = $dao->getById($id);
        if (empty($video)){
            throw new InvalidArgumentException("Invalid video.");
        }
        if ($video["owner_id"] != $_SESSION["logged_user"]["id"]){
            throw new AuthorizationException("Unauthorized user.");
        }
        $dao->delete($id, $owner_id);
        include_once "view/main.php";
        echo "Delete successful.";
    }

    public function getByOwnerId($owner_id=null){
        if (isset($_GET["owner_id"])){
            $owner_id = $_GET["owner_id"];
        }
        else {
            $owner_id = $_SESSION["logged_user"]["id"];
        }
        if (empty($owner_id)){
            include_once "view/main.php";
            echo "<h3>Login to like videos!</h3>";
        }
        else {
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
    
    public function getById($id=null){
        if (isset ($_GET["id"])){
            $id = $_GET["id"];
        }
        if (empty($id)){
            throw new InvalidArgumentException("Invalid arguments.");
        }
        $videodao = VideoDAO::getInstance();
        $video = $videodao->getById($id);
        if (empty($video)){
            throw new InvalidArgumentException("Invalid video.");
        }
        $videodao->updateViews($id);
        $video["likes"] = $videodao->getReactions($id, 1);
        $video["dislikes"] = $videodao->getReactions($id, 0);
        $comments = $videodao->getComments($id);
        if (isset($_SESSION["logged_user"]["id"])) {
            $userdao = UserDAO::getInstance();
            $user_id = $_SESSION["logged_user"]["id"];
            $userdao->addToHistory($id, $user_id, date("Y-m-d H:i:s"));
            $video["isFollowed"] = $userdao->isFollowing($user_id, $video["owner_id"]);
            $video["isReacting"] = $userdao->isReacting($user_id, $id);
        }
        else {
            $video["isFollowed"] = false;
            $video["isReacting"] = false;
        }
        include_once "view/video.php";
    }

    public function getAll(){
        $orderby = null;
        if (isset($_GET["orderby"])){
            switch ($_GET["orderby"]){
                case "date": $orderby = "ORDER BY date_uploaded";
                break;
                case "likes": $orderby = "ORDER BY likes";
                break;
            }
            if (isset($_GET["desc"]) && $orderby){
                $orderby .= " DESC";
            }
        }
        $dao = VideoDAO::getInstance();
        $videos = $dao->getAll($orderby);
        $action = "getAll";
        $orderby = true;
        include_once "view/main.php";
    }

    public function getTrending(){
        $dao = VideoDAO::getInstance();
        $videos = $dao->getMostWatched();
        include_once "view/main.php";
    }

    public function getHistory() {
        if (isset($_SESSION["logged_user"]["id"])){
            $user_id = $_SESSION["logged_user"]["id"];
            $orderby = null;
            if (isset($_GET["orderby"])){
                switch ($_GET["orderby"]){
                    case "date": $orderby = "ORDER BY date_uploaded";
                        break;
                    case "likes": $orderby = "ORDER BY likes";
                        break;
                }
                if (isset($_GET["desc"]) && $orderby){
                    $orderby .= " DESC";
                }
            }
            $dao = VideoDAO::getInstance();
            $videos = $dao->getHistory($user_id, $orderby);
            include_once "view/main.php";
        }
        else {
            include_once "view/main.php";
            echo "<h3>Login to record history!</h3>";
        }
        $action = "getHistory";
        $orderby = true;
    }

    public function getWatchLater() {
        if (isset($_SESSION["logged_user"]["id"])){
            $user_id = $_SESSION["logged_user"]["id"];
            $dao = PlaylistDAO::getInstance();
            $videos = $dao->getWatchLater($user_id);
            include_once "view/main.php";
        }
        else {
            include_once "view/main.php";
            echo "<h3>Login to save videos for watching later!</h3>";
        }
        $action = "getWatchLater";
    }

    public function getLikedVideos() {
        if (isset($_SESSION["logged_user"]["id"])){
            $user_id = $_SESSION["logged_user"]["id"];
            $orderby = null;
            if (isset($_GET["orderby"])){
                switch ($_GET["orderby"]){
                    case "date": $orderby = "ORDER BY date_uploaded";
                        break;
                    case "likes": $orderby = "ORDER BY likes";
                        break;
                }
                if (isset($_GET["desc"]) && $orderby){
                    $orderby .= " DESC";
                }
            }
            $dao = VideoDAO::getInstance();
            $videos = $dao->getLikedVideos($user_id, $orderby);
            include_once "view/main.php";
        }
        else {
            include_once "view/main.php";
            echo "<h3>Login to like videos!</h3>";
        }
        $action = "getLikedVideos";
        $orderby = true;
    }
}