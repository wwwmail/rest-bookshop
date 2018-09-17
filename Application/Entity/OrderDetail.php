<?php

namespace Application\Entity;

class OrderDetail extends Base {

    const TABLE_NAME = 'order_detail';

    public $id = '';
    public $order_id = '';
    public $book_id = '';
    public $book_price = '';
    public $title_book = '';
    public $count = '';
    protected $mapping = [
        'id' => 'id',
        'order_id' => 'orderId',
        'book_id' => 'bookId',
        'book_price' => 'bookPrice',
        'title_book' => 'titleBook',
        'count' => 'count',
    ];

    public function getOrderId(): string
    {
        return $this->order_id;
    }

    public function setOrderId($id)
    {
        $this->order_id = $id;
    }

    public function getBookId()
    {

        return $this->book_id;
    }

    public function setBookId($id)
    {
        if ($id) {
            $this->book_id = $id;
        }
    }

    public function getBookPrice()
    {
        return $this->book_price;
    }

    public function setBookPrice($price)
    {
        $this->book_price = $price;
    }

    public function setTitleBook($title)
    {
        $this->title_book = $title;
    }

    public function getTitleBook()
    {
        return $this->title_book;
    }

    public function getCount()
    {
        return $this->count;
    }

    public function setCount($count)
    {
        $this->count = $count;
    }

}
