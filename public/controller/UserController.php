<?php

namespace controller;

use exceptions\AuthorizationException;
use exceptions\InvalidArgumentException;
use exceptions\InvalidFileException;
use model\User;
use model\UserDAO;
use model\UsersFollowUsersDAO;
use model\UsersReactVideosDAO;
use model\VideoDAO;

class UserController extends AbstractController
{
    /**
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public function login()
    {
        $postParams = $this->request->getPostParams();
        if (isset($postParams['login'])) {
            if (!isset($postParams['email']) || !isset($postParams['password'])) {
                throw new InvalidArgumentException("Invalid arguments.");
            }
            $email = $postParams['email'];
            $password = $postParams['password'];
            if (empty(trim($email)) || empty(trim($password))) {
                $msg = "Empty field(s)!";

                include_once "view/login.php";

                return;
            }
            $userDao = new UserDAO();
            $params = [
                'email' => $email
            ];
            $user = $userDao->findBy($params, true);
            if (!$user) {
                $msg = "Invalid password or email! Try again.";

                include_once "view/login.php";

                return;
            }
            if (password_verify($password, $user['password'])) {
                $user['full_name'] = $user['name'];
                unset($user["password"]);
                $_SESSION['logged_user'] = $user;
                header("Location:/");

                echo "Successful login! <br>";

                return;
            }
            else {
                $msg = "Invalid password or email! Try again.";

                include_once "view/login.php";
            }
        }
        else {
            throw new InvalidArgumentException("Invalid arguments.");
        }
    }

    /**
     * @return void
     *
     * @throws InvalidFileException
     */
    public function register()
    {
        $postParams = $this->request->getPostParams();
        if (isset($postParams['register'])) {
            $error = false;
            $msg = "";
            if (!isset($postParams["username"]) || empty(trim($postParams["username"]))) {
                $msg = "Username is empty!";
                $error = true;
            } elseif (!isset($postParams["full_name"]) || empty(trim($postParams["full_name"]))) {
                $msg = "Name is empty!";
                $error = true;
            } elseif (!isset($postParams["email"]) || empty(trim($postParams["email"]))) {
                $msg = "Email is empty!";
                $error = true;
            } elseif (!isset($postParams["password"]) || empty(trim($postParams["password"]))) {
                $msg = "Password is empty!";
                $error = true;
            } elseif (!isset($postParams["cpassword"]) || empty(trim($postParams["cpassword"]))) {
                $msg = "Confirm password is empty!";
                $error = true;
            }
            if ($error) {

                include_once "view/register.php";

                return;
            }
            $username = $postParams['username'];
            $email = $postParams['email'];
            $fullName = $postParams['full_name'];
            $password = $postParams['password'];
            $cpassword = $postParams['cpassword'];
            $msg = $this->registerValidator($username, $email, $password, $cpassword);
            if ($msg != '') {

                include_once "view/register.php";

                return;
            }
            $userDao = new UserDAO();
            $params = [
                'email' => $email
            ];
            $user = $userDao->findBy($params, true);
            if ($user) {
                $msg = "User with that email already exists!";

                include_once "view/login.php";

                return;
            }
            $params = [
                'username' => $username
            ];
            $user = $userDao->findBy($params, true);
            if ($user) {
                $msg = "User with that username already exists!";

                include_once "view/login.php";

                return;
            }
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
            $registrationDate = date("Y-m-d H:i:s");
            $avatarUrl = $this->uploadImage("avatar", $_POST['username']);
            $user = new User($username, $email, $password, $fullName, $registrationDate, $avatarUrl);
            $userDao->registerUser($user);
            $arrayUser = [];
            $arrayUser['id'] = $user->getId();
            $arrayUser['username'] = $user->getUsername();
            $arrayUser['email'] = $user->getEmail();
            $arrayUser['full_name'] = $user->getFullName();
            $arrayUser["avatar_url"] = $user->getAvatarUrl();
            $_SESSION['logged_user'] = $arrayUser;

            include_once "view/main.php";

            echo "Successful registration! You are now logged in.<br>";
        }
    }

