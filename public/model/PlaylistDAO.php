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
        $query = "
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
            $query,
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
        //playlists
        $this->insert($params);

        return $this->lastInsertId();
    }

    /**
     * @param int $playlistId
     *
     * @return array
     */
    public function getVideosFromPlaylist($playlistId)
    {
        $params = [
            'playlistId' => $playlistId
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
            FROM videos AS v 
                JOIN users AS u ON v.owner_id = u.id
                JOIN added_to_playlist AS atp ON v.id = atp.video_id
                JOIN playlists AS p ON p.id = atp.playlist_id      
            WHERE 
                atp.playlist_id = :playlistId
                ORDER BY atp.date_added;
        ";

        return $this->fetchAllAssoc(
            $query,
            $params
        );
    }

    /**
     * @param int    $playlistId
     * @param int    $videoId
     * @param string $date
     */
    public function addToPlaylist($playlistId, $videoId, $date)
    {
        $params = [
            'playlist_id' => $playlistId,
            'video_id'    => $videoId,
            'date_added'  => $date
        ];
        //added_to_playlist
        $this->insert($params);
    }

    /**
     * @param int $playlistId
     *
     * @return array
     */
    public function existsPlaylist($playlistId)
    {
        $params = [
            'id' => $playlistId
        ];
        //playlists
        return $this->findAllAssoc($params);
    }

    /**
     * @param int $videoId
     *
     * @return array
     */
    public function existsVideo($videoId)
    {
        $params = [
            'id' => $videoId
        ];
        //videos
        return $this->findAllAssoc($params);
    }

    /**
     * @param int $videoId
     * @param int $playlistId
     *
     * @return array
     */
    public function existsRecord($playlistId, $videoId)
    {
        $params = [
            'playlistId' => $playlistId,
            'videoId'    => $videoId
        ];
        $query = "
            SELECT
                *
            FROM 
                added_to_playlist
            WHERE 
                playlist_id = :playlistId
                AND video_id = :videoId;
        ";

        return $this->fetchAllAssoc(
            $query,
            $params
        );
    }

    /**
     * @param int    $playlistId
     * @param int    $videoId
     * @param string $date
     */
    public function updateRecord($playlistId, $videoId, $date)
    {
        $params = [
            'date_added' => $date
        ];
        $conditions = [
            'playlist_id' => $playlistId,
            'video_id'    => $videoId
        ];
        //added_to_playlist
        $this->update($params, $conditions);
    }

    protected function setTable()
    {
        $this->table = 'playlists';
    }
}