<?php

namespace model;

use PDO;

class AbstractDAO
{
    /**
     * @var $pdo
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
     * @param string $sql
     * @param array $params
     */
    public function prepareAndExecute($sql, $params = [])
    {
        $this->statement = $this->pdo->prepare($sql);
        $this->statement->execute([$params]);
    }

    /**
     * @param string $sql
     * @param array $params
     *
     * @return array
     */
    public function fetchAssoc($sql, $params = [])
    {
        $this->prepareAndExecute($sql, $params);

        return $this->statement->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * @param string $sql
     * @param array $params
     *
     * @return array
     */
    public function fetchAllAssoc($sql, $params = [])
    {
        $this->prepareAndExecute($sql, $params);

        return $this->statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @param string $sql
     * @param array $params
     */
    public function insert($sql, $params = [])
    {
        $this->prepareAndExecute($sql, $params);
    }

    /**
     * @param string $sql
     * @param array $params
     */
    public function delete($sql, $params = [])
    {
        $this->prepareAndExecute($sql, $params);
    }

    /**
     * @param string $sql
     * @param array $params
     */
    public function update($sql, $params = [])
    {
        $this->prepareAndExecute($sql, $params);
    }
}