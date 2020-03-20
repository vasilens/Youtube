<?php

namespace controller;

use exceptions\AuthorizationException;
use exceptions\InvalidArgumentException;
use exceptions\InvalidFileException;
use model\User;
use model\UserDAO;
use model\VideoDAO;

class UserController extends AbstractController {
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
            $dao = UserDAO::getInstance();
            $user = $dao->checkUser($email);
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
            $full_name = $postParams['full_name'];
            $password = $postParams['password'];
            $cpassword = $postParams['cpassword'];
            $msg = $this->registerValidator($username, $email, $password, $cpassword);
            if ($msg != '') {
                include_once "view/register.php";
                return;
            }
            $dao = UserDAO::getInstance();
            $user = $dao->checkUser($email);
            if ($user) {
                $msg = "User with that email already exists!";
                include_once "view/login.php";
                return;
            }
            $user = $dao->checkUsername($username);
            if ($user) {
                $msg = "User with that username already exists!";
                include_once "view/login.php";
                return;
            }
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
            $registration_date = date("Y-m-d H:i:s");
            $avatar_url = $this->uploadImage("avatar", $_POST['username']);
            $user = new User($username, $email, $password, $full_name, $registration_date, $avatar_url);
            $dao = UserDAO::getInstance();
            $dao->registerUser($user);
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
            $dao = UserDAO::getInstance();
            $user = $dao->checkUser($_SESSION["logged_user"]["email"]);
            if (empty($user)) {
                throw new AuthorizationException("Unauthorized user.");
            }
            $user = $dao->checkUsername($postParams["username"]);
            if ($user && $user["id"] != $_SESSION["logged_user"]["id"]) {
                include_once "view/editProfile.php";
                echo "User with that username already exists!";
                return;
            }
            $user = $dao->checkUser($postParams["email"]);
            if ($user && $user["id"] != $_SESSION["logged_user"]["id"]) {
                include_once "view/editProfile.php";
                echo "User with that email already exists!";
                return;
            }
            $password = $user['password'];
            if(password_verify($postParams['password'], $password)){
                $newAvatar = $this->uploadImage("avatar", $postParams['username']);
                if (!$newAvatar){
                    $newAvatar = $_SESSION["logged_user"]["avatar_url"];
                }
                $username = $postParams["username"];
                $email = $postParams["email"];
                $full_name = $postParams["full_name"];
                if (isset($postParams['new_password']) && isset($postParams['cpassword'])) {
                    $msg = $this->registerValidator($username, $email, $postParams["new_password"], $postParams["cpassword"]);
                    if ($msg) {
                        include_once "view/editProfile.php";
                        echo $msg;
                        return;
                    }
                    $password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
                }
                $user = new User($username, $email, $password, $full_name, null, $newAvatar);
                $user->setId($_SESSION['logged_user']['id']);
                $dao->editUser($user);
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

    public function uploadImage($file, $username)
    {
        if (is_uploaded_file($_FILES[$file]["tmp_name"])) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $_FILES[$file]["tmp_name"]);
            if (!(in_array($mime, array ('image/bmp', 'image/jpeg', 'image/png')))) {
                throw new InvalidFileException ("File is not in supported format.");
            }
            $file_name_parts = explode(".", $_FILES[$file]["name"]);
            $extension = $file_name_parts[count($file_name_parts) - 1];
            $filename = $username . "-" . time() . "." . $extension;
            $file_url = "uploads" . DIRECTORY_SEPARATOR . $filename;
            if (move_uploaded_file($_FILES[$file]["tmp_name"], $file_url)) {
                return $file_url;
            } else {
                throw new InvalidFileException("File handling error.");
            }
        }
        return false;
    }

    public function logout()
    {
        unset($_SESSION);
        session_destroy();
        header("Location:/");
        exit;
    }

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

    public function getById()
    {
        $getParams = $this->request->getGetParams();
        if (isset($getParams['id'])) {
            $id = $getParams['id'];
        }
        if (empty($id)) {
            throw new InvalidArgumentException("Invalid arguments.");
        }
        $userdao = UserDAO::getInstance();
        $user = $userdao->getById($id);
        if (empty($user)) {
            throw new InvalidArgumentException("Invalid user.");
        }
        $user["id"] = $id;
        $user["isFollowed"] = $userdao->isFollowing($_SESSION["logged_user"]["id"], $id);
        $videodao = VideoDAO::getInstance();
        $videos = $videodao->getByOwnerId($id);
        include_once "view/profile.php";
    }

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
        $dao = UserDAO::getInstance();
        return $dao->isFollowing($followerId, $followedId);
    }

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
        $dao = UserDAO::getInstance();
        $user = $dao->getById($followedId);
        if (empty($user)) {
            throw new InvalidArgumentException("Invalid user.");
        }
        $dao->followUser($followerId, $followedId);
    }

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
        $dao = UserDAO::getInstance();
        $user = $dao->getById($followedId);
        if (empty($user)) {
            throw new InvalidArgumentException("Invalid user.");
        }
        $dao->unfollowUser($followerId, $followedId);
    }

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
        $dao = UserDAO::getInstance();
        return $dao->isReacting($userId, $videoId);
    }

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
        $videodao = VideoDAO::getInstance();
        $video = $videodao->getById($videoId);
        if (empty($video)) {
            throw new InvalidArgumentException("Invalid video.");
        }
        $isReacting = $this->isReacting($userId, $videoId);
        $userdao = UserDAO::getInstance();
        if ($isReacting == -1) {//if there has been no reaction
            $userdao->reactVideo($userId, $videoId, $status);
        } elseif ($isReacting == $status) { //if liking liked or unliking unliked video
            $userdao->unreactVideo($userId, $videoId);
        } elseif ($isReacting != $status) { //if liking disliked or disliking liked video
            $userdao->unreactVideo($userId, $videoId);
            $userdao->reactVideo($userId, $videoId, 1 - $isReacting);
        }
        $arr = [];
        $arr["stat"] = $this->isReacting($userId, $videoId);
        $arr["likes"] = $videodao->getReactions($videoId, 1);
        $arr["dislikes"] = $videodao->getReactions($videoId, 0);
        echo json_encode($arr);
    }

    public function subscriptions()
    {
        $userId = $_SESSION["logged_user"]["id"];
        if (isset($userId) && !empty($userId)) {
            $dao = UserDAO::getInstance();
            $userexists = $dao->getById($userId);
            if (empty($userexists)) {
                throw new InvalidArgumentException("Invalid user.");
            }
            $subscriptions = $dao->getSubscriptions($userId);
            include_once "view/subscriptions.php";
        }
    }
    public function clickedUser()
    {
        $getParams = $this->request->getGetParams();
        if (isset($getParams['id'])) {
            $followedId = $getParams['id'];
        }
        $dao = UserDAO::getInstance();
        $user = $dao->getFollowedUser($followedId);
        if (empty($user)) {
            throw new InvalidArgumentException("Invalid user.");
        }
        include_once "view/subscriptions.php";
    }
}
