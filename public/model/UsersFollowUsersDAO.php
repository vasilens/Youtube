<?php

namespace model;

class UsersFollowUsersDAO extends AbstractDAO
{
    /**
     * @return void
     */
    protected function setTable()
    {
        $this->table = 'users_follow_users';
    }
}