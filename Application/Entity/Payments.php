<?php

namespace Application\Entity;

class Payments extends Base {

    const TABLE_NAME = 'orders';

    public $id = '';
    public $type = '';
    protected $mapping = [
        'id' => 'id',
        'type' => 'type',
    ];

    public function getType(): string
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

}
