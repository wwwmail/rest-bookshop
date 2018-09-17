<?php

namespace Application\Entity;

class Genres extends Base {

    const TABLE_NAME = 'genres';

    public $id = '';
    public $title = '';
   
    protected $mapping = [
        'id' => 'id',
        'title' => 'title',       
    ];
       
    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }
    
   
}
