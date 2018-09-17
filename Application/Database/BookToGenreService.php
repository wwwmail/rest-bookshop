<?php

namespace Application\Database;

use Application\Entity\Authors;
use Application\Entity\Books;
use Application\Entity\BookToGenre;
use PDO;

class BookToGenreService {

    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function fetchById($id)
    {
        $stmt = $this->connection->pdo
                ->prepare(Finder::select('book_to_genre')
                ->where('id = :id')::getSql());
        $stmt->execute(['id' => (int) $id]);
        return BookToGenre::arrayToEntity(
                        $stmt->fetch(PDO::FETCH_ASSOC), new BookToGenre());
    }

    public function fetchAll()
    {
        $stmt = $this->connection->pdo
                ->prepare(Finder::select('book_to_genre')::getSql());
        $stmt->execute();


        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            yield BookToGenre::arrayToEntity($row, new BookToGenre());
        }
    }

    public function getGenresByBookId($id)
    {
        $stmt = $this->connection->pdo
                ->prepare(Finder::getSql(" SELECT genres.* FROM genres 
               JOIN book_to_genre
               ON genres.id = book_to_genre.genre_id
               WHERE book_to_genre.book_id = ".$id."
               GROUP BY genres.id"));
        $stmt->execute();


        return  $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        
    }

    public function addGenres($id, $array)
    {
       
        foreach ($array as $item) {
           
            $sql = 'INSERT INTO `book_to_genre` '
                    . '(book_id, genre_id)'
                    . 'VALUES '
                    . '(:book_id, :genre_id)';

            $stmt = $this->connection->pdo
                ->prepare(Finder::getSql($sql));
             $stmt->execute(['book_id' => (int) $id, 'genre_id'=> $item['id']]);
        }
        
        return true;
    }
    
    
    public function deleteGenreForBook($book_id, $genre_id)
    {
       
        $sql = 'DELETE FROM book_to_genre WHERE book_id = :book_id AND genre_id = :genre_id';
        $stmt = $this->connection->pdo->prepare($sql);
        return $stmt->execute(['book_id' => $book_id, 'genre_id' => $genre_id]);
        
    }

    public function save(BookToGenre $obj)
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
