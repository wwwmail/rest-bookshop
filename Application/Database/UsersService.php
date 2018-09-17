<?php

namespace Application\Database;

use Application\Entity\Users;
use PDO;
class UsersService {

    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }


    public function fetchById($id)
    {
        $stmt = $this->connection->pdo
                ->prepare(Finder::select('users')
                ->where('id = :id')::getSql());
        $stmt->execute(['id' => (int) $id]);
        return Users::arrayToEntity(
                        $stmt->fetch(PDO::FETCH_ASSOC), new Users());
    }
    
    
    public function fetchAll()
    {
        $stmt = $this->connection->pdo
                ->prepare(Finder::select('users')::getSql());
        $stmt->execute();
 
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            yield Users::arrayToEntity($row, new Users());
        }

    }

    public function fetchByToken($token)
    {
        $stmt = $this->connection->pdo->prepare(
                Finder::select('users')
                        ->where('token = :token')::getSql());
        $stmt->execute(['token' => $token]);
        
        return Users::arrayToEntity(
                        $stmt->fetch(PDO::FETCH_ASSOC), new Users());
    }

    public function checkByEmail($email)
    {
        $stmt = $this->connection->pdo->prepare(
                Finder::select('users')->where('email = :email')::getSql());
        $stmt->execute(['email' => $email]);
        
        if($stmt->fetch()){
        
        return true;
        }else{
            return false;
        }
    }
    
    public function fetchByEmail($email)
    {
        $stmt = $this->connection->pdo->prepare(
                Finder::select('users')->where('email = :email')::getSql());
        $stmt->execute(['email' => $email]);
        
        return Users::arrayToEntity(
                        $stmt->fetch(PDO::FETCH_ASSOC), new Users());
       
    }
    
    

    public function save(Users $cust)
    {
        if ($cust->getId() && $this->fetchById($cust->getId())) {
            return $this->doUpdate($cust);
        } else {
            return $this->doInsert($cust);
        }
    }

    protected function doUpdate($cust)
    {
        $values = $cust->entityToArray();
        $update = 'UPDATE ' . $cust::TABLE_NAME;
        $where = ' WHERE id = ' . $cust->getId();
        unset($values['id']);
        return $this->flush($update, $values, $where);
    }

    protected function doInsert($cust)
    {
        $values = $cust->entityToArray();
        $email = $cust->getEmail();
        unset($values['id']);
        $insert = 'INSERT INTO ' . $cust::TABLE_NAME . ' ';
        if ($this->flush($insert, $values)) {
            return true;
        } else {
            return FALSE;
        }
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
            $success = TRUE;
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

    public function remove(Users $cust)
    {
        $sql = 'DELETE FROM ' . $cust::TABLE_NAME . ' WHERE id = :id';
        $stmt = $this->connection->pdo->prepare($sql);
        $stmt->execute(['id' => $cust->getId()]);
        return ($this->fetchById($cust->getId())) ? FALSE : TRUE;
    }

}
