<?php

namespace model;

use PDO;

class PlaylistDAO extends AbstractDAO
{
    /**
     * @param int $userId
     *
     * @return array
     */
    public function getAllByUserId($userId)
    {
        $params = [
            'ownerId' => $userId
        ];
        $sql = "
            SELECT 
                id,
                playlist_title,
                owner_id,
                date_created 
            FROM 
                playlists 
            WHERE 
                owner_id = :ownerId;
        ";

        return $this->fetchAllAssoc(
            $sql,
            $params
        );
    }

    /**
     * @param Playlist $playlist
     *
     * @return string
     */
    public function create(Playlist $playlist)
    {
        $params = [
            'playlist_title' => $playlist->getTitle(),
            'owner_id'       => $playlist->getOwnerId(),
            'date_created'   => $playlist->getDateCreated()
        ];
        $this->prepareAndExecute(
            $this->createInsertQuery($params),
            $params
        );

        return $this->lastInsertId();
    }

    /**
     * @param int $playlist_id
     *
     * @return array
     */
    public function getVideosFromPlaylist($playlist_id)
    {
        $params = [
            'playlistId' => $playlist_id
        ];
        $sql = "
            SELECT 
                v.id,
                v.title,
                v.date_uploaded,
                p.playlist_title,
                u.username,
                v.views,
                v.thumbnail_url
            FROM videos AS v 
                JOIN users AS u ON v.owner_id = u.id
                JOIN added_to_playlist AS atp ON v.id = atp.video_id
                JOIN playlists AS p ON p.id = atp.playlist_id      
            WHERE 
                atp.playlist_id = :playlistId
                ORDER BY atp.date_added;
        ";

        return $this->fetchAllAssoc(
            $sql,
            $params
        );
    }

    /**
     * @param int    $playlist_id
     * @param int    $video_id
     * @param string $date
     */
    public function addToPlaylist($playlist_id, $video_id, $date)
    {
        $params = [
            'playlist_id' => $playlist_id,
            'video_id'    => $video_id,
            'date_added'  => $date
        ];
        $this->prepareAndExecute(
            $this->createInsertQuery($params),
            $params
        );
    }

    /**
     * @param int $playlist_id
     *
     * @return array | bool
     */
    public function existsPlaylist($playlist_id)
    {
        $params = [
            'playlistId' => $playlist_id
        ];
        $sql = "
            SELECT
                * 
            FROM
                playlists 
            WHERE 
                id = :playlistId;
        ";

        return $this->fetchAssoc(
            $sql,
            $params
        );
    }

    /**
     * @param int $video_id
     *
     * @return array
     */
    public function existsVideo($video_id)
    {
        $params = [
            ':videoId' => $video_id
        ];
        $sql = "
            SELECT
                * 
            FROM
                videos
            WHERE 
                id = :videoId;
        ";

        return $this->fetchAllAssoc(
            $sql,
            $params
        );
    }

    /**
     * @param int $video_id
     * @param int $playlist_id
     *
     * @return array
     */
    public function existsRecord($playlist_id, $video_id)
    {
        $params = [
            'playlistId' => $playlist_id,
            'videoId'    => $video_id
        ];
        $sql = "
            SELECT
                *
            FROM 
                added_to_playlist
            WHERE 
                playlist_id = :playlistId
                AND video_id = :videoId;
        ";

        return $this->fetchAllAssoc(
            $sql,
            $params
        );
    }

    /**
     * @param int    $playlist_id
     * @param int    $video_id
     * @param string $date
     */
    public function updateRecord($playlist_id, $video_id, $date)
    {
        $params = [
            'date_added' => $date
        ];
        $conditions = [
            'playlist_id' => $playlist_id,
            'video_id'    => $video_id
        ];
        $this->prepareAndExecute(
            $this->createUpdateQuery($params, $conditions),
            $params
        );
    }

    protected function setTable()
    {
        // TODO: Implement setTable() method.
    }
}