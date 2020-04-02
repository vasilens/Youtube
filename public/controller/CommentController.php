<?php

namespace controller;

use exceptions\InvalidArgumentException;
use exceptions\AuthorizationException;
use model\Comment;
use model\CommentDAO;
use model\User;
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
        $commentId = $this->addComment($comment);
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
        $this->deleteComment($commentId, $ownerId);
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

        return $this->isReactingToComment($userId, $commentId);
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
            $this->reactComment($userId, $commentId, $status);
        } elseif ($isReacting == $status) { //if liking liked or disliking disliked video
            $this->unreactComment($userId, $commentId);
        } elseif ($isReacting != $status) { //if liking disliked or disliking liked video
            $this->unreactComment($userId, $commentId);
            $this->reactComment($userId, $commentId, 1 - $isReacting);
        }
        $arr = [];
        $arr["stat"] = $this->isReactingToComment($userId, $commentId);
        $arr["likes"] = $this->getCommentReactions($commentId, 1);
        $arr["dislikes"] = $this->getCommentReactions($commentId, 0);

        echo json_encode($arr);
    }

    /**
     * @param Comment $comment
     *
     * @return int
     */
    public function addComment(Comment $comment): int
    {
        $dao = new CommentDAO();
        $params = [
            'video_id' => $comment->getVideoId(),
            'owner_id' => $comment->getOwnerId(),
            'content'  => $comment->getContent(),
            'date'     => $comment->getDate()
        ];

        return $dao->insert($params);
    }

    /**
     * @param int $comment_id
     * @param int $owner_id
     *
     * @return int
     */
    public function deleteComment(int $comment_id, int $owner_id): int
    {
        $dao = new CommentDAO();
        $params = [
            'id'       => $comment_id,
            'owner_id' => $owner_id
        ];

        return $dao->delete($params);
    }

    /**
     * @param int $commentId
     * @param int $status
     *
     * @return int
     */
    public function getCommentReactions(int $commentId, int $status): int
    {
        $dao = new UsersReactCommentsDAO();
        $params = [
            'comment_id' => $commentId,
            'status'     => $status
        ];
        $row = $this->findBy($params);
        if ($row) {

            return $row["count"];
        }

        return 0;
    }

    /**
     * @param int $userId
     * @param int $commentId
     */
    public function unreactComment(int $userId, int $commentId)
    {
        $dao = new UsersReactCommentsDAO();
        $params = [
            'user_id'    => $userId,
            'comment_id' => $commentId
        ];
        $dao->delete($params);
    }

    /**
     * @param int $userId
     * @param int $commentId
     * @param int $status
     */
    public function reactComment(int $userId, int $commentId, int $status)
    {
        $dao = new UsersReactCommentsDAO();
        $params = [
            'user_id'    => $userId,
            'comment_id' => $commentId,
            'status'     => $status
        ];
        $dao->insert($params);
    }

    /**
     * @param int $userId
     * @param int $commentId
     *
     * @return int
     */
    public function isReactingToComment(int $userId, int $commentId): int
    {
        $dao = new UsersReactCommentsDAO();
        $params = [
            'user_id'    => $userId,
            'comment_id' => $commentId
        ];
        $row = $dao->findBy($params);
        if ($row) {

            return $row["status"];
        }

        return -1;
    }
}