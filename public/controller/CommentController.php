<?php
namespace controller;
use exceptions\InvalidArgumentException;
use exceptions\AuthorizationException;
use model\Comment;
use model\UserDAO;
use model\VideoDAO;

class CommentController
{
    public function add(){
        if (!isset($_SESSION["logged_user"]["id"])){
            throw new AuthorizationException("Log in to comment.");
        }
        if (empty($_POST["video_id"]) || empty($_POST["owner_id"])) {
            throw new InvalidArgumentException("Invalid arguments.");
        }
        if ($_POST["owner_id"] != $_SESSION["logged_user"]["id"]){
            throw new AuthorizationException("Unauthorized user.");
        }
        if (empty($_POST["content"])){
            throw new InvalidArgumentException("Comment is empty.");
        }
        $dao = VideoDAO::getInstance();
        $video = $dao->getById($_POST["video_id"]);
        if (empty($video)){
            throw new InvalidArgumentException("Invalid video.");
        }
        $comment = new Comment();
        $comment->setContent($_POST["content"]);
        $comment->setVideoId($_POST["video_id"]);
        $comment->setOwnerId($_POST["owner_id"]);
        $comment->setDate(date("Y-m-d H:i:s"));
        $comment_id = $dao->addComment($comment);
        $comment = $dao->getCommentById($comment_id);
        echo json_encode($comment);
    }

    public function delete(){
        if (isset($_GET["id"])){
            $comment_id = $_GET["id"];
            $owner_id = $_SESSION["logged_user"]["id"];
        }
        if (empty($comment_id) || empty($owner_id)){
            throw new InvalidArgumentException("Invalid arguments.");
        }
        if ($owner_id != $_SESSION["logged_user"]["id"]){
            throw new AuthorizationException("Unauthorized user.");
        }
        $dao = VideoDAO::getInstance();
        $comment = $dao->getCommentById($comment_id);
        if (empty($comment)){
            throw new InvalidArgumentException("Invalid comment.");
        }
        $dao->deleteComment($comment_id, $owner_id);
    }

    public function isReactingComment($user_id=null, $comment_id=null){
        if (isset($_GET["id"])){
            $comment_id = $_GET["id"];
            $user_id = $_SESSION["logged_user"]["id"];
        }
        if (empty($user_id) || empty($comment_id)){
            throw new InvalidArgumentException("Invalid arguments.");
        }
        $dao = UserDAO::getInstance();
        return $dao->isReactingComment($user_id, $comment_id);
    }

    public function react(){
        if (isset($_GET["id"]) && isset($_GET["status"])){
            $comment_id = $_GET["id"];
            $status = $_GET["status"];
        }
        $user_id = $_SESSION["logged_user"]["id"];
        if (empty($comment_id) || empty($user_id)){
            throw new InvalidArgumentException("Invalid arguments.");
        }
        if ($status != 0 && $status != 1){
            throw new InvalidArgumentException("Invalid arguments.");
        }
        $videodao = VideoDAO::getInstance();
        $comment = $videodao->getCommentById($comment_id);
        if (empty($comment)){
            throw new InvalidArgumentException("Invalid comment.");
        }
        $isReacting = $this->isReactingComment($user_id, $comment_id);
        $userdao = UserDAO::getInstance();
        if ($isReacting == -1) {//if there has been no reaction
            $userdao->reactComment($user_id, $comment_id, $status);
        } elseif ($isReacting == $status) { //if liking liked or disliking disliked video
            $userdao->unreactComment($user_id, $comment_id);
        } elseif ($isReacting != $status) { //if liking disliked or disliking liked video
            $userdao->unreactComment($user_id, $comment_id);
            $userdao->reactComment($user_id, $comment_id, 1 - $isReacting);
        }
        $arr = [];
        $arr["stat"] = $this->isReactingComment();
        $arr["likes"] = $userdao->getCommentReactions($comment_id, 1);
        $arr["dislikes"] = $userdao->getCommentReactions($comment_id, 0);
        echo json_encode($arr);
    }
}