    /**
     * @return void
     *
     * @throws AuthorizationException
     * @throws InvalidArgumentException
     * @throws InvalidFileException
     */
    public function edit()
    {
        $postParams = $this->request->getPostParams();
        if (isset($postParams['edit'])) {
            $error = false;
            $msg = "";
            if (!isset($postParams["username"]) || empty(trim($postParams["username"]))) {
                $msg = "Username is empty";
                $error = true;
            } elseif (!isset($postParams["full_name"]) || empty(trim($postParams["full_name"]))) {
                $msg = "Name is empty";
                $error = true;
            } elseif (!isset($postParams["email"]) || empty(trim($postParams["email"]))) {
                $msg = "Email is empty";
                $error = true;
            } elseif (!isset($postParams["password"]) || empty(trim($postParams["password"]))) {
                $msg = "Password is empty";
                $error = true;
            } elseif ((!isset($postParams["cpassword"]) || empty(trim($postParams["cpassword"]))) &&
                (isset($postParams["new_password"]) && !empty(trim($postParams["new_password"])))) {
                $msg = "Confirm new password is empty";
                $error = true;
            }
            if ($error) {

                include_once "view/editProfile.php";

                return;
            }
            $userDao = new UserDAO();
            $params = [
                'email' => $_SESSION["logged_user"]["email"]
            ];
            $user = $userDao->findBy($params, true);
            if (empty($user)) {
                throw new AuthorizationException("Unauthorized user.");
            }
            $params = [
                'username' => $postParams["username"]
            ];
            $user = $userDao->findBy($params, true);
            if ($user && $user["id"] != $_SESSION["logged_user"]["id"]) {

                include_once "view/editProfile.php";

                echo "User with that username already exists!";

                return;
            }
            $params = [
                'email' => $postParams['email']
            ];
            $user = $userDao->findBy($params);
            if ($user && $user["id"] != $_SESSION["logged_user"]["id"]) {

                include_once "view/editProfile.php";

                echo "User with that email already exists!";

                return;
            }
            $password = $user['password'];
            if (password_verify($postParams['password'], $password)) {
                $newAvatar = $this->uploadImage("avatar", $postParams['username']);
                if (!$newAvatar) {
                    $newAvatar = $_SESSION["logged_user"]["avatar_url"];
                }
                $username = $postParams["username"];
                $email = $postParams["email"];
                $fullName = $postParams["full_name"];
                if (isset($postParams['new_password']) && isset($postParams['cpassword'])) {
                    $msg = $this->registerValidator($username, $email, $postParams["new_password"], $postParams["cpassword"]);
                    if ($msg) {

                        include_once "view/editProfile.php";

                        echo $msg;

                        return;
                    }
                    $password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
                }
                $user = new User($username, $email, $password, $fullName, null, $newAvatar);
                $user->setId($_SESSION['logged_user']['id']);
                $params = [
                    'username'   => $user->getUsername(),
                    'email'      => $user->getEmail(),
                    'password'   => $user->getPassword(),
                    'name'       => $user->getFullName(),
                    'avatar_url' => $user->getAvatarUrl()
                ];
                $conditions = [
                    'id' => $user->getId()
                ];
                $userDao->update($params, $conditions);
                $arrayUser = [];
                $arrayUser['id'] = $user->getId();
                $arrayUser['username'] = $user->getUsername();
                $arrayUser['email'] = $user->getEmail();
                $arrayUser['full_name'] = $user->getFullName();
                $arrayUser["avatar_url"] = $user->getAvatarUrl();
                $_SESSION['logged_user'] = $arrayUser;

                include_once "view/main.php";

                echo "Profile changed successfully!";
            } else {

                include_once "view/editProfile.php";

                echo "Incorrect password!";
            }
        } else {
            throw new InvalidArgumentException("Invalid arguments.");
        }
    }

