<?php

namespace Application\Entity;

class CartBooks extends Base {

    const TABLE_NAME = 'cart';

    public $id = '';
    public $bookId = '';
    public $count = 1;
    protected $idUser = '';
    public $status = '';
    public $title = '';
    public $price = '';
    protected $mapping = [
        'id' => 'id',
        'book_id' => 'bookId',
        'count' => 'count',
        'id_user' => 'idUser',
        'status' => 'status',
        'title' => 'title',
        'price' => 'price',
        'discount' => 'discount',
        'user_discount' => 'userDiscount'
    ];

    public function getBookId(): int
    {
        return $this->bookId;
    }

    public function setBookId($id)
    {
        $this->bookId = $id;
    }

    public function getCount()
    {
        return $this->count;
    }

    public function setCount($count)
    {
        if ($count) {
            $this->count = $count;
        }
    }

    public function getIdUser(): int
    {
        return $this->idUser;
    }

    public function setIdUser($id)
    {
        $this->idUser = $id;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {

        $this->status = $status;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function setPrice($price)
    {
        $this->price = $price;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setDiscount($discount)
    {
        $this->discount = $discount;
    }

    public function getDiscount()
    {
        return $this->discount;
    }

    public function setUserDiscount($discount)
    {
        $this->userDiscount = $discount;
    }

    public function getUserDiscount()
    {
        return $this->userDiscount;
    }

}
