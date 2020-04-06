<?php

namespace model;

use PDO;
use PDOException;

class SearchDAO extends AbstractDAO
{
    /**
     * @param string $searchQuery
     *
     * @return array
     */
    public function getSearchedVideos($searchQuery)
    {
        $params = [
            'searchQuery' => $searchQuery
        ];
        $query = "
            SELECT
                v.id,
                v.title,
                v.date_uploaded,
                u.username,
                v.thumbnail_url,
                v.views
            FROM
                videos AS v 
                JOIN users AS u ON v.owner_id = u.id 
            WHERE
                v.title LIKE :searchQuery;
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
    public function getSearchedUsers($searchQuery)
    {
        $params = [
            'searchQuery' => $searchQuery
        ];
        $query = "
            SELECT
                u.id,
                u.username,
                u.name,
                u.avatar_url,
                u.registration_date
            FROM
                users AS u
            WHERE
                u.username LIKE :searchQuery;
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
    protected function setTable()
    {
    }
}