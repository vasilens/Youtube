<?php

namespace model;

use PDO;

abstract class AbstractDAO
{
    /**
     * @var instance
     */
    private $pdo;

    /**
     * @var \PDOStatement
     */
    private $statement;

    /**
     * @var string
     */
    private $table;

    abstract protected function setTable();

    /**
     * AbstractDAO constructor.
     */
    public function __construct()
    {
        $this->setTable();
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
     * @param array  $params
     */
    public function prepareAndExecute($sql, $params = [])
    {
        $this->statement = $this->pdo->prepare($sql);
        $this->statement->execute($params);
    }

    /**
     * @param string $sql
     * @param array  $params
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
     * @param array  $params
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

        return "INSERT INTO 
                    $this->table 
                    ($columns) 
                VALUES 
                    (:$holders);";
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public function createDeleteQuery($params)
    {
        foreach ($params as $key=>$value) {
            $params[$key] = "$key = :$key";
        }
        $columns = implode(' AND ', array_keys($params));

        return "DELETE FROM 
                    $this->table 
                WHERE 
                    $columns;";
    }

    /**
     * @param array $params
     * @param array $conditions
     *
     * @return string
     */
    public function createUpdateQuery($params, $conditions)
    {
        $params = implode(', :', array_keys($params));
        $conditions = implode(', :', array_keys($conditions));

        return "UPDATE
                    $this->table 
                SET 
                    :$params 
                WHERE 
                    :$conditions;";
    }
}