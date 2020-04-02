<?php

namespace model;

class CommentDAO extends AbstractDAO
{
    /**
     * @param Comment $comment
     *
     * @return int
     */
    public function addComment(Comment $comment): int
    {
        $params = [
            'video_id' => $comment->getVideoId(),
            'owner_id' => $comment->getOwnerId(),
            'content'  => $comment->getContent(),
            'date'     => $comment->getDate()
        ];

        return $this->insert($params);
    }

    /**
     * @param int $comment_id
     * @param int $owner_id
     *
     * @return int
     */
    public function deleteComment(int $comment_id, int $owner_id): int
    {
        $params = [
            'id'       => $comment_id,
            'owner_id' => $owner_id
        ];

        return $this->delete($params);
    }

    protected function setTable()
    {
        $this->table = 'comments';
    }
}