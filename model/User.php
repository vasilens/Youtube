<?php
namespace model;

class User {

    private $id;
    private $username;
    private $email;
    private $password;
    private $full_name;
    private $registration_date;
    private $avatar_url;

    public function __construct($username=null, $email=null, $password=null,
                                $full_name=null, $registration_date=null, $avatar_url=null){
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->full_name = $full_name;
        $this->registration_date = $registration_date;
        $this->avatar_url = $avatar_url;
    }
    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }
    /**
     * @param mixed $id
     */
    public function setId($id) {
        $this->id = $id;
    }
    /**
     * @return mixed
     */
    public function getEmail() {
        return $this->email;
    }
    /**
     * @param mixed $email
     */
    public function setEmail($email) {
        $this->email = $email;
    }
    /**
     * @return mixed
     */
    public function getUsername() {
        return $this->username;
    }
    /**
     * @param mixed $username
     */
    public function setUsername($username) {
        $this->username = $username;
    }
    /**
     * @return mixed
     */
    public function getPassword() {
        return $this->password;
    }
    /**
     * @param mixed $password
     */
    public function setPassword($password) {
        $this->password = $password;
    }

    /**
     * @return null
     */
    public function getFullName()
    {
        return $this->full_name;
    }

    /**
     * @param null $full_name
     */
    public function setFullName($full_name)
    {
        $this->full_name = $full_name;
    }

    /**
     * @return mixed
     */
    public function getRegistrationDate() {
        return $this->registration_date;
    }
    /**
     * @param mixed $registration_date
     */
    public function setRegistrationDate($registration_date) {
        $this->registration_date = $registration_date;
    }
    /**
     * @return mixed
     */
    public function getAvatarUrl() {
        return $this->avatar_url;
    }
    /**
     * @param mixed $avatar_url
     */
    public function setAvatarUrl($avatar_url) {
        $this->avatar_url = $avatar_url;
    }
}

