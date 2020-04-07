<?php

namespace model;

use PDO;
use PDOStatement;

abstract class AbstractDAO
{
    /**
     * @var PDO
     */
    private $pdo;

    /**
     * @var PDOStatement
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
     * @param array  $params
     *
     * @return int
     */
    public function rowCount(string $query, array $params): int
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
    public function lastInsertId(): int
    {
        return $this->pdo->lastInsertId();
    }

    /**
     * @param string $query
     * @param array  $params
     *
     * @return void
     */
    public function prepareAndExecute(string $query, array $params = [])
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
    public function fetchAssoc(string $query, array $params = [])
    {
        $this->prepareAndExecute($query, $params);

        return $this->statement->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @param string $query
     * @param array  $params
     *
     * @return array
     */
    public function fetchAllAssoc(string $query, array $params = []): array
    {
        $this->prepareAndExecute($query, $params);

        return $this->statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param array $params
     *
     * @return int
     */
    public function insert(array $params): int
    {
        $columns = implode(', ', array_keys($params));
        $holders = implode(', :', array_keys($params));

        $query =  "
            INSERT INTO 
                 {$this->table} 
                ({$columns}) 
            VALUES 
                (:{$holders});
        ";
        $this->prepareAndExecute($query, $params);

        return $this->lastInsertId();
    }

    /**
     * @param array $params
     *
     * @return int
     */
    public function delete(array $params): int
    {
        foreach ($params as $key => $value) {
            $values[$key] = "$key = :$key";
        }
        $columns = implode(' AND ', array_values($values));

        $query = "
            DELETE FROM 
                {$this->table} 
            WHERE 
                {$columns};
        ";

        return $this->rowCount($query, $params);
    }

    /**
     * @param array $params
     * @param array $conditions
     *
     * @return int
     */
    public function update(array $params, array $conditions): int
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
                {$this->table} 
            SET 
                {$columnsAndValues}
            WHERE 
                {$condition};
        ";
        $allParams = array_merge($params, $conditions);

        return $this->rowCount($query, $allParams);
    }

    /**
     * @return array
     */
    public function findAll(): array
    {
        $query = "
            SELECT
                *
            FROM
                {$this->table};
        ";

        return $this->fetchAllAssoc($query);
    }

    /**
     * @param int $id
     *
     * @return array
     */
    public function find(int $id): array
    {
        $params['id'] = $id;
        $query = "
            SELECT
                *
            FROM
                {$this->table}
            WHERE
                id = :id;
        ";

        return $this->fetchAssoc($query, $params);
    }

    /**
     * @param array $params
     * @param bool  $fetch
     *
     * @return array
     */
    public function findBy(array $params, bool $fetch = false)
    {
        foreach ($params as $key => $value) {
            $values[$key] = "$key = :$key";
        }
        $columns = implode(' AND ', array_values($values));
        $query = "
            SELECT
                *
            FROM
                {$this->table}
            WHERE
                {$columns};
        ";
        if ($fetch) {
            return $this->fetchAssoc($query, $params);
        }

        return $this->fetchAllAssoc($query, $params);
    }
}