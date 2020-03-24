<?php

namespace model;

use PDO;

abstract class AbstractDAO
{
    /**
     * @var $pdo
     */
    public $pdo;
    public $statement;
    public $table;

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
     * @param array $params
     *
     * @return string
     */
    public function createInsertQuery($params)
    {
        $columns = implode(', ', array_keys($params));
        $holders = implode(', :', array_keys($params));

        return "INSERT INTO $this->table ($columns) VALUES (:$holders);";
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public function createDeleteQuery($params)
    {
        foreach ($params as $key=>$item) {
            $params["$key = :$key"] = $params[$key];
            unset($params[$key]);
        }
        $columns = implode(' AND ', array_keys($params));

        return "DELETE FROM $this->table WHERE $columns;";
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public function createUpdateQuery($params)
    {
        $values = [];
        $columns = [];
        foreach ($params as $key=>$item) {
            if (strpos($key, 'id') !== false) {
                $values["$key = :$key"] = $params[$key];
            } else {
                $columns["$key = :$key"] = $params[$key];
            }
        }
        $columns = implode(', ', array_keys($columns));
        $values = implode(', ', array_keys($values));

        return "UPDATE $this->table SET $columns WHERE $values;";
    }
}