    /**
     * @param $file
     * @param $username
     *
     * @return bool|string
     *
     * @throws InvalidFileException
     */
    public function uploadImage($file, $username)
    {
        if (is_uploaded_file($_FILES[$file]["tmp_name"])) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $_FILES[$file]["tmp_name"]);
            if (!(in_array($mime, array ('image/bmp', 'image/jpeg', 'image/png')))) {
                throw new InvalidFileException ("File is not in supported format.");
            }
            $fileNameParts = explode(".", $_FILES[$file]["name"]);
            $extension = $fileNameParts[count($fileNameParts) - 1];
            $filename = $username . "-" . time() . "." . $extension;
            $fileUrl = "uploads" . DIRECTORY_SEPARATOR . $filename;
            if (move_uploaded_file($_FILES[$file]["tmp_name"], $fileUrl)) {

                return $fileUrl;
            } else {
                throw new InvalidFileException("File handling error.");
            }
        }

        return false;
    }

    /**
     * @return void
     */
    public function logout()
    {
        unset($_SESSION);
        session_destroy();
        header("Location:/");
        exit;
    }

    /**
     * @param string        $username
     * @param string        $email
     * @param string | null $password
     * @param string | null $cpassword
     *
     * @return string
     */
    public function registerValidator($username, $email, $password = null, $cpassword = null)
    {
        $msg = '';
        if (strlen($username) < 8) {
            $msg = "Username must be at least 8 characters! <br>";
        }
        if (!(filter_var($email, FILTER_VALIDATE_EMAIL))) {
            $msg .= " Invalid email. <br> ";
        }
        if ($password != null && $cpassword != null) {
            if ($password === $cpassword) {
                if (!(preg_match("#.*^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).*$#", $password))) {
                    $msg .= " Wrong password input. <br> Password should be at least 8 characters, including lowercase, uppercase, number and symbol. <br>";
                }
            } else {
                $msg .= "Passwords not matching! <br>";
            }
        }

        return $msg;
    }

    /**
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public function getById()
    {
        $getParams = $this->request->getGetParams();
        if (isset($getParams['id'])) {
            $id = $getParams['id'];
        }
        if (empty($id)) {
            throw new InvalidArgumentException("Invalid arguments.");
        }
        $userDao = new UserDAO();
        $usersFollowUsersDao = new UsersFollowUsersDAO();
        $user = $userDao->find($id);
        if (empty($user)) {
            throw new InvalidArgumentException("Invalid user.");
        }
        $user["id"] = $id;
        $params = [
            'follower_id' => $_SESSION["logged_user"]["id"],
            'followed_id' => $id
        ];
        $user["isFollowed"] = $usersFollowUsersDao->findBy($params, true);
        $videoDao = new VideoDAO();
        $videos = $videoDao->getByOwnerId($id);

        include_once "view/profile.php";
    }

    /**
     * @return bool
     *
     * @throws InvalidArgumentException
     */
    public function isFollowing()
    {
        $getParams = $this->request->getGetParams();
        if (isset($getParams['id'])) {
            $followedId = $getParams["id"];
            $followerId = $_SESSION["logged_user"]["id"];
        }
        if (empty($followerId) || empty($followedId)) {
            throw new InvalidArgumentException("Invalid arguments.");
        }
        $usersFollowUsersDao = new UsersFollowUsersDAO();
        $params = [
            'follower_id' => $followerId,
            'followed_id' => $followedId
        ];
        $row = $usersFollowUsersDao->findBy($params);
        if ($row) {
            return true;
        }

        return false;
    }

    /**
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public function follow()
    {
        $getParams = $this->request->getGetParams();
        if (isset($getParams['id'])) {
            $followedId = $getParams["id"];
            $followerId = $_SESSION["logged_user"]["id"];
        }
        if (empty($followerId) || empty($followedId)) {
            throw new InvalidArgumentException("Invalid arguments.");
        }
        $userDao = new UserDAO();
        $usersFollowUsersDao = new UsersFollowUsersDAO();
        $user = $userDao->find($followedId);
        if (empty($user)) {
            throw new InvalidArgumentException("Invalid user.");
        }
        $params = [
            'follower_id' => $followerId,
            'followed_id' => $followedId
        ];
        $usersFollowUsersDao->insert($params);
    }

    /**
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public function unfollow()
    {
        $getParams = $this->request->getGetParams();
        if (isset($getParams['id'])) {
            $followedId = $getParams["id"];
            $followerId = $_SESSION["logged_user"]["id"];
        }
        if (empty($followerId) || empty($followedId)) {
            throw new InvalidArgumentException("Invalid arguments.");
        }
        $userDao = new UserDAO();
        $usersFollowUsersDao = new UsersFollowUsersDAO();
        $user = $userDao->find($followedId);
        if (empty($user)) {
            throw new InvalidArgumentException("Invalid user.");
        }
        $params = [
            'follower_id' => $followerId,
            'followed_id' => $followedId
        ];
        $usersFollowUsersDao->delete($params);
    }

    /**
     * @param int $userId
     * @param int $videoId
     *
     * @return int
     *
     * @throws InvalidArgumentException
     */
    public function isReacting($userId, $videoId)
    {
        $getParams = $this->request->getGetParams();
        if (isset($getParams['video_id'])) {
            $videoId = $getParams["video_id"];
            $userId = $_SESSION["logged_user"]["id"];
        }
        if (empty($userId) || empty($videoId)) {
            throw new InvalidArgumentException("Invalid arguments.");
        }
        $usersReactVideosDao = new UsersReactVideosDAO();
        $params = [
            'user_id'  => $userId,
            'video_id' => $videoId
        ];
        $row = $usersReactVideosDao->findBy($params);
        if ($row) {
            return $row[0]["status"];
        }
        return -1;
    }

    /**
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public function reactVideo()
    {
        $getParams = $this->request->getGetParams();
        if (isset($getParams["video_id"]) && isset($getParams["status"])) {
            $videoId = $getParams["video_id"];
            $status = $getParams["status"];
        }
        $userId = $_SESSION["logged_user"]["id"];
        if (empty($videoId)) {
            throw new InvalidArgumentException("Invalid arguments.");
        }
        if (empty($userId)) {
            throw new InvalidArgumentException("Unauthorized user.");
        }
        if ($status != 1 && $status != 0) {
            throw new InvalidArgumentException("Invalid arguments.");
        }
        $videoDao = new VideoDAO();
        $video = $videoDao->getById($videoId);
        if (empty($video)) {
            throw new InvalidArgumentException("Invalid video.");
        }
        $isReacting = $this->isReacting($userId, $videoId);
        $usersReactVideosDao = new UsersReactVideosDAO();
        if ($isReacting == -1) {//if there has been no reaction
            $params = [
                'user_id'  => $userId,
                'video_id' => $videoId,
                'status'   => $status
            ];
            $usersReactVideosDao->insert($params);
        } elseif ($isReacting == $status) { //if liking liked or unliking unliked video
            $params = [
                'user_id'  => $userId,
                'video_id' => $videoId
            ];
            $usersReactVideosDao->delete($params);
        } elseif ($isReacting != $status) { //if liking disliked or disliking liked video
            $params = [
                'user_id'  => $userId,
                'video_id' => $videoId
            ];
            $usersReactVideosDao->delete($params);
            $params = [
                'user_id'  => $userId,
                'video_id' => $videoId,
                'status'   => 1 - $isReacting
            ];
            $usersReactVideosDao->insert($params);
        }
        $arr = [];
        $arr["stat"] = $this->isReacting($userId, $videoId);
        $arr["likes"] = $videoDao->getReactions($videoId, 1);
        $arr["dislikes"] = $videoDao->getReactions($videoId, 0);

        echo json_encode($arr);
    }

    /**
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public function subscriptions()
    {
        $userId = $_SESSION["logged_user"]["id"];
        if (isset($userId) && !empty($userId)) {
            $userDao = new UserDAO();

            $userExists = $userDao->find($userId);
            if (empty($userExists)) {
                throw new InvalidArgumentException("Invalid user.");
            }
            $subscriptions = $userDao->getSubscriptions($userId);

            include_once "view/subscriptions.php";
        }
    }

    /**
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public function clickedUser()
    {
        $getParams = $this->request->getGetParams();
        if (isset($getParams['id'])) {
            $followedId = $getParams['id'];
        }
        $userDao = new UserDAO();
        $user = $userDao->getFollowedUser($followedId);
        if (empty($user)) {
            throw new InvalidArgumentException("Invalid user.");
        }

        include_once "view/subscriptions.php";
    }
}
