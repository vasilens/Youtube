<?php

namespace model;

use PDO;
use PDOException;

class SearchDAO extends AbstractDAO
{
    /**
     * @param string $search_query
     *
     * @return array
     */
    public function getSearchedVideos($search_query)
    {
        $params = [
            'searchQuery' => $search_query
        ];
        $sql = "
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
            $sql,
            $params
        );
    }

    /**
     * @param string $search_query
     *
     * @return array
     */
    public function getSearchedUsers($search_query)
    {
        $params = [
            'searchQuery' => $search_query
        ];
        $sql = "
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
            $sql,
            $params
        );
    }

    /**
     * @param string $search_query
     *
     * @return array
     */
    public function getSearchedPlaylists($search_query)
    {
        $params = [
            'searchQuery' => $search_query
        ];
        $sql = "
            SELECT
                p.id,
                p.playlist_title,
                p.date_created 
            FROM 
                playlists AS p
            WHERE
                p.playlist_title LIKE ?;
        ";

        return $this->fetchAllAssoc(
            $sql,
            $params
        );
    }

    /**
     * @return array
     */
    public function getAllPlaylists()
    {
        $sql = "
            SELECT
                *
            FROM
                playlists;
        ";
        return $this->fetchAllAssoc($sql);
    }

    /**
     * @return array
     */
    public function getAllVideos()
    {
        $sql = "
            SELECT
                *
            FROM
                videos;
        ";

        return $this->fetchAllAssoc($sql);
    }

    /**
     * @return array
     */
    public function getAllUsers()
    {
        $sql = "
            SELECT
                *
            FROM
                users;
        ";

        return $this->fetchAllAssoc($sql);
    }

    protected function setTable()
    {
        // TODO: Implement setTable() method.
    }
}