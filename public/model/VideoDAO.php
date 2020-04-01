<?php

namespace model;

use http\Exception\BadUrlException;
use PDO;
use PDOException;

class VideoDAO extends AbstractDAO
{
    /**
     * @param Video $video
     */
    public function add(Video $video)
    {
        $title = $video->getTitle();
        $description = $video->getDescription();
        $date_uploaded = $video->getDateUploaded();
        $owner_id = $video->getOwnerId();
        $category_id = $video->getCategoryId();
        $video_url = $video->getVideoUrl();
        $duration = $video->getDuration();
        $thumbnail_url = $video->getThumbnailUrl();
        $pdo = $this->getPDO();
        $sql = "INSERT INTO videos
                (title, description, date_uploaded, owner_id, category_id, video_url, duration, thumbnail_url)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?);";
        $params = array($title, $description, $date_uploaded, $owner_id, $category_id, $video_url, $duration, $thumbnail_url);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $video_id = $pdo->lastInsertId();
        $video->setId($video_id);
    }

    /**
     * @param Video $video
     */
    public function edit(Video $video)
    {
        $title = $video->getTitle();
        $description = $video->getDescription();
        $category_id = $video->getCategoryId();
        $thumbnail_url = $video->getThumbnailUrl();
        $id = $video->getId();
        $pdo = $this->getPDO();
        $sql = "UPDATE videos
                SET title = ?, description = ?, category_id = ?, thumbnail_url = ?
                WHERE id = ?;";
        $params = array($title, $description, $category_id, $thumbnail_url, $id);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
    }

    /**
     * @param int $id
     * @param int $owner_id
     */
    public function deleteVideo($id, $owner_id)
    {
        $pdo = $this->getPDO();
        $sql = "DELETE FROM videos WHERE id = ? AND owner_id = ?;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($id, $owner_id));
    }

    /**
     * @return array
     */
    public function getCategories()
    {
        $pdo = $this->getPDO();
        $sql = "SELECT id, name FROM categories;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    public function getCategoryById($id)
    {
        $pdo = $this->getPDO();
        $sql = "SELECT id, name FROM categories WHERE id = ?;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($id));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row){

            return true;
        }

        return false;
    }

    /**
     * @param int           $owner_id
     * @param string | null $orderby
     *
     * @return array
     */
    public function getByOwnerId($owner_id, $orderby = null)
    {
        $pdo = $this->getPDO();
        $sql = "SELECT v.id, v.title, v.date_uploaded, u.username, v.views, v.thumbnail_url, SUM(urv.status) AS likes FROM videos AS v 
                JOIN users AS u ON v.owner_id = u.id
                LEFT JOIN users_react_videos AS urv ON urv.video_id = v.id
                WHERE owner_id = ?
                GROUP BY v.id
                $orderby;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($owner_id));
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }

    /**
     * @param int $id
     *
     * @return array
     */
    public function getById($id)
    {
        $pdo = $this->getPDO();
        $sql = "SELECT v.id, v.title, v.description, v.date_uploaded, v.owner_id, v.views, v.category_id, v.video_url, v.duration, v.thumbnail_url, 
                u.id AS user_id, u.username, u.name FROM videos AS v
                JOIN users AS u ON v.owner_id = u.id
                WHERE v.id = ?;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($id));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row;
    }

    /**
     * @param string | null $orderby
     *
     * @return array
     */
    public function getAll($orderby = null)
    {
        $query = "
            SELECT
                v.id,
                v.title,
                v.date_uploaded,
                u.username,
                v.views,
                v.thumbnail_url,
                SUM(urv.status) AS likes
            FROM videos AS v 
                JOIN users AS u ON v.owner_id = u.id
                LEFT JOIN users_react_videos AS urv ON urv.video_id = v.id
                GROUP BY v.id
                $orderby;";

        return $this->fetchAllAssoc($query);
    }

    /**
     * @param int           $user_id
     * @param string | null $orderby
     *
     * @return array
     */
    public function getHistory ($user_id, $orderby=null)
    {
        $pdo = $this->getPDO();
        $sql = "SELECT v.id, v.title, v.date_uploaded, u.username, v.views, v.thumbnail_url, SUM(urv.status) AS likes FROM videos AS v 
                JOIN users AS u ON v.owner_id = u.id
                LEFT JOIN users_react_videos AS urv ON urv.video_id = v.id
                LEFT JOIN users_watch_videos AS uwv ON uwv.video_id = v.id
                WHERE uwv.user_id = ?
                GROUP BY v.id
                ORDER BY uwv.date DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($user_id));
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }

    /**
     * @param int           $user_id
     * @param string | null $orderby
     *
     * @return array
     */
    public function getLikedVideos($user_id, $orderby=null)
    {
        $pdo = $this->getPDO();
        $sql = "SELECT v.id, v.title, v.date_uploaded, u.username, v.views, v.thumbnail_url, SUM(urv.status) AS likes FROM videos AS v 
                JOIN users AS u ON v.owner_id = u.id
                LEFT JOIN users_react_videos AS urv ON urv.video_id = v.id
                WHERE urv.user_id = ? AND urv.status = 1
                GROUP BY v.id;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($user_id));
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }

    /**
     * @param int $video_id
     * @param int $status
     *
     * @return int | bool
     */
    public function getReactions($video_id, $status)
    {
        $pdo = $this->getPDO();
        $sql = "SELECT COUNT(*) AS count FROM users_react_videos 
                WHERE video_id = ? AND status = ?;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($video_id, $status));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {

            return $row["count"];
        }

        return false;
    }

    /**
     * @param Comment $comment
     *
     * @return string
     */
    public function addComment(Comment $comment)
    {
        $pdo = $this->getPDO();
        $sql = "INSERT INTO comments
                (video_id, owner_id, content, date)
                VALUES (?, ?, ?, ?);";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($comment->getVideoId(), $comment->getOwnerId(), $comment->getContent(), $comment->getDate()));

        return $pdo->lastInsertId();
    }

    /**
     * @param int $comment_id
     *
     * @return array
     */
    public function getCommentById($comment_id)
    {
        $pdo = $this->getPDO();
        $sql = "SELECT c.id, c.content, c.date, c.owner_id, u.name, u.avatar_url, 
                    COALESCE(SUM(urc.status), 0) AS likes, COALESCE((COUNT(urc.status) - SUM(urc.status)), 0) AS dislikes FROM comments AS c 
                    JOIN users AS u ON c.owner_id = u.id
					LEFT JOIN users_react_comments AS urc ON c.id = urc.comment_id
                    WHERE c.id = ?;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($comment_id));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row;
    }

    /**
     * @param int $video_id
     *
     * @return array
     */
    public function getComments($video_id)
    {
        $pdo = $this->getPDO();
        $sql = "SELECT c.id, c.content, c.date, c.owner_id, u.name, u.avatar_url, 
                    COALESCE(SUM(urc.status), 0) AS likes, COALESCE((COUNT(urc.status) - SUM(urc.status)), 0) AS dislikes FROM comments AS c 
                    JOIN users AS u ON c.owner_id = u.id
					LEFT JOIN users_react_comments AS urc ON c.id = urc.comment_id
                    WHERE c.video_id = ?
                    GROUP BY c.id;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($video_id));
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }

    /**
     * @param int $comment_id
     * @param int $owner_id
     */
    public function deleteComment($comment_id, $owner_id)
    {
        $pdo = $this->getPDO();
        $sql = "DELETE FROM comments WHERE id = ? AND owner_id = ?;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($comment_id, $owner_id));
    }

    /**
     * @param int $video_id
     */
    public function updateViews($video_id)
    {
        $pdo = $this->getPDO();
        $sql = "UPDATE videos SET views = views + 1 WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($video_id));
    }

    /**
     * @return array
     */
    public function getMostWatched()
    {
        $pdo = $this->getPDO();
        $sql = "SELECT v.id, v.title, v.date_uploaded, u.username, v.views, v.thumbnail_url, SUM(urv.status) AS likes FROM videos AS v 
                JOIN users AS u ON v.owner_id = u.id
                LEFT JOIN users_react_videos AS urv ON urv.video_id = v.id
                GROUP BY v.id
                ORDER BY views DESC LIMIT 5;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }

    /**
     * @param int $user_id
     *
     * @return array
     */
    public function getWatchLater($user_id)
    {
        $pdo = $this->getPDO();
        $sql = "SELECT v.id, v.title, v.date_uploaded, p.playlist_title, u.username, v.views, v.thumbnail_url FROM videos AS v 
                JOIN users AS u ON v.owner_id = u.id
                JOIN added_to_playlist AS atp ON v.id = atp.video_id
                JOIN playlists AS p ON p.id = atp.playlist_id
                WHERE p.playlist_title = 'Watch Later' AND p.owner_id = ?
                ORDER BY atp.date_added;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($user_id));
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }

    /**
     *
     */
    protected function setTable()
    {
        // TODO: Implement setTable() method.
    }
}
