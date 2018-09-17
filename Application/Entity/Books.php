<?php

namespace Application\Entity;

class Books extends Base {

    const TABLE_NAME = 'books';

    public $id = '';
    public $title = '';
    public $description ='';
    public $price = '';
    public $genre = '';
    public $author = '';
    public  $discount = '';
    protected $mapping = [
        'id' => 'id',
        'title' => 'title',
        'description' => 'description',
        'price' => 'price',
        'author_name' => 'authorName',
        'genre_name' => 'genre',
        'discount' => 'discount'
    ];
    
    public function getAuthorName()
    {
        return $this->author;
    }

    public function setGenre($genre)
    {
        $this->genre = $genre;
    }
    
        public function getGenre()
    {
        return $this->genre;
    }

    public function setAuthorName($author)
    {
        $this->author = $author;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }
    
    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }
    
    public function setPrice($price)
    {
       $this->price = $price;
    }
    
    public function getPrice(): float
    {
        return $this->price;
    }
    
    public function setDiscount($discount)
    {
       $this->discount = $discount;
    }
    
    public function getDiscount(): float
    {
        return $this->discount;
    }

}
