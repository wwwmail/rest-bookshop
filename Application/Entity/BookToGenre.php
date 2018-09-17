<?php

namespace Application\Entity;

class BookToGenre extends Base {

    const TABLE_NAME = 'book_to_genre';

    public $id = '';
    public $book_id = '';
    public $genre_id = '';


    protected $mapping = [
        'id' => 'id',
        'book_id' => 'bookId',
        'genre_id' => 'genreId',
        
    ];
       
    public function getBookId(): string
    {
        return $this->book_id;
    }

    public function setBookId($id)
    {
        $this->book_id = $id;
    }
    
    public function getGenreId()
    {
        return $this->genre_id;
    }
    
    public function setGenreId($id)
    {
        $this->genre_id = $id;
    }
    
   
}
