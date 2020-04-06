<?php

namespace model;

class UsersReactCommentsDAO extends AbstractDAO
{
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

    /**
     * @param int $comment_id
     * @param int $status
     *
     * @return int
     */
    public function getCommentReactions($comment_id, $status)
    {
        $params = [
            'comment_id' => $comment_id,
            'status'     => $status
        ];
        $query = "
            SELECT
                COUNT(*) AS count
            FROM
                {$this->table} 
            WHERE
                comment_id = :comment_id AND status = :status;";
        $row = $this->fetchAssoc($query, $params);

        return $row['count'] ?? 0;
    }

    protected function setTable()
    {
        $this->table = 'users_react_comments';
    }
}