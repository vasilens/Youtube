<?php

namespace services;

use model\User;
use model\UserDAO;

class UserService extends AbstractService
{

    protected function setDao()
    {
        $this->dao = new UserDAO();
    }

    public function login(array $postParams)
    {
        $params = [
            'email' => $postParams['email']
        ];
        $user = $this->dao->findBy($params, true);
        if (!$user) {
            $msg = "Invalid password or email! Try again.";

            include_once "view/login.php";

        }
        if (password_verify($postParams['password'], $user['password'])) {
            $user['full_name'] = $user['name'];
            unset($user["password"]);
            $_SESSION['logged_user'] = $user;
            header("Location:/");

            echo "Successful login! <br>";
        }
        else {
            $msg = "Invalid password or email! Try again.";

            include_once "view/login.php";
        }
    }

    public function register(string $avatarUrl, array $postParams)
    {
        $username = $postParams['username'];
        $email = $postParams['email'];
        $fullName = $postParams['full_name'];
        $password = $postParams['password'];
        $cpassword = $postParams['cpassword'];

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