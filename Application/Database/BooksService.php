<?php

namespace Application\Database;

use Application\Entity\{
    Books,
    SimpleBook
};
use PDO;

class BooksService {

    protected $connection;
    public $lastInsertId;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function fetchById($id)
    {
        $stmt = $this->connection->pdo
                ->prepare(Finder::getSql("SELECT books.*, GROUP_CONCAT(DISTINCT (authors.title) SEPARATOR ', ') AS author_name, 
					GROUP_CONCAT(DISTINCT authors.id) AS author_id, 
					GROUP_CONCAT(DISTINCT genres.title SEPARATOR ', ') AS genre_name, 
					GROUP_CONCAT(DISTINCT genres.id) AS genre_id 
					FROM books
            LEFT JOIN book_to_author ON book_to_author.book_id=books.id
            LEFT JOIN authors ON authors.id=book_to_author.author_id
            LEFT JOIN book_to_genre ON book_to_genre.book_id=books.id
            LEFT JOIN genres ON genres.id=book_to_genre.genre_id
            WHERE books.id =" . $id . "
            GROUP BY books.id
            ORDER BY books.id ASC
            "));
        $stmt->execute(['id' => (int) $id]);
        return Books::arrayToEntity(
                        $stmt->fetch(PDO::FETCH_ASSOC), new Books());
    }

    public function fetchAll()
    {
        $stmt = $this->connection->pdo
                ->prepare(Finder::getSql("SELECT books.*, GROUP_CONCAT(DISTINCT authors.title SEPARATOR ', ') AS author_name, 
					GROUP_CONCAT(DISTINCT authors.id) AS author_id, 
					GROUP_CONCAT(DISTINCT genres.title SEPARATOR ', ') AS genre_name, 
					GROUP_CONCAT(DISTINCT genres.id) AS genre_id 
					FROM books
            LEFT JOIN book_to_author ON book_to_author.book_id=books.id
            LEFT JOIN authors ON authors.id=book_to_author.author_id
            LEFT JOIN book_to_genre ON book_to_genre.book_id=books.id
            LEFT JOIN genres ON genres.id=book_to_genre.genre_id
             GROUP BY books.id
            ORDER BY books.id ASC"));
        $stmt->execute();

//        echo '<pre>';
//        var_dump($stmt->fetch(PDO::FETCH_ASSOC)); die;

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            yield Books::arrayToEntity($row, new Books());
        }
    }

    public function fetchByGenre($id)
    {

        $stmt = $this->connection->pdo->prepare(
                Finder::getSql("SELECT books.*, GROUP_CONCAT(DISTINCT authors.title SEPARATOR ', ') AS author_name, 
					GROUP_CONCAT(DISTINCT authors.id) AS author_id, 
					GROUP_CONCAT(DISTINCT genres.title SEPARATOR ', ') AS genre_name, 
					GROUP_CONCAT(DISTINCT genres.id) AS genre_id 
					FROM books
            LEFT JOIN book_to_author ON book_to_author.book_id=books.id
            LEFT JOIN authors ON authors.id=book_to_author.author_id
            LEFT JOIN book_to_genre ON book_to_genre.book_id=books.id
            LEFT JOIN genres ON genres.id=book_to_genre.genre_id
            WHERE genres.id =" . $id . "
             GROUP BY books.id
            ORDER BY books.id ASC
            "));
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            yield Books::arrayToEntity($row, new Books());
        }

//        return Books::arrayToEntity(
//                        $stmt->fetch(PDO::FETCH_ASSOC), new Books());
    }

    public function fetchByAuthor($id)
    {
        $stmt = $this->connection->pdo->prepare(
                Finder::getSql("SELECT books.*, GROUP_CONCAT(DISTINCT authors.title SEPARATOR ', ') AS author_name, 
					GROUP_CONCAT(DISTINCT authors.id) AS author_id, 
					GROUP_CONCAT(DISTINCT genres.title SEPARATOR ', ') AS genre_name, 
					GROUP_CONCAT(DISTINCT genres.id) AS genre_id 
					FROM books
            LEFT JOIN book_to_author ON book_to_author.book_id=books.id
            LEFT JOIN authors ON authors.id=book_to_author.author_id
            LEFT JOIN book_to_genre ON book_to_genre.book_id=books.id
            LEFT JOIN genres ON genres.id=book_to_genre.genre_id
            WHERE authors.id =" . $id . "
            GROUP BY books.id
            ORDER BY books.id ASC
            "));



        $stmt->execute();
//        return Books::arrayToEntity(
//                        $stmt->fetch(PDO::FETCH_ASSOC), new Books());
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            yield Books::arrayToEntity($row, new Books());
        }
    }

    public function addAuthorToBook($data)
    {
       $db = DB::getInstance();

        foreach ($data['author_id'] as $item) {
            $sql = 'INSERT INTO `book_to_author` '
                    . '(book_id, author_id)'
                    . 'VALUES '
                    . '(:book_id, :author_id)';

            $result = $db->prepare($sql);
            $result->bindParam(':book_id', $data['book_id'], PDO::PARAM_INT);
            $result->bindParam(':author_id', $item, PDO::PARAM_INT);
            $result->execute();
        }

        return $result->execute();
    }

    public function getBooksByIds($arrayIds)
    {
        $ids = implode(",", $arrayIds);
        $stmt = $this->connection->pdo->prepare(
                Finder::getSql("SELECT * FROM books WHERE id IN ($ids)"));

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function save(SimpleBook $obj)
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
            //return $this->fetchByEmail($email);
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
            $this->lastInsertId = $this->connection->pdo->lastInsertId();
            //var_dump( $this->lastInsertId); die;
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

    public function remove(Books $obj)
    {
        $sql = 'DELETE FROM ' . $obj::TABLE_NAME . ' WHERE id = :id';
        $stmt = $this->connection->pdo->prepare($sql);
        $stmt->execute(['id' => $obj->getId()]);
        return ($this->fetchById($obj->getId())) ? FALSE : TRUE;
    }

}
