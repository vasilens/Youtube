<?php
namespace model;

class BaseDao {

    public function getPDO()
    {
        return dbManager::getInstance()->getPDO();
    }
}