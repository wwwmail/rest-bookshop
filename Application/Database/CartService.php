<?php

namespace Application\Database;

use Application\Entity\Cart;
use Application\Entity\CartBooks;
use Application\Entity\Books;
use PDO;

class CartService {

    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function fetchById($id)
    {
        // echo $id; die;
        $status = 'open';
        $stmt = $this->connection->pdo
                ->prepare(Finder::select('cart')
                ->where('id = :id')
                ->and('status = :status')::getSql());
        $stmt->execute(['id' => (int) $id, 'status' => $status]);

        return Cart::arrayToEntity(
                        $stmt->fetch(PDO::FETCH_ASSOC), new Cart());
    }

    public function fetchByIdAndUserId($bookId, $userId)
    {
        $stmt = $this->connection->pdo
                ->prepare(Finder::select('cart')
                ->where('book_id = :book_id')
                ->and('id_user = id_user')::getSql());
        $stmt->execute(['book_id' => (int) $bookIdid, 'id_user' => $userId]);
        return Cart::arrayToEntity(
                        $stmt->fetch(PDO::FETCH_ASSOC), new Cart());
    }

    public function fetchAll($id)
    {
        //was right join JOIN cart ON cart.book_id=books.id
        $stmt = $this->connection->pdo
                ->prepare(Finder::getSql("SELECT books.* ,cart.*, users.*, GROUP_CONCAT(DISTINCT authors.title SEPARATOR ', ') AS author_name, 
					GROUP_CONCAT(DISTINCT authors.id) AS author_id, 
					GROUP_CONCAT(DISTINCT genres.title SEPARATOR ', ') AS genre_name, 
					GROUP_CONCAT(DISTINCT genres.id) AS genre_id 
					FROM books
            LEFT JOIN cart ON cart.book_id=books.id
            LEFT JOIN users ON users.id = cart.id_user
            LEFT JOIN book_to_author ON book_to_author.book_id=books.id
            LEFT JOIN authors ON authors.id=book_to_author.author_id
            LEFT JOIN book_to_genre ON book_to_genre.book_id=books.id
            LEFT JOIN genres ON genres.id=book_to_genre.genre_id
             WHERE cart.id_user =" . $id . "
                 AND cart.status = 'open'
             GROUP BY books.id
            ORDER BY books.id ASC"));
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            yield CartBooks::arrayToEntity($row, new CartBooks());
        }

    }

    public function checkCart($id_user, $book_id)
    {
        $sql = "SELECT * from  cart WHERE book_id =:book_id "
                . "AND id_user = :id_user";

        $stmt = $this->connection->pdo
                ->prepare($sql);

        $stmt->execute(['book_id' => (int) $book_id, 'id_user' => $id_user]);

        return Cart::arrayToEntity(
                        $stmt->fetch(PDO::FETCH_ASSOC), new Cart());
    }

    public function getByUserId($id)
    {
        $sql = "SELECT * from  cart WHERE id_user = :id_user ";
        
        $stmt = $this->connection->pdo
                ->prepare($sql);
        $stmt->execute(['id_user' => $id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function save(Cart $obj)
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

    public function removeByUserId($id_user)
    {
        $sql = 'DELETE FROM cart WHERE id_user = :id_user';
 
        $stmt = $this->connection->pdo->prepare($sql);
        return $stmt->execute(['id_user' => $id_user]);
    }

    public function remove(Cart $obj)
    {
        $sql = 'DELETE FROM ' . $obj::TABLE_NAME . ' WHERE id = :id';
        $stmt = $this->connection->pdo->prepare($sql);
        $stmt->execute(['id' => $obj->getId()]);
        return ($this->fetchById($obj->getId())) ? FALSE : TRUE;
    }

}
