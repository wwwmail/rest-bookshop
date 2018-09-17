<?php

namespace Application\Entity;

class Orders extends Base {

    const TABLE_NAME = 'orders';

    public $id = '';
    public $user_id = '';
    public $created = '';
    public $status = '';
    public $paiment_id = '';
    public $total_order = '';
    protected $is_paymented = '';
    protected $mapping = [
        'id' => 'id',
        'user_id' => 'userId',
        'created' => 'created',
        'status' => 'status',
        'payment_id' => 'paymentId',
        'total_order' => 'totalOrder'
    ];

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): string
    {
        return $this->user_id;
    }

    public function setUserId($id)
    {
        $this->user_id = $id;
    }

    public function getCreated()
    {

        return $this->created;
    }

    public function setCreated($data)
    {
        if ($data) {
            $this->created = $data;
        }
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getPaymentId()
    {
        return $this->payment_id;
    }

    public function setPaymentId($id)
    {

        $this->payment_id = $id;
    }

    public function setTotalOrder($total)
    {
        $this->total_order = $total;
    }

    public function getTotalOrder()
    {
        return $this->total_order;
    }

}
