<?php

namespace model;

class PlaylistDAO extends AbstractDAO
{
    /**
     * @return void
     */
    protected function setTable()
    {
        $this->table = "playlists";
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
            FROM 
                videos AS v 
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
     * @param string $searchQuery
     *
     * @return array
     */
    public function getSearchedPlaylists($searchQuery)
    {
        $params = [
            'searchQuery' => $searchQuery
        ];
        $query = "
            SELECT
                p.id,
                p.playlist_title,
                p.date_created 
            FROM 
                playlists AS p
            WHERE
                p.playlist_title LIKE :searchQuery;
        ";

        return $this->fetchAllAssoc(
            $query,
            $params
        );
    }
}