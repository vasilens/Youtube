<?php


namespace model;


class Playlist {
    private $id;
    private $title;
    private $owner_id;
    private $date_created;

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }
    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
    /**
     * @return mixed
     */
    public function getTitle() {
        return $this->title;
    }
    /**
     * @param mixed $title
     */
    public function setTitle($title) {
        $this->title = $title;
    }
    /**
     * @return mixed
     */
    public function getOwnerId() {
        return $this->owner_id;
    }
    /**
     * @param mixed $owner_id
     */
    public function setOwnerId($owner_id) {
        $this->owner_id = $owner_id;
    }
    /**
     * @return mixed
     */
    public function getDateCreated() {
        return $this->date_created;
    }
    /**
     * @param mixed $date_created
     */
    public function setDateCreated($date_created) {
        $this->date_created = $date_created;
    }
}