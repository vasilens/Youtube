<?php

namespace model;

class UsersReactCommentsDAO extends AbstractDAO
{
    /**
     * @param int $userId
     * @param int $commentId
     *
     * @return int
     */
    public function isReactingComment(int $userId, int $commentId): int
    {
        $params = [
            'user_id'    => $userId,
            'comment_id' => $commentId
        ];
        $row = $this->findBy($params);
        if ($row) {

            return $row["status"];
        }

        return -1;
    }

    /**
     * @param int $userId
     * @param int $commentId
     * @param int $status
     */
    public function reactComment(int $userId, int $commentId, int $status)
    {
        $params = [
            'user_id'    => $userId,
            'comment_id' => $commentId,
            'status'     => $status
        ];
        $this->insert($params);
    }

    /**
     * @param int $userId
     * @param int $commentId
     */
    public function unreactComment(int $userId, int $commentId)
    {
        $params = [
            'user_id'    => $userId,
            'comment_id' => $commentId
        ];
        $this->delete($params);
    }

    /**
     * @param int $commentId
     * @param int $status
     *
     * @return int
     */
    public function getCommentReactions(int $commentId, int $status): int
    {
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
     * @param int $videoId
     *
     * @return array
     */
    public function getComments(int $videoId): array
    {
        $params = [
            'video_id' => $videoId
        ];
        $query = "
            SELECT
                c.id,
                c.content,
                c.date,
                c.owner_id,
                u.name,
                u.avatar_url, 
                COALESCE(SUM(urc.status), 0) AS likes,
                COALESCE((COUNT(urc.status) - SUM(urc.status)), 0) AS dislikes
            FROM
                comments AS c 
                JOIN users AS u ON c.owner_id = u.id
				LEFT JOIN users_react_comments AS urc ON c.id = urc.comment_id
            WHERE 
                c.video_id = :video_id
                GROUP BY c.id;
        ";

        return $this->fetchAllAssoc($query, $params);
    }

    /**
     * @param int $commentId
     *
     * @return array
     */
    public function getCommentById(int $commentId): array
    {
        $params = [
            'comment_id' => $commentId
        ];
        $query = "
            SELECT
                c.id,
                c.content,
                c.date,
                c.owner_id,
                u.name,
                u.avatar_url, 
                COALESCE(SUM(urc.status), 0) AS likes,
                COALESCE((COUNT(urc.status) - SUM(urc.status)), 0) AS dislikes
            FROM
                comments AS c 
                JOIN users AS u ON c.owner_id = u.id
				LEFT JOIN users_react_comments AS urc ON c.id = urc.comment_id
            WHERE
                c.id = :comment_id;
        ";

        return $this->fetchAssoc($query, $params);
    }

    protected function setTable()
    {
        $this->table = 'users_react_comments';
    }
}