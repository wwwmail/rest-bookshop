<?php

namespace Application\Database;

use Application\Entity\Genres;
use PDO;

class GenresService {

    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function fetchById($id)
    {
        $stmt = $this->connection->pdo
                ->prepare(Finder::select('genres')
                ->where('id = :id')::getSql());
        $stmt->execute(['id' => (int) $id]);
        return Genres::arrayToEntity(
                        $stmt->fetch(PDO::FETCH_ASSOC), new Genres());
    }

    public function fetchAll()
    {
        $stmt = $this->connection->pdo
                ->prepare(Finder::select('genres')::getSql());
        $stmt->execute();


        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            yield Genres::arrayToEntity($row, new Genres());
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
                Finder::getSql("  SELECT genres.* FROM genres 
               JOIN book_to_genre
               ON genres.id = book_to_genre.genre_id
               WHERE book_to_genre.book_id = " . $id . "
               GROUP BY genres.id
            "));

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function save(Genres $obj)
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
            // return $this->fetchByEmail($email);
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

    public function remove(Genres $obj)
    {
        $sql = 'DELETE FROM ' . $obj::TABLE_NAME . ' WHERE id = :id';
        $stmt = $this->connection->pdo->prepare($sql);
        $stmt->execute(['id' => $obj->getId()]);
        return ($this->fetchById($obj->getId())) ? FALSE : TRUE;
    }

}
