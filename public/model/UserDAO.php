<?php
namespace model;
use PDO;
use PDOException;
class UserDAO extends BaseDao {
    private static $instance;

    private function __construct()
    {
    }

    public static function getInstance(){
        if (self::$instance == null){
            self::$instance = new UserDAO();
        }
        return self::$instance;
    }

    public function getAll() {
        $pdo = $this->getPDO();
        $sql = "SELECT id, username, email, name, registration_date FROM users";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    }

    public function checkUser($email)
    {
        $pdo = $this->getPDO();
        $sql = "SELECT id, username, email, password, name, avatar_url FROM users WHERE email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($email));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (empty($row)) {
            return false;
        }
        else {
            return $row;
        }
    }

    public function checkUsername($username)
    {
        $pdo = $this->getPDO();
        $sql = "SELECT id, username, email FROM users WHERE username = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($username));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (empty($row)) {
            return false;
        }
        else {
            return $row;
        }
    }

    public function registerUser(User $user)
    {
        $username = $user->getUsername();
        $email = $user->getEmail();
        $password = $user->getPassword();
        $full_name = $user->getFullName();
        $date = $user->getRegistrationDate();
        $avatar_url = $user->getAvatarUrl();
        try {
            $pdo = $this->getPDO();
            $pdo->beginTransaction();
            $sql = "INSERT INTO users (username,  email, password, name, registration_date, avatar_url)
                VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array($username, $email, $password, $full_name, $date, $avatar_url));
            $user->setId($pdo->lastInsertId());

            $playlist_title = "Watch Later";
            $owner_id = $user->getId();
            $date_created = date("Y-m-d H:i:s");
            $sql = "INSERT INTO playlists (playlist_title, owner_id, date_created) VALUES (?, ?, ?);";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array($playlist_title, $owner_id, $date_created));
            $pdo->commit();
            return true;
        }
        catch (PDOException $e){
            $pdo->rollBack();
            throw $e;
        }
    }

    public function getById($id){
        $pdo = $this->getPDO();
        $sql = "SELECT username, name, registration_date, avatar_url FROM users WHERE id = ?;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($id));
        $rows = $stmt->fetch(PDO::FETCH_ASSOC);
        return $rows;
    }

    public function editUser(User $user)
    {
        $username = $user->getUsername();
        $email  = $user->getEmail();
        $password   = $user->getPassword();
        $full_name = $user->getFullName();
        $avatar_url = $user->getAvatarUrl();
        $id = $user->getId();
        $pdo = $this->getPDO();
        $sql = "UPDATE users SET username = ? , email = ?, password = ?, name = ?, avatar_url = ? WHERE id=?;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($username, $email, $password, $full_name, $avatar_url, $id));
    }

    public function followUser($follower_id, $followed_id){
        $pdo = $this->getPDO();
        $sql = "INSERT INTO users_follow_users (follower_id, followed_id)
                VALUES (?, ?);";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($follower_id, $followed_id));
    }

    public function unfollowUser($follower_id, $followed_id){
        $pdo = $this->getPDO();
        $sql = "DELETE FROM users_follow_users WHERE follower_id = ? AND followed_id = ?;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($follower_id, $followed_id));
    }

    public function isFollowing($follower_id, $followed_id){
        $pdo = $this->getPDO();
        $sql = "SELECT followed_id FROM users_follow_users WHERE follower_id = ? AND followed_id = ?;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($follower_id, $followed_id));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row){
            return true;
        }
        else {
            return false;
        }
    }

    public function isReacting($user_id, $video_id){
        $pdo = $this->getPDO();
        $sql = "SELECT status FROM users_react_videos WHERE user_id = ? AND video_id = ?;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($user_id, $video_id));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row){
            return $row["status"];
        }
        else {
            return -1;
        }
    }

    public function reactVideo($user_id, $video_id, $status){
        $pdo = $this->getPDO();
        $sql = "INSERT INTO users_react_videos (user_id, video_id, status)
                VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($user_id, $video_id, $status));
    }

    public function unreactVideo($user_id, $video_id){
        $pdo = $this->getPDO();
        $sql = "DELETE FROM users_react_videos WHERE user_id = ? AND video_id = ?;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($user_id, $video_id));
    }

    public function isReactingComment($user_id, $comment_id){
        $pdo = $this->getPDO();
        $sql = "SELECT status FROM users_react_comments WHERE user_id = ? AND comment_id = ?;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($user_id, $comment_id));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row){
            return $row["status"];
        }
        else {
            return -1;
        }
    }

    public function reactComment($user_id, $comment_id, $status){
        $pdo = $this->getPDO();
        $sql = "INSERT INTO users_react_comments (user_id, comment_id, status)
                VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($user_id, $comment_id, $status));
    }

    public function unreactComment($user_id, $comment_id){
        $pdo = $this->getPDO();
        $sql = "DELETE FROM users_react_comments WHERE user_id = ? AND comment_id = ?;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($user_id, $comment_id));
    }

    public function getCommentReactions($comment_id, $status){
        $pdo = $this->getPDO();
        $sql = "SELECT COUNT(*) AS count FROM users_react_comments 
                WHERE comment_id = ? AND status = ?;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($comment_id, $status));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return $row["count"];
        }
        else {
            return 0;
        }
    }

    public function getSubscriptions($logged_user){
        $pdo = $this->getPDO();
        $sql = "SELECT u.username, u.avatar_url, u.name, ufu.followed_id FROM users_follow_users AS ufu
                JOIN users AS u ON u.id = ufu.followed_id WHERE ufu.follower_id = ?;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($logged_user));
        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($row){
            return $row;
        }
        else {
            return false;
        }
    }
    public function getFollowedUser($followed_id){
        $pdo = $this->getPDO();
        $sql = "SELECT u.username, u.avatar_url, u.name,p.id, p.playlist_title, p.date_created, v.title,
                v.date_uploaded,v.id AS video_id, v.thumbnail_url FROM users AS u
                JOIN playlists AS p ON p.owner_id = u.id
                JOIN videos AS v ON v.owner_id = u.id WHERE u.id = ?;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($followed_id));
        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $row;
    }

    public function addToHistory($video_id, $user_id, $date){
        try{
            $pdo = $this->getPDO();
            $pdo->beginTransaction();
            $sql1 = "SELECT * FROM users_watch_videos WHERE video_id = ? AND user_id = ?;";
            $sql2 = "INSERT INTO users_watch_videos (video_id, user_id, date)
                VALUES (?, ?, ?)";
            $sql3 = "UPDATE users_watch_videos SET date = ?
                    WHERE video_id = ? AND user_id = ?;";
            $stmt1 = $pdo->prepare($sql1);
            $stmt1->execute(array($video_id, $user_id));
            if(!$stmt1->rowCount()){
                $stmt2 = $pdo->prepare($sql2);
                $stmt2->execute(array($video_id, $user_id, $date));
            }
            else {
                $stmt3 = $pdo->prepare($sql3);
                $stmt3->execute(array($date, $video_id, $user_id));
            }
            $pdo->commit();
        } catch (PDOException $e){
            $pdo->rollBack();
            throw new PDOException();
        }
    }

}