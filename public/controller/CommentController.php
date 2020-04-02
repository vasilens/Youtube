<?php

namespace controller;

use exceptions\InvalidArgumentException;
use exceptions\AuthorizationException;
use model\Comment;
use model\CommentDAO;
use model\UsersReactCommentsDAO;
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
        $usersReactCommentsDao = new UsersReactCommentsDAO();
        $commentsDao = new CommentDAO();
        $videoDao = new VideoDAO();
        $video = $videoDao->getById($postParams["video_id"]);
        if (empty($video)) {
            throw new InvalidArgumentException("Invalid video.");
        }
        $comment = new Comment();
        $comment->setContent($postParams["content"]);
        $comment->setVideoId($postParams["video_id"]);
        $comment->setOwnerId($postParams["owner_id"]);
        $comment->setDate(date("Y-m-d H:i:s"));
        $commentId = $commentsDao->addComment($comment);
        $comment = $usersReactCommentsDao->getCommentById($commentId);

        echo json_encode($comment);
    }

    public function delete()
    {
        $getParams = $this->request->getGetParams();
        if (isset($getParams["id"])) {
            $commentId = $getParams["id"];
            $ownerId = $_SESSION["logged_user"]["id"];
        }
        if (empty($commentId) || empty($ownerId)) {
            throw new InvalidArgumentException("Invalid arguments.");
        }
        $usersReactCommentsDao = new UsersReactCommentsDAO();
        $comment = $usersReactCommentsDao->getCommentById($commentId);
        if (empty($comment)) {
            throw new InvalidArgumentException("Invalid comment.");
        }
        $commentsDao = new CommentDAO();
        $commentsDao->deleteComment($commentId, $ownerId);
    }

    public function isReactingComment()
    {
        $getParams = $this->request->getGetParams();
        if (isset($getParams["id"])) {
            $commentId = $getParams["id"];
            $userId = $_SESSION["logged_user"]["id"];
        }
        if (empty($userId) || empty($commentId)) {
            throw new InvalidArgumentException("Invalid arguments.");
        }
        $commentDao = new UsersReactCommentsDAO();

        return $commentDao->isReactingComment($userId, $commentId);
    }

    public function react()
    {
        $getParams = $this->request->getGetParams();
        if (isset($getParams["id"]) && isset($getParams["status"])) {
            $commentId = $getParams["id"];
            $status = $getParams["status"];
        }
        $userId = $_SESSION["logged_user"]["id"];
        if (empty($commentId) || empty($userId)) {
            throw new InvalidArgumentException("Invalid arguments.");
        }
        if ($status != 0 && $status != 1) {
            throw new InvalidArgumentException("Invalid arguments.");
        }
        $commentDao = new UsersReactCommentsDAO();
        $comment = $commentDao->getCommentById($commentId);
        if (empty($comment)) {
            throw new InvalidArgumentException("Invalid comment.");
        }
        $isReacting = $this->isReactingComment();
        if ($isReacting == -1) {//if there has been no reaction
            $commentDao->reactComment($userId, $commentId, $status);
        } elseif ($isReacting == $status) { //if liking liked or disliking disliked video
            $commentDao->unreactComment($userId, $commentId);
        } elseif ($isReacting != $status) { //if liking disliked or disliking liked video
            $commentDao->unreactComment($userId, $commentId);
            $commentDao->reactComment($userId, $commentId, 1 - $isReacting);
        }
        $arr = [];
        $arr["stat"] = $this->isReactingComment();
        $arr["likes"] = $commentDao->getCommentReactions($commentId, 1);
        $arr["dislikes"] = $commentDao->getCommentReactions($commentId, 0);

        echo json_encode($arr);
    }
}