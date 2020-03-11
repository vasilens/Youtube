<?php


namespace model;
use PDO;
use PDOException;

class SearchDAO extends BaseDao {
    private static $instance;

    private function __construct()
    {
    }

    public static function getInstance(){
        if (self::$instance == null){
            self::$instance = new SearchDAO();
        }
        return self::$instance;
    }

    public function getSearchedVideos($search_query){
        $pdo = $this->getPDO();
        $sql = "SELECT v.id, v.title, v.date_uploaded, u.username, v.thumbnail_url, v.views FROM videos AS v 
                    JOIN users AS u ON v.owner_id = u.id WHERE v.title LIKE ?;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array("%$search_query%"));
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($rows)) {
            return false;
        }
        else {
            return $rows;
        }
    }

    public function getSearchedUsers($search_query){
        $pdo = $this->getPDO();
        $sql = "SELECT u.id, u.username, u.name, u.avatar_url, u.registration_date FROM users AS u WHERE u.username LIKE ?;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array("%$search_query%"));
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($rows)){
            return false;
        }
        else {
            return $rows;
        }
    }

    public function getSearchedPlaylists($search_query){
        $pdo = $this->getPDO();
        $sql = "SELECT p.id, p.playlist_title, p.date_created FROM playlists AS p WHERE p.playlist_title LIKE ?;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array("%$search_query%"));
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($rows)){
            return false;
        }
        else {
            return $rows;
        }
    }
    public function getAllPlaylists(){
        $pdo = $this->getPDO();
        $sql = "SELECT * FROM playlists";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    }
    public function getAllVideos(){
        $pdo = $this->getPDO();
        $sql = "SELECT * FROM videos";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    }
    public function getAllUsers(){
        $pdo = $this->getPDO();
        $sql = "SELECT * FROM users";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    }
}