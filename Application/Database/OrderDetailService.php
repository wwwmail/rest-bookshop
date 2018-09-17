<?php

namespace Application\Database;

use Application\Entity\OrderDetail;
use PDO;

class OrderDetailService {

    protected $connection;
    public $lastInserId;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function fetchById($id)
    {
        $stmt = $this->connection->pdo
                ->prepare(Finder::select('order_detail')
                ->where('id = :id')::getSql());
        $stmt->execute(['id' => (int) $id]);
        return OrderDetail::arrayToEntity(
                        $stmt->fetch(PDO::FETCH_ASSOC), new OrderDetail());
    }

    public function fetchAll()
    {
        $stmt = $this->connection->pdo
                ->prepare(Finder::select('order_detail')::getSql());
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            yield OrderDetail::arrayToEntity($row, new OrderDetail());
        }
    }

    public function fetchByOrder($order_id)
    {
        $stmt = $this->connection->pdo->prepare(
                Finder::select('order_detail')
                        ->where('order_id = :order_id')::getSql());
        $stmt->execute(['order_id' => $order_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    public function save(OrderDetail $obj)
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

    public function remove(Customer $obj)
    {
        $sql = 'DELETE FROM ' . $obj::TABLE_NAME . ' WHERE id = :id';
        $stmt = $this->connection->pdo->prepare($sql);
        $stmt->execute(['id' => $obj->getId()]);
        return ($this->fetchById($obj->getId())) ? FALSE : TRUE;
    }

}
