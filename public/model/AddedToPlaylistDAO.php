<?php

namespace model;

class AddedToPlaylistDAO extends AbstractDAO
{
    /**
     * @return void
     */
    protected function setTable()
    {
        $this->table = 'added_to_playlist';
    }
}