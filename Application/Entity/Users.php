<?php

namespace Application\Entity;

class Users extends Base {

    const TABLE_NAME = 'users';

    public $id = '';
    public $email = '';
    public $first_name = '';
    public $last_name = '';
    public $user_discount = '';
    protected $token = '';
    protected $expire = '';
    protected $password = '';
    protected $isAdmin;
    protected $mapping = [
        'id' => 'id',
        'email' => 'email',
        'first_name' => 'firstName',
        'last_name' => 'lastName',
        'token' => 'token',
        'expire' => 'expire',
        'password' => 'password',
        'user_discount' => 'discount',
        'is_admin' => 'isAdmin'
    ];

    public function getFirstName()
    {
        return $this->first_name;
    }

    public function setFirstName($name)
    {
        $this->first_name = $name;
    }

    public function getLastName()
    {
        return $this->last_name;
    }

    public function setLastName($name)
    {
        $this->last_name = $name;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {

        $this->password = $password;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function setToken($token)
    {
        //bin2hex(random_bytes(16))
        $this->token = $token;
    }

    public function getExpire()
    {
        return $this->expire;
    }

    public function setExpire($expire)
    {
//        $date = (new \DateTime());
//        $expire = $date->modify('+' . STAY_LOGINING_TIME . ' minutes')->format('Y-m-d H:i:s');
        $this->expire = $expire;
    }

    public function getDiscount(): string
    {
        return $this->user_discount;
    }

    public function setDiscount($discount)
    {
        if ($discount) {
            $this->user_discount = $discount;
        }
    }

    public function getIsAdmin()
    {
        return $this->isAdmin;
    }

    public function setIsAdmin($isAdmin)
    {
        if ($isAdmin) {
            $this->isAdmin = $isAdmin;
        }
    }

}
