<?php


namespace model;


class Comment
{
    private $id;
    private $content;
    private $date;
    private $video_id;
    private $owner_id;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate($date)
    {
        $this->date = $date;
    }

    public function getVideoId()
    {
        return $this->video_id;
    }

    public function setVideoId($video_id)
    {
        $this->video_id = $video_id;
    }

    public function getOwnerId()
    {
        return $this->owner_id;
    }

    public function setOwnerId($owner_id)
    {
        $this->owner_id = $owner_id;
    }


}