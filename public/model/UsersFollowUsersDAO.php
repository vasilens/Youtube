<?php

namespace model;

class UsersFollowUsersDAO extends AbstractDAO
{

    protected function setTable()
    {
        $this->table = 'users_follow_users';
    }
}