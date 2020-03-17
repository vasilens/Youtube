<?php
namespace controller;
use exceptions\InvalidArgumentException;
use exceptions\AuthorizationException;
use model\Comment;
use model\UserDAO;
use model\VideoDAO;

class CommentController extends AbstractController
{
    public function add()
    {
        $postParams = $this->request->getPostParams();
        if (!isset($_SESSION["logged_user"]["id"])) {
            throw new AuthorizationException("Log in to comment.");
        }
        if (empty($postParams["video_id"]) || empty($postParams["owner_id"])) {
            throw new InvalidArgumentException("Invalid arguments.");
        }
        if ($postParams["owner_id"] != $_SESSION["logged_user"]["id"]) {
            throw new AuthorizationException("Unauthorized user.");
        }
        if (empty($postParams["content"])) {
            throw new InvalidArgumentException("Comment is empty.");
        }
        $dao = VideoDAO::getInstance();
        $video = $dao->getById($postParams["video_id"]);
        if (empty($video)) {
            throw new InvalidArgumentException("Invalid video.");
        }
        $comment = new Comment();
        $comment->setContent($postParams["content"]);
        $comment->setVideoId($postParams["video_id"]);
        $comment->setOwnerId($postParams["owner_id"]);
        $comment->setDate(date("Y-m-d H:i:s"));
        $comment_id = $dao->addComment($comment);
        $comment = $dao->getCommentById($comment_id);
        echo json_encode($comment);
    }

    public function delete()
    {
        $getParams = $this->request->getGetParams();
        if (isset($getParams["id"])) {
            $comment_id = $getParams["id"];
            $owner_id = $_SESSION["logged_user"]["id"];
        }
        if (empty($comment_id) || empty($owner_id)) {
            throw new InvalidArgumentException("Invalid arguments.");
        }
        if ($owner_id != $_SESSION["logged_user"]["id"]) {
            throw new AuthorizationException("Unauthorized user.");
        }
        $dao = VideoDAO::getInstance();
        $comment = $dao->getCommentById($comment_id);
        if (empty($comment)) {
            throw new InvalidArgumentException("Invalid comment.");
        }
        $dao->deleteComment($comment_id, $owner_id);
    }

    public function isReactingComment()
    {
        $getParams = $this->request->getGetParams();
        if (isset($getParams["id"])) {
            $comment_id = $getParams["id"];
            $user_id = $_SESSION["logged_user"]["id"];
        }
        if (empty($user_id) || empty($comment_id)) {
            throw new InvalidArgumentException("Invalid arguments.");
        }
        $dao = UserDAO::getInstance();
        return $dao->isReactingComment($user_id, $comment_id);
    }

    public function react()
    {
        $getParams = $this->request->getGetParams();
        if (isset($getParams["id"]) && isset($getParams["status"])) {
            $comment_id = $getParams["id"];
            $status = $getParams["status"];
        }
        $user_id = $_SESSION["logged_user"]["id"];
        if (empty($comment_id) || empty($user_id)) {
            throw new InvalidArgumentException("Invalid arguments.");
        }
        if ($status != 0 && $status != 1) {
            throw new InvalidArgumentException("Invalid arguments.");
        }
        $videodao = VideoDAO::getInstance();
        $comment = $videodao->getCommentById($comment_id);
        if (empty($comment)) {
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