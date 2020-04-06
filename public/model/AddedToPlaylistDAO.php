<?php

namespace model;

class AddedToPlaylistDAO extends AbstractDAO
{

    protected function setTable()
    {
        $this->table = 'added_to_playlist';
    }
}