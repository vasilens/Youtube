<?php

namespace model;

use PDO;
use PDOException;

class UserDAO extends AbstractDAO
{
    /**
     * @return array
     */
    public function getAll()
    {
        $pdo = $this->getPDO();
        $sql = "SELECT id, username, email, name, registration_date FROM users";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }

    /**
     * @param string $email
     *
     * @return array | bool
     */
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

        return $row;
    }

    /**
     * @param string $username
     *
     * @return array | bool
     */
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

        return $row;
    }

    /**
     * @param User $user
     *
     * @return bool
     */
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
        catch (PDOException $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    /**
     * @param int $id
     *
     * @return array
     */
    public function getById($id)
    {
        $pdo = $this->getPDO();
        $sql = "SELECT username, name, registration_date, avatar_url FROM users WHERE id = ?;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($id));
        $rows = $stmt->fetch(PDO::FETCH_ASSOC);

        return $rows;
    }

    /**
     * @param User $user
     */
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

    /**
     * @param int $follower_id
     * @param int $followed_id
     */
    public function followUser($follower_id, $followed_id)
    {
        $pdo = $this->getPDO();
        $sql = "INSERT INTO users_follow_users (follower_id, followed_id)
                VALUES (?, ?);";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($follower_id, $followed_id));
    }

    /**
     * @param int $follower_id
     * @param int $followed_id
     */
    public function unfollowUser($follower_id, $followed_id)
    {
        $pdo = $this->getPDO();
        $sql = "DELETE FROM users_follow_users WHERE follower_id = ? AND followed_id = ?;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($follower_id, $followed_id));
    }

    /**
     * @param int $follower_id
     * @param int $followed_id
     *
     * @return bool
     */
    public function isFollowing($follower_id, $followed_id)
    {
        $pdo = $this->getPDO();
        $sql = "SELECT followed_id FROM users_follow_users WHERE follower_id = ? AND followed_id = ?;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($follower_id, $followed_id));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row){

            return true;
        }

        return false;
    }

    /**
     * @param int $user_id
     * @param int $video_id
     *
     * @return int
     */
    public function isReacting($user_id, $video_id)
    {
        $pdo = $this->getPDO();
        $sql = "SELECT status FROM users_react_videos WHERE user_id = ? AND video_id = ?;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($user_id, $video_id));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {

            return $row["status"];
        }

        return -1;
    }

    /**
     * @param int $user_id
     * @param int $video_id
     * @param int $status
     */
    public function reactVideo($user_id, $video_id, $status){
        $pdo = $this->getPDO();
        $sql = "INSERT INTO users_react_videos (user_id, video_id, status)
                VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($user_id, $video_id, $status));
    }

    /**
     * @param int $user_id
     * @param int $video_id
     */
    public function unreactVideo($user_id, $video_id){
        $pdo = $this->getPDO();
        $sql = "DELETE FROM users_react_videos WHERE user_id = ? AND video_id = ?;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($user_id, $video_id));
    }

    /**
     * @param int $user_id
     * @param int $comment_id
     * @return int
     */
    public function isReactingComment($user_id, $comment_id){
        $pdo = $this->getPDO();
        $sql = "SELECT status FROM users_react_comments WHERE user_id = ? AND comment_id = ?;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($user_id, $comment_id));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {

            return $row["status"];
        }

        return -1;
    }

    /**
     * @param int $user_id
     * @param int $comment_id
     * @param int $status
     */
    public function reactComment($user_id, $comment_id, $status)
    {
        $pdo = $this->getPDO();
        $sql = "INSERT INTO users_react_comments (user_id, comment_id, status)
                VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($user_id, $comment_id, $status));
    }

    /**
     * @param int $user_id
     * @param int $comment_id
     */
    public function unreactComment($user_id, $comment_id)
    {
        $pdo = $this->getPDO();
        $sql = "DELETE FROM users_react_comments WHERE user_id = ? AND comment_id = ?;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($user_id, $comment_id));
    }

    /**
     * @param int $comment_id
     * @param int $status
     * @return int
     */
    public function getCommentReactions($comment_id, $status)
    {
        $pdo = $this->getPDO();
        $sql = "SELECT COUNT(*) AS count FROM users_react_comments 
                WHERE comment_id = ? AND status = ?;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($comment_id, $status));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {

            return $row["count"];
        }

        return 0;
    }

    /**
     * @param int $logged_user
     *
     * @return array | bool
     */
    public function getSubscriptions($logged_user)
    {
        $pdo = $this->getPDO();
        $sql = "SELECT u.username, u.avatar_url, u.name, ufu.followed_id FROM users_follow_users AS ufu
                JOIN users AS u ON u.id = ufu.followed_id WHERE ufu.follower_id = ?;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($logged_user));
        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($row) {

            return $row;
        }

        return false;
    }

    /**
     * @param int $followed_id
     * @return array
     */
    public function getFollowedUser($followed_id)
    {
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

    /**
     * @param int $video_id
     * @param int $user_id
     * @param string $date
     */
    public function addToHistory($video_id, $user_id, $date)
    {
        try {
            $pdo = $this->getPDO();
            $pdo->beginTransaction();
            $sql1 = "SELECT * FROM users_watch_videos WHERE video_id = ? AND user_id = ?;";
            $sql2 = "INSERT INTO users_watch_videos (video_id, user_id, date)
                VALUES (?, ?, ?)";
            $sql3 = "UPDATE users_watch_videos SET date = ?
                    WHERE video_id = ? AND user_id = ?;";
            $stmt1 = $pdo->prepare($sql1);
            $stmt1->execute(array($video_id, $user_id));
            if (!$stmt1->rowCount()) {
                $stmt2 = $pdo->prepare($sql2);
                $stmt2->execute(array($video_id, $user_id, $date));
            }
            else {
                $stmt3 = $pdo->prepare($sql3);
                $stmt3->execute(array($date, $video_id, $user_id));
            }
            $pdo->commit();
        } catch (PDOException $e) {
            $pdo->rollBack();
            throw new PDOException();
        }
    }

    /**
     *
     */
    protected function setTable()
    {
        // TODO: Implement setTable() method.
    }
}