<?php

namespace Application\Database;

use Application\Entity\Orders;
use PDO;

class OrdersService {

    protected $connection;
    public $lastInserId;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function fetchById($id)
    {
        $stmt = $this->connection->pdo
                ->prepare(Finder::select('orders')
                ->where('id = :id')::getSql());
        $stmt->execute(['id' => (int) $id]);
        return Orders::arrayToEntity(
                        $stmt->fetch(PDO::FETCH_ASSOC), new Orders());
    }

    public function fetchAll()
    {

        $stmt = $this->connection->pdo
                ->prepare(Finder::select('orders')::getSql());
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            yield Orders::arrayToEntity($row, new Orders());
        }
    }

    public function fetchByToken($token)
    {
        $stmt = $this->connection->pdo->prepare(
                Finder::select('users')
                        ->where('token = :token')::getSql());
        $stmt->execute(['token' => $token]);

        return Orders::arrayToEntity(
                        $stmt->fetch(PDO::FETCH_ASSOC), new Orders());
    }

    public function checkByEmail($email)
    {
        $stmt = $this->connection->pdo->prepare(
                Finder::select('users')->where('email = :email')::getSql());
        $stmt->execute(['email' => $email]);

        if ($stmt->fetch()) {

            return true;
        } else {
            return false;
        }
    }

    public function fetchByEmail($email)
    {
        $stmt = $this->connection->pdo->prepare(
                Finder::select('users')->where('email = :email')::getSql());
        $stmt->execute(['email' => $email]);
        return Orders::arrayToEntity(
                        $stmt->fetch(PDO::FETCH_ASSOC), new Orders());
    }

    public function save(Orders $obj)
    {
        if ($obj->getId() && $this->fetchById($obj->getId())) {
            return $this->doUpdate($obj);
        } else {
            return $this->doInsert($obj);
        }
    }

    protected function doUpdate($obj)
    {
        $values = $obj->entityToArray();
        $update = 'UPDATE ' . $obj::TABLE_NAME;
        $where = ' WHERE id = ' . $obj->getId();

        unset($values['id']);
        return $this->flush($update, $values, $where);
    }

    protected function doInsert($obj)
    {
        $values = $obj->entityToArray();

        unset($values['id']);
        $insert = 'INSERT INTO ' . $obj::TABLE_NAME . ' ';
        return $this->flush($insert, $values);
    }

    protected function flush($sql, $values, $where = '')
    {
        $sql .= ' SET ';
        foreach ($values as $column => $value) {
            $sql .= $column . ' = :' . $column . ',';
        }
        $sql = substr($sql, 0, -1) . $where;
        $success = FALSE;
        try {
            $stmt = $this->connection->pdo->prepare($sql);
            $stmt->execute($values);
            if ($this->connection->pdo->lastInsertId() != 0) {
                $this->lastInserId = $this->connection->pdo->lastInsertId();
                $success = TRUE;
            } else {
                $success = TRUE;
            }
        } catch (PDOException $e) {
            error_log(__METHOD__ . ':' . __LINE__ . ':'
                    . $e->getMessage());
            $success = FALSE;
        } catch (Throwable $e) {
            error_log(__METHOD__ . ':' . __LINE__ . ':'
                    . $e->getMessage());
            $success = FALSE;
        }
        return $success;
    }

    public function remove(Orders $obj)
    {
        $sql = 'DELETE FROM ' . $obj::TABLE_NAME . ' WHERE id = :id';
        $stmt = $this->connection->pdo->prepare($sql);
        $stmt->execute(['id' => $obj->getId()]);
        return ($this->fetchById($obj->getId())) ? FALSE : TRUE;
    }

}
