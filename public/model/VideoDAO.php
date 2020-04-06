<?php

namespace model;

class VideoDAO extends AbstractDAO
{

    protected function setTable()
    {
        $this->table = 'videos';
    }

    /**
     * @param int           $ownerId
     * @param string | null $orderby
     *
     * @return array
     */
    public function getByOwnerId($ownerId, $orderby = null)
    {
        $params = [
            'owner_id' => $ownerId,
        ];
        $query = "
            SELECT
                v.id,
                v.title,
                v.date_uploaded,
                u.username,
                v.views,
                v.thumbnail_url,
                SUM(urv.status) AS likes
            FROM
                videos AS v 
                JOIN users AS u ON v.owner_id = u.id
                LEFT JOIN users_react_videos AS urv ON urv.video_id = v.id
            WHERE
                owner_id = :owner_id
                GROUP BY v.id
                $orderby;
        ";

        return $this->fetchAllAssoc($query, $params);
    }

    /**
     * @param int $id
     *
     * @return array
     */
    public function getById($id)
    {
        $params = [
            'id' => $id
        ];
        $query = "
            SELECT
                v.id,
                v.title,
                v.description,
                v.date_uploaded,
                v.owner_id,
                v.views,
                v.category_id,
                v.video_url,
                v.duration,
                v.thumbnail_url, 
                u.id AS user_id,
                u.username,
                u.name
            FROM
                videos AS v
                JOIN users AS u ON v.owner_id = u.id
            WHERE
                v.id = :id;
        ";

        return $this->fetchAssoc($query, $params);
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
            FROM
                videos AS v 
                JOIN users AS u ON v.owner_id = u.id
                LEFT JOIN users_react_videos AS urv ON urv.video_id = v.id
                GROUP BY v.id
                $orderby;
        ";

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
        $params = [
            'user_id' => $user_id
        ];
        $query = "
            SELECT
                v.id,
                v.title,
                v.date_uploaded,
                u.username,
                v.views,
                v.thumbnail_url,
                SUM(urv.status) AS likes
            FROM
                videos AS v 
                JOIN users AS u ON v.owner_id = u.id
                LEFT JOIN users_react_videos AS urv ON urv.video_id = v.id
                LEFT JOIN users_watch_videos AS uwv ON uwv.video_id = v.id
            WHERE
                uwv.user_id = :user_id
                GROUP BY v.id
                ORDER BY uwv.date DESC
        ";

        return $this->fetchAllAssoc($query, $params);
    }

    /**
     * @param int           $user_id
     * @param string | null $orderby
     *
     * @return array
     */
    public function getLikedVideos($user_id, $orderby=null)
    {
        $params = [
            'user_id' => $user_id
        ];
        $query = "
            SELECT
                v.id,
                v.title,
                v.date_uploaded,
                u.username,
                v.views,
                v.thumbnail_url,
                SUM(urv.status) AS likes
            FROM
                videos AS v 
                JOIN users AS u ON v.owner_id = u.id
                LEFT JOIN users_react_videos AS urv ON urv.video_id = v.id
            WHERE
                urv.user_id = :user_id AND urv.status = 1
                GROUP BY v.id;
        ";

        return $this->fetchAllAssoc($query, $params);
    }

    /**
     * @param int $video_id
     * @param int $status
     *
     * @return int | bool
     */
    public function getReactions($video_id, $status)
    {
        $params = [
            'video_id' => $video_id,
            'status'   => $status
        ];
        $query = "
            SELECT
                COUNT(*) AS count
            FROM
                users_react_videos 
            WHERE
                video_id = :video_id AND status = :status;
        ";
        $row = $this->fetchAssoc($query, $params);
        if ($row) {
            return $row["count"];
        }

        return false;
    }



    /**
     * @param int $video_id
     */
    public function updateViews($video_id)
    {
        $params = [
            'id' => $video_id
        ];
        $query = "
            UPDATE
                videos
            SET
                views = views + 1
            WHERE
                id = :id
        ";
        $this->prepareAndExecute($query, $params);
    }

    /**
     * @return array
     */
    public function getMostWatched()
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
            FROM
                videos AS v 
                JOIN users AS u ON v.owner_id = u.id
                LEFT JOIN users_react_videos AS urv ON urv.video_id = v.id
                GROUP BY v.id
                ORDER BY views DESC LIMIT 5;
        ";

        return $this->fetchAllAssoc($query);
    }

    /**
     * @param int $user_id
     *
     * @return array
     */
    public function getWatchLater($user_id)
    {
        $params = [
            'id' => $user_id
        ];
        $query = "
            SELECT
                v.id,
                v.title,
                v.date_uploaded,
                p.playlist_title,
                u.username,
                v.views,
                v.thumbnail_url
            FROM
                videos AS v 
                JOIN users AS u ON v.owner_id = u.id
                JOIN added_to_playlist AS atp ON v.id = atp.video_id
                JOIN playlists AS p ON p.id = atp.playlist_id
            WHERE
                p.playlist_title = 'Watch Later' AND p.owner_id = :id
                ORDER BY atp.date_added;";

        return $this->fetchAllAssoc($query, $params);
    }
}
