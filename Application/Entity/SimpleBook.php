<?php

namespace Application\Entity;

class SimpleBook extends Base {

    const TABLE_NAME = 'books';

    public $id = '';
    public $title = '';
    public $description = '';
    public $price = '';
    public $discount = '';
    protected $mapping = [
        'id' => 'id',
        'title' => 'title',
        'description' => 'description',
        'price' => 'price',
        'discount' => 'discount'
    ];

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
