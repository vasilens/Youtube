<?php

namespace model;

class UsersReactVideosDAO extends AbstractDAO
{

    protected function setTable()
    {
        $this->table = 'users_react_videos';
    }
}