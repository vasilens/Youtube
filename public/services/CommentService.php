<?php

namespace services;

use exceptions\InvalidArgumentException;
use model\Comment;
use model\CommentDAO;
use model\UsersReactCommentsDAO;

class CommentService extends AbstractService
{
    /**
     * @return void
     */
    protected function setDao()
    {
        $this->dao = new CommentDAO();
    }

    /**
     * @param array $postParams
     *
     * @return void
     */
    public function add(array $postParams)
    {
        $usersReactCommentsDao = new UsersReactCommentsDAO();
        $comment = new Comment();
        $comment->setContent($postParams["content"]);
        $comment->setVideoId($postParams["video_id"]);
        $comment->setOwnerId($postParams["owner_id"]);
        $comment->setDate(date("Y-m-d H:i:s"));
        $params = [
            'video_id' => $comment->getVideoId(),
            'owner_id' => $comment->getOwnerId(),
            'content'  => $comment->getContent(),
            'date'     => $comment->getDate()
        ];

        $commentId = $this->dao->insert($params);
        $comment = $usersReactCommentsDao->getCommentById($commentId);

        echo json_encode($comment);
    }

    /**
     * @param array $getParams
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public function delete(array $getParams)
    {
        $usersReactCommentsDao = new UsersReactCommentsDAO();
        $comment = $usersReactCommentsDao->getCommentById($getParams['id']);
        if (empty($comment)) {
            throw new InvalidArgumentException("No comment found");
        }
        $params = [
            'id'       => $getParams['id'],
            'owner_id' => $_SESSION["logged_user"]["id"]
        ];
        $this->dao->delete($params);

    }

    /**
     * @param array $getParams
     *
     * @return int
     */
    public function isReactingComment(array $getParams)
    {

        $commentDao = new UsersReactCommentsDAO();
        $params = [
            'user_id'    => $_SESSION["logged_user"]["id"],
            'comment_id' => $getParams['id']
        ];
        $row = $commentDao->findBy($params);
        if ($row) {
            return $row[0]["status"];
        }

        return -1;
    }

    /**
     * @param int $isReacting
     * @param array $getParams
     *
     * @return void
     */
    public function react(int $isReacting, array $getParams)
    {
        $commentDao = new UsersReactCommentsDAO();
        $userId = $_SESSION["logged_user"]["id"];
        $commentId = $getParams["id"];
        $status = $getParams["status"];
        if ($isReacting == -1) {//if there has been no reaction
            $params = [
                'user_id'    => $userId,
                'comment_id' => $commentId,
                'status'     => $status
            ];
            $commentDao->insert($params);
        } elseif ($isReacting == $status) { //if liking liked or disliking disliked video
            $params = [
                'user_id'    => $userId,
                'comment_id' => $commentId
            ];
            $commentDao->delete($params);
        } elseif ($isReacting != $status) { //if liking disliked or disliking liked video
            $params = [
                'user_id'    => $userId,
                'comment_id' => $commentId
            ];
            $commentDao->delete($params);
            $params = [
                'user_id'    => $userId,
                'comment_id' => $commentId,
                'status'     => $status
            ];
            $commentDao->insert($params);
        }
        $arr = [];
        $arr['stat'] = $isReacting;
        $arr["likes"] = $commentDao->getCommentReactions($commentId, 1);
        $arr["dislikes"] = $commentDao->getCommentReactions($commentId, 0);

        echo json_encode($arr);
    }

}