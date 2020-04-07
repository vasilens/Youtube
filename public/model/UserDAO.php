<?php

namespace model;

use PDOException;

class UserDAO extends AbstractDAO
{
    /**
     * @return void
     */
    protected function setTable()
    {
        $this->table = 'users';
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function registerUser(User $user): bool
    {
        try {
            $this->beginTransaction();
            $params = [
                'username'          => $user->getUsername(),
                'email'             => $user->getEmail(),
                'password'          => $user->getPassword(),
                'name'              => $user->getFullName(),
                'registration_date' => $user->getRegistrationDate(),
                'avatar_url'        => $user->getAvatarUrl()
            ];
            $query = "
                INSERT INTO
                    users (
                        username,
                        email,
                        password,
                        name,
                        registration_date,
                        avatar_url
                    )
                VALUES (
                    :username,
                    :email,
                    :password,
                    :name,
                    :registration_date,
                    :avatar_url
                    )";
            $this->prepareAndExecute(
                $query,
                $params
            );
            $user->setId($this->lastInsertId());
            $params2 = [
                'playlist_title' => "Watch Later",
                'owner_id'       => $user->getId(),
                'date_created'   => date("Y-m-d H:i:s")
            ];
            $query2 = "
                INSERT INTO
                    playlists (
                        playlist_title,
                        owner_id,
                        date_created
                    )
                VALUES (
                    :playlist_title,
                    :owner_id,
                    :date_created
                    );";
            $this->prepareAndExecute(
                $query2,
                $params2
            );
            $this->commit();

            return true;
        }
        catch (PDOException $e) {
            $this->rollBack();
            throw $e;
        }
    }

    /**
     * @param int $loggedUser
     *
     * @return array|bool
     */
    public function getSubscriptions(int $loggedUser)
    {
        $params = [
            'follower_id' => $loggedUser
        ];
        $query = "
            SELECT
                u.username,
                u.avatar_url,
                u.name,
                ufu.followed_id
            FROM
                users_follow_users AS ufu
                JOIN users AS u ON u.id = ufu.followed_id
            WHERE
                ufu.follower_id = :follower_id;";
        $row = $this->fetchAllAssoc(
            $query,
            $params
        );
        if ($row) {

            return $row;
        }

        return false;
    }

    /**
     * @param int $followedId
     *
     * @return array
     */
    public function getFollowedUser(int $followedId): array
    {
        $params = [
            'followed_id' => $followedId
        ];
        $query = "
            SELECT
                u.username,
                u.avatar_url,
                u.name,
                p.id,
                p.playlist_title,
                p.date_created,
                v.title,
                v.date_uploaded,
                v.id AS video_id,
                v.thumbnail_url
            FROM
                users AS u
                JOIN playlists AS p ON p.owner_id = u.id
                JOIN videos AS v ON v.owner_id = u.id
            WHERE
                u.id = :followed_id;";

        return $this->fetchAllAssoc(
            $query,
            $params
        );
    }

    /**
     * @param int $videoId
     * @param int $userId
     * @param string $date
     *
     * @return void
     */
    public function addToHistory(int $videoId, int $userId, string $date)
    {
        try {
            $this->beginTransaction();
            $params = [
                'video_id' => $videoId,
                'user_id'  => $userId,
            ];
            $query = "
                SELECT
                    *
                FROM
                    users_watch_videos
                WHERE
                    video_id = :video_id AND user_id = :user_id;";
            $params2 = [
                'video_id' => $videoId,
                'user_id'  => $userId,
                'date'     => $date
            ];
            $query2 = "
                INSERT INTO
                    users_watch_videos (
                        video_id,
                        user_id,
                        date
                    )
                VALUES (
                    :video_id,
                    :user_id,
                    :date
                )";
            $params3 = [
                'video_id' => $videoId,
                'user_id'  => $userId,
                'date'     => $date
            ];
            $query3 = "
                UPDATE
                    users_watch_videos
                SET
                    date = :date
                WHERE
                    video_id = :video_id AND user_id = :user_id;";
            if (!$this->rowCount($query, $params)) {
                $this->prepareAndExecute($query2, $params2);
            }
            else {
                $this->prepareAndExecute($query3, $params3);
            }
            $this->commit();
        } catch (PDOException $e) {
            $this->rollBack();
            throw new PDOException();
        }
    }

    /**
     * @param string $searchQuery
     *
     * @return array
     */
    public function getSearchedUsers(string $searchQuery): array
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
}