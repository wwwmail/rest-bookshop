<?php

namespace Application\Database;

use Application\Entity\Authors;
use Application\Entity\Books;
use PDO;

class AuthorsService {

    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function fetchById($id)
    {
        $stmt = $this->connection->pdo
                ->prepare(Finder::select('authors')
                ->where('id = :id')::getSql());
        $stmt->execute(['id' => (int) $id]);
        return Authors::arrayToEntity(
                        $stmt->fetch(PDO::FETCH_ASSOC), new Authors());
    }

    public function fetchAll()
    {
        $stmt = $this->connection->pdo
                ->prepare(Finder::select('authors')::getSql());
        $stmt->execute();


        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            yield Authors::arrayToEntity($row, new Authors());
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
        $stmt->execute(['genres.id']);
        return Books::arrayToEntity(
                        $stmt->fetch(PDO::FETCH_ASSOC), new Books());
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
        return Books::arrayToEntity(
                        $stmt->fetch(PDO::FETCH_ASSOC), new Books());
    }

    public function fetchByBook($id)
    {
        $stmt = $this->connection->pdo->prepare(
                Finder::getSql(" SELECT authors.* FROM authors 
               JOIN book_to_author
               ON authors.id = book_to_author.author_id
               WHERE book_to_author.book_id = " . $id . "
               GROUP BY authors.id
            "));

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function save(Authors $obj)
    {

        if ($obj->getId() && $this->fetchById($obj->getId())) {
            return $this->doUpdate($obj);
        } else {
            return $this->doInsert($obj);
        }
    }

    protected function doUpdate($obj)
    {
// get properties in the form of an array
        $values = $obj->entityToArray();
// build the SQL statement
        $update = 'UPDATE ' . $obj::TABLE_NAME;
        $where = ' WHERE id = ' . $obj->getId();
// unset ID as we want do not want this to be updated
        unset($values['id']);
        return $this->flush($update, $values, $where);
    }

    protected function doInsert($obj)
    {
        $values = $obj->entityToArray();

        unset($values['id']);
        $insert = 'INSERT INTO ' . $obj::TABLE_NAME . ' ';
        if ($this->flush($insert, $values)) {
            //  return $this->fetchByEmail($email);
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

    public function remove(Authors $obj)
    {
        $sql = 'DELETE FROM ' . $obj::TABLE_NAME . ' WHERE id = :id';
        $stmt = $this->connection->pdo->prepare($sql);
        $stmt->execute(['id' => $obj->getId()]);
        return ($this->fetchById($obj->getId())) ? FALSE : TRUE;
    }

}
