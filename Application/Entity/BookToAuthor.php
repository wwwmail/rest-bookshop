<?php

namespace Application\Entity;

class BookToAuthor extends Base {

    const TABLE_NAME = 'book_to_author';

    public $id = '';
    public $book_id = '';
    public $author_id = '';


    protected $mapping = [
        'id' => 'id',
        'book_id' => 'bookId',
        'author_id' => 'authorId',
        
    ];
       
    public function getBookId(): string
    {
        return $this->book_id;
    }

    public function setBookId($id)
    {
        $this->book_id = $id;
    }
    
    public function getAuthorId()
    {
        return $this->author_id;
    }
    
    public function setAuthorId($id)
    {
        $this->author_id = $id;
    }
       
}
