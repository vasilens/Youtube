<?php
namespace controller;

use exceptions\AuthorizationException;
use exceptions\InvalidArgumentException;
use exceptions\InvalidFileException;
use model\User;
use model\UserDAO;
use model\VideoDAO;

class UserController {
    public function login()
    {
        if (isset($_POST['login'])) {
            if (!isset($_POST['email']) || !isset($_POST['password'])) {
                throw new InvalidArgumentException("Invalid arguments.");
            }
            $email = $_POST['email'];
            $password = $_POST['password'];
            if (empty(trim($email)) || empty(trim($password))){
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
                header("Location:index.php");
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
        if (isset($_POST['register'])) {
            $error = false;
            $msg = "";
            if (!isset($_POST["username"]) || empty(trim($_POST["username"]))) {
                $msg = "Username is empty!";
                $error = true;
            }
            elseif (!isset($_POST["full_name"]) || empty(trim($_POST["full_name"]))) {
                $msg = "Name is empty!";
                $error = true;
            }
            elseif (!isset($_POST["email"]) || empty(trim($_POST["email"]))) {
                $msg = "Email is empty!";
                $error = true;
            }
            elseif (!isset($_POST["password"]) || empty(trim($_POST["password"]))) {
                $msg = "Password is empty!";
                $error = true;
            }
            elseif (!isset($_POST["cpassword"]) || empty(trim($_POST["cpassword"]))) {
                $msg = "Confirm password is empty!";
                $error = true;
            }
            if ($error) {
                include_once "view/register.php";
                return;
            }
            $username = $_POST['username'];
            $email = $_POST['email'];
            $full_name = $_POST['full_name'];
            $password = $_POST['password'];
            $cpassword = $_POST['cpassword'];
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

    public function edit(){
        if(isset($_POST['edit'])){
            if (!isset($_SESSION["logged_user"]["id"])) {
                throw new AuthorizationException("Unauthorized user.");
            }
            $error = false;
            $msg = "";
            if (!isset($_POST["username"]) || empty(trim($_POST["username"]))) {
                $msg = "Username is empty";
                $error = true;
            }
            elseif (!isset($_POST["full_name"]) || empty(trim($_POST["full_name"]))) {
                $msg = "Name is empty";
                $error = true;
            }
            elseif (!isset($_POST["email"]) || empty(trim($_POST["email"]))) {
                $msg = "Email is empty";
                $error = true;
            }
            elseif (!isset($_POST["password"]) || empty(trim($_POST["password"]))) {
                $msg = "Password is empty";
                $error = true;
            }
            elseif ((!isset($_POST["cpassword"]) || empty(trim($_POST["cpassword"]))) &&
                (isset($_POST["new_password"]) && !empty(trim($_POST["new_password"])))) {
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
            $user = $dao->checkUsername($_POST["username"]);
            if ($user && $user["id"] != $_SESSION["logged_user"]["id"]) {
                include_once "view/editProfile.php";
                echo "User with that username already exists!";
                return;
            }
            $user = $dao->checkUser($_POST["email"]);
            if ($user && $user["id"] != $_SESSION["logged_user"]["id"]) {
                include_once "view/editProfile.php";
                echo "User with that email already exists!";
                return;
            }
            $password = $user['password'];
            if(password_verify($_POST['password'], $password)){
                $newAvatar = $this->uploadImage("avatar", $_POST['username']);
                if (!$newAvatar){
                    $newAvatar = $_SESSION["logged_user"]["avatar_url"];
                }
                $username = $_POST["username"];
                $email = $_POST["email"];
                $full_name = $_POST["full_name"];
                if(isset($_POST['new_password']) && isset($_POST['cpassword'])) {
                    $msg = $this->registerValidator($username, $email, $_POST["new_password"], $_POST["cpassword"]);
                    if ($msg){
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
            }
            else {
                include_once "view/editProfile.php";
                echo "Incorrect password!";
            }
        }
        else {
            throw new InvalidArgumentException("Invalid arguments.");
        }
    }

    public function uploadImage($file, $username){
        if (is_uploaded_file($_FILES[$file]["tmp_name"])) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $_FILES[$file]["tmp_name"]);
            if (!(in_array($mime, array ('image/bmp', 'image/jpeg', 'image/png')))){
                throw new InvalidFileException ("File is not in supported format.");
            }
            $file_name_parts = explode(".", $_FILES[$file]["name"]);
            $extension = $file_name_parts[count($file_name_parts) - 1];
            $filename = $username . "-" . time() . "." . $extension;
            $file_url = "uploads" . DIRECTORY_SEPARATOR . $filename;
            if (move_uploaded_file($_FILES[$file]["tmp_name"], $file_url)){
                return $file_url;
            }
            else {
                throw new InvalidFileException("File handling error.");
            }
        }
        return false;
    }

    public function logout(){
        unset($_SESSION);
        session_destroy();
        header("Location:index.php?target=view&action=viewRouter&view=login");
        exit;
    }

    public function registerValidator($username, $email, $password = null, $cpassword = null){
        $msg = '';
        if(strlen($username) < 8){
            $msg = "Username must be at least 8 characters! <br>";
        }
        if (!(filter_var($email, FILTER_VALIDATE_EMAIL))) {
            $msg .= " Invalid email. <br> ";
        }
        if($password != null && $cpassword != null){
            if($password === $cpassword){
                if (!(preg_match("#.*^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).*$#", $password))) {
                    $msg .= " Wrong password input. <br> Password should be at least 8 characters, including lowercase, uppercase, number and symbol. <br>";
                }
            }
            else {
                $msg .= "Passwords not matching! <br>";
            }
        }
        return $msg;
    }

    public function getById($id=null){
        if (isset($_GET["id"])) {
            $id = $_GET["id"];
        }
        if (empty($id)){
            throw new InvalidArgumentException("Invalid arguments.");
        }
        $userdao = UserDAO::getInstance();
        $user = $userdao->getById($id);
        if (empty($user)){
            throw new InvalidArgumentException("Invalid user.");
        }
        $user["id"] = $id;
        if (isset($_SESSION["logged_user"]["id"])) {
            $user["isFollowed"] = $userdao->isFollowing($_SESSION["logged_user"]["id"], $id);
        }
        $videodao = VideoDAO::getInstance();
        $videos = $videodao->getByOwnerId($id);
        include_once "view/profile.php";
    }

    public function isFollowing(){
        if (isset($_GET["id"])){
            $followed_id = $_GET["id"];
            $follower_id = $_SESSION["logged_user"]["id"];
        }
        if (empty($follower_id) || empty($followed_id)){
            throw new InvalidArgumentException("Invalid arguments.");
        }
        $dao = UserDAO::getInstance();
        return $dao->isFollowing($follower_id, $followed_id);
    }

    public function follow(){
        if (isset($_GET["id"])){
            $followed_id = $_GET["id"];
            $follower_id = $_SESSION["logged_user"]["id"];
        }
        if (empty($follower_id) || empty($followed_id)){
            throw new InvalidArgumentException("Invalid arguments.");
        }
        $dao = UserDAO::getInstance();
        $user = $dao->getById($followed_id);
        if (empty($user)){
            throw new InvalidArgumentException("Invalid user.");
        }
        $dao->followUser($follower_id, $followed_id);
    }

    public function unfollow(){
        if (isset($_GET["id"])){
            $followed_id = $_GET["id"];
            $follower_id = $_SESSION["logged_user"]["id"];
        }
        if (empty($follower_id) || empty($followed_id)){
            throw new InvalidArgumentException("Invalid arguments.");
        }
        $dao = UserDAO::getInstance();
        $user = $dao->getById($followed_id);
        if (empty($user)){
            throw new InvalidArgumentException("Invalid user.");
        }
        $dao->unfollowUser($follower_id, $followed_id);
    }

    public function isReacting($user_id=null, $video_id=null){
        if (isset($_GET["id"])){
            $video_id = $_GET["id"];
            $user_id = $_SESSION["logged_user"]["id"];
        }
        if (empty($user_id) || empty($video_id)){
            throw new InvalidArgumentException("Invalid arguments.");
        }
        $dao = UserDAO::getInstance();
        return $dao->isReacting($user_id, $video_id);
    }

    public function reactVideo(){
        if (isset($_GET["id"]) && isset($_GET["status"])){
            $video_id = $_GET["id"];
            $status = $_GET["status"];
        }
        $user_id = $_SESSION["logged_user"]["id"];
        if (empty($video_id)){
            throw new InvalidArgumentException("Invalid arguments.");
        }
        if (empty($user_id)){
            throw new InvalidArgumentException("Unauthorized user.");
        }
        if ($status != 1 && $status != 0){
            throw new InvalidArgumentException("Invalid arguments.");
        }
        $videodao = VideoDAO::getInstance();
        $video = $videodao->getById($video_id);
        if (empty($video)){
            throw new InvalidArgumentException("Invalid video.");
        }
        $isReacting = $this->isReacting($user_id, $video_id);
        $userdao = UserDAO::getInstance();
        if ($isReacting == -1) {//if there has been no reaction
            $userdao->reactVideo($user_id, $video_id, $status);
        } elseif ($isReacting == $status) { //if liking liked or unliking unliked video
            $userdao->unreactVideo($user_id, $video_id);
        } elseif ($isReacting != $status) { //if liking disliked or disliking liked video
            $userdao->unreactVideo($user_id, $video_id);
            $userdao->reactVideo($user_id, $video_id, 1 - $isReacting);
        }
        $arr = [];
        $arr["stat"] = $this->isReacting();
        $arr["likes"] = $videodao->getReactions($video_id, 1);
        $arr["dislikes"] = $videodao->getReactions($video_id, 0);
        echo json_encode($arr);
    }

    public function subscriptions(){
        if(isset($_GET['user_id'])){
            $user_id = $_GET['user_id'];
        }
        else {
            if (isset($_SESSION["logged_user"]["id"])){
                $user_id = $_SESSION["logged_user"]["id"];
            }
        }
        if (isset($user_id) && !empty($user_id)) {
            $dao = UserDAO::getInstance();
            $userexists = $dao->getById($user_id);
            if (empty($userexists)){
                throw new InvalidArgumentException("Invalid user.");
            }
            $subscriptions = $dao->getSubscriptions($user_id);
            include_once "view/subscriptions.php";
        }
        else {
            include_once "view/subscriptions.php";
            echo "<h3>Login to view your subscriptions!</h3>";
        }

    }
    public function clickedUser(){
        if(isset($_GET['id'])){
            $followed_id = $_GET['id'];
        }
        $dao = UserDAO::getInstance();
        $user = $dao->getFollowedUser($followed_id);
        if (empty($user)){
            throw new InvalidArgumentException("Invalid user.");
        }
        include_once "view/subscriptions.php";
    }
}
