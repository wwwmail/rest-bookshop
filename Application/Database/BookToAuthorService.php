<?php

namespace Application\Database;

use Application\Entity\Authors;
use Application\Entity\Books;
use Application\Entity\BookToAuthor;
use PDO;

class BookToAuthorService {

    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function fetchById($id)
    {
        $stmt = $this->connection->pdo
                ->prepare(Finder::select('book_to_author')
                ->where('id = :id')::getSql());
        $stmt->execute(['id' => (int) $id]);
        return BookToAuthor::arrayToEntity(
                        $stmt->fetch(PDO::FETCH_ASSOC), new BookToAuthor());
    }

    public function fetchAll()
    {
        $stmt = $this->connection->pdo
                ->prepare(Finder::select('book_to_author')::getSql());
        $stmt->execute();


        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            yield BookToAuthor::arrayToEntity($row, new BookToAuthor());
        }
    }

    public function getAuthorsByBookId($id)
    {
        $stmt = $this->connection->pdo
                ->prepare(Finder::getSql("SELECT authors.* FROM authors 
               JOIN book_to_author
               ON authors.id = book_to_author.author_id
               WHERE book_to_author.book_id = " . $id . "
               GROUP BY authors.id"));
        $stmt->execute();


        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addAuthors($id, $array)
    {

        foreach ($array as $item) {

            $sql = 'INSERT INTO `book_to_author` '
                    . '(book_id, author_id)'
                    . 'VALUES '
                    . '(:book_id, :author_id)';

            $stmt = $this->connection->pdo
                    ->prepare(Finder::getSql($sql));
            $stmt->execute(['book_id' => (int) $id, 'author_id' => $item['id']]);
        }

        return true;
    }

    public function deleteAuthorForBook($book_id, $author_id)
    {

        $sql = 'DELETE FROM book_to_author WHERE book_id = :book_id AND author_id = :author_id';
        $stmt = $this->connection->pdo->prepare($sql);
        return $stmt->execute(['book_id' => $book_id, 'author_id' => $author_id]);
    }

    public function save(BookToAuthor $obj)
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
