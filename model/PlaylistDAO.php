<?php


namespace model;
use PDO;

class PlaylistDAO extends BaseDao {
    private static $instance;

    private function __construct()
    {
    }

    public static function getInstance(){
        if (self::$instance == null){
            self::$instance = new PlaylistDAO();
        }
        return self::$instance;
    }

    public function getAllByUserId($userid) {
        $pdo = $this->getPDO();
        $sql = "SELECT id, playlist_title, owner_id, date_created FROM playlists WHERE owner_id = ?;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($userid));
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    }

    public function getAll() {
        $pdo = $this->getPDO();
        $sql = "SELECT id, playlist_title, owner_id, date_created FROM playlists WHERE owner_id = ?;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    }

    public function create(Playlist $playlist){
        $title = $playlist->getTitle();
        $owner_id = $playlist->getOwnerId();
        $date_created = $playlist->getDateCreated();
        $pdo = $this->getPDO();
        $sql = "INSERT INTO playlists (playlist_title, owner_id, date_created) VALUES (?, ?, ?);";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($title, $owner_id, $date_created));
        $playlist_id = $pdo->lastInsertId();
        $playlist->setId($playlist_id);
    }

    public function getVideosFromPlaylist($playlist_id){
        $pdo = $this->getPDO();
        $sql = "SELECT v.id, v.title, v.date_uploaded, p.playlist_title, u.username, v.views, v.thumbnail_url FROM videos AS v 
                JOIN users AS u ON v.owner_id = u.id
                JOIN added_to_playlist AS atp ON v.id = atp.video_id
                JOIN playlists AS p ON p.id = atp.playlist_id
                WHERE atp.playlist_id = ?
                ORDER BY atp.date_added;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($playlist_id));
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    }

    public function getWatchLater($user_id){
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

    public function addToPlaylist($playlist_id, $video_id, $date){
        $pdo = $this->getPDO();
        $sql = "INSERT INTO added_to_playlist (playlist_id, video_id, date_added) VALUES (?, ?, ?);";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($playlist_id, $video_id, $date));
    }

    public function existsPlaylist($playlist_id){
        $pdo = $this->getPDO();
        $sql = "SELECT * FROM playlists WHERE id = ?;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($playlist_id));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row){
            return $row;
        }
        else{
            return false;
        }
    }

    public function existsVideo($video_id){
        $pdo = $this->getPDO();
        $sql = "SELECT * FROM videos WHERE id = ?;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($video_id));
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if($rows){
            return true;
        }
        else{
            return false;
        }
    }

    public function existsRecord($playlist_id, $video_id){
        $pdo = $this->getPDO();
        $sql = "SELECT * FROM added_to_playlist WHERE playlist_id = ? AND video_id = ?;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($playlist_id, $video_id));
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if($rows){
            return true;
        }
        else{
            return false;
        }
    }

    public function updateRecord($playlist_id, $video_id, $date){
        $pdo = $this->getPDO();
        $sql = "UPDATE added_to_playlist SET date_added = ? 
                WHERE playlist_id = ? AND video_id = ?;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($date, $playlist_id, $video_id));
    }
}