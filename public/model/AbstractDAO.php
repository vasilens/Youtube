<?php

namespace model;

use PDO;

abstract class AbstractDAO
{
    /**
     * @var PDO
     */
    private $pdo;

    /**
     * @var \PDOStatement
     */
    private $statement;

    /**
     * @var string
     */
    protected $table;

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
     * @param string $query
     * @param array $params
     *
     * @return int
     */
    public function rowCount($query, $params)
    {
        $this->prepareAndExecute($query, $params);
        return $this->statement->rowCount();
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
     * @param string $query
     * @param array  $params
     */
    public function prepareAndExecute($query, $params = [])
    {
        $this->statement = $this->pdo->prepare($query);
        $this->statement->execute($params);
    }

    /**
     * @param string $query
     * @param array  $params
     *
     * @return array
     */
    public function fetchAssoc($query, $params = [])
    {
        $this->prepareAndExecute($query, $params);

        return $this->statement->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * @param string $query
     * @param array  $params
     *
     * @return array
     */
    public function fetchAllAssoc($query, $params = [])
    {
        $this->prepareAndExecute($query, $params);

        return $this->statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @param array $params
     *
     * @return int
     */
    public function insert($params)
    {
        $columns = implode(', ', array_keys($params));
        $holders = implode(', :', array_keys($params));

        $query =  "
            INSERT INTO 
                 $this->table 
                ($columns) 
            VALUES 
                (:$holders);
        ";
        $this->prepareAndExecute($query, $params);

        return $this->lastInsertId();
    }

    /**
     * @param array $params
     *
     * @return int
     */
    public function delete($params)
    {
        foreach ($params as $key => $value) {
            $values[$key] = "$key = :$key";
        }
        $columns = implode(' AND ', array_values($values));

        $query = "
            DELETE FROM 
                $this->table 
            WHERE 
                $columns;
        ";

        return $this->rowCount($query, $params);
    }

    /**
     * @param array $params
     * @param array $conditions
     *
     * @return int
     */
    public function update($params, $conditions)
    {
        foreach ($params as $key => $value) {
            $parameters[$key] = "$key = :$key";
        }
        foreach ($conditions as $key => $value) {
            $cond[$key] = "$key = :$key";
        }
        $columnsAndValues = implode(', ', array_values($parameters));
        $condition = implode(', ', array_values($cond));
        $query = "
            UPDATE
                $this->table 
            SET 
                $columnsAndValues
            WHERE 
                $condition;
        ";
        $allParams = array_merge($params, $conditions);

        return $this->rowCount($query, $allParams);
    }

    /**
     * @return array
     */
    public function findAll()
    {
        $query = "
            SELECT
                *
            FROM
                $this->table;
        ";

        return $this->fetchAllAssoc($query);
    }

    /**
     * @param int $id
     *
     * @return array
     */
    public function find($id)
    {
        $params['id'] = $id;
        $query = "
            SELECT
                *
            FROM
                $this->table
            WHERE
                id = :id;
        ";

        return $this->fetchAssoc($query, $params);
    }

    /**
     * @param array $params
     *
     * @return array
     */
    public function findBy($params)
    {
        foreach ($params as $key => $value) {
            $params[$key] = "$key = :$key";
        }
        $columns = implode(' AND ', array_keys($params));
        $query = "
            SELECT
                *
            FROM
                $this->table
            WHERE
                $columns;
        ";

        return $this->fetchAllAssoc($query, $params);
    }
}