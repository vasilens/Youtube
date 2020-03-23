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
     * @param string $table
     * @param array $columns
     * @param array $values
     * @param array $holders
     *
     * @return string
     */
    public function createInsertQuery($table, array $columns, array $holders)
    {
        $columns = implode(', ', array_values($columns));
        $holders = implode(', ', $holders);

        return "INSERT INTO $table ($columns) VALUES ($holders)";;
    }

    /**
     * @param string $table
     * @param array $columns
     *
     * @return string
     */
    public function createDeleteQuery($table, array $columns)
    {
        $columns = implode(' = ? AND', array_values($columns));
        $columns .= ' = ? AND';

        return "DELETE FROM $table WHERE $columns";
    }
    /**
     * @param string $table
     * @param array $columns
     * @param null | array
     *
     * @return string
     */
    public function createUpdateQuery($table, array $columns, $param = null)
    {
        $columns = implode(' = ?,', array_values($columns));
        $columns .= ' = ?';
        if ($param != null) {

            return "UPDATE $table SET $columns WHERE $param = ?;";
        } else {

            return "UPDATE $table SET $columns";
        }
    }
}