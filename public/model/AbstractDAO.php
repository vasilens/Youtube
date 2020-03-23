<?php

namespace model;

class AbstractDAO
{
    /**
     * @var \PDO
     */
    public $pdo;
    public $statement;

    /**
     * AbstractDAO constructor.
     */
    public function __construct()
    {
        $this->pdo = dbManager::getInstance()->getPDO();
    }

    /**
     * Begin Transaction
     */
    public function beginTransaction()
    {
        $this->pdo->beginTransaction();
    }

    /**
     * Commit
     */
    public function commit()
    {
        $this->pdo->commit();
    }

    /**
     * Rollback
     */
    public function rollback()
    {
        $this->pdo->rollBack();
    }

    /**
     * @return int
     */
    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }

    /**
     * @param $fetch
     *
     * @return array
     */
    public function fetch($fetch)
    {
        return $this->statement->fetch($fetch);
    }

    /**
     * @param $fetch
     *
     * @return array
     */
    public function fetchAll($fetch)
    {
        return $this->statement->fetchAll($fetch);
    }

    /**
     * @param $sql
     * @param $params
     */
    public function prepareAndExecute($sql, $params)
    {
        $this->statement = $this->pdo->prepare($sql);
        $this->statement->execute([$params]);
    }

    /**
     * @param $sql
     * @param $params
     *
     * @return array
     */
    public function fetchAssoc($sql, $params)
    {
        $this->prepareAndExecute($sql, $params);

        return $this->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * @param $sql
     * @param $params
     *
     * @return array
     */
    public function fetchAllAssoc($sql, $params)
    {
        $this->prepareAndExecute($sql, $params);

        return $this->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @param $sql
     * @param $params
     */
    public function insert($sql, $params)
    {
        $this->prepareAndExecute($sql, $params);
    }

    /**
     * @param $sql
     * @param $params
     */
    public function delete($sql, $params)
    {
        $this->prepareAndExecute($sql, $params);
    }

    /**
     * @param $sql
     * @param $params
     */
    public function update($sql, $params)
    {
        $this->prepareAndExecute($sql, $params);
    }
}