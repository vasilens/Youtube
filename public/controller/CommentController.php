<?php

namespace controller;

use components\router\http\Request;
use exceptions\AuthorizationException;
use exceptions\InvalidArgumentException;
use model\UsersReactCommentsDAO;
use model\VideoDAO;
use services\CommentService;

class CommentController extends AbstractController
{

    /**
     * @var CommentService
     */
    private $commentService;

    /**
     * CommentController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->commentService = new CommentService();

    }

    /**
     * @return void
     *
     * @throws AuthorizationException
     * @throws InvalidArgumentException
     */
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
        $videoDao = new VideoDAO();
        $video = $videoDao->getById($postParams["video_id"]);
        if (empty($video)) {
            throw new InvalidArgumentException("Invalid video.");
        }
        $this->commentService->add($postParams);
    }

    /**
     * @return void
     *
     * @throws InvalidArgumentException
     */
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
        $this->commentService->delete($getParams);
    }

    /**
     * @return int
     *
     * @throws InvalidArgumentException
     */
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

        return $this->commentService->isReactingComment($getParams);
    }

    /**
     * @return void
     *
     * @throws InvalidArgumentException
     */
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
        $this->commentService->react($isReacting, $getParams);
    }
}