<?php

namespace model;

class UsersReactVideosDAO extends AbstractDAO
{
    /**
     * @return void
     */
    protected function setTable()
    {
        $this->table = 'users_react_videos';
    }
}