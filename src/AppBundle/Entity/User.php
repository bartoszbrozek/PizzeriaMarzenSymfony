<?php

namespace AppBundle\Entity;

//use Symfony\Component\Security\Core\User\UserInterface;

class User {

    private $username;
    private $password;

    public function getUsername() {
        return $this->username;
    }

    public function getPassword() {
        return $this->password;
    }

    public function eraseCredentials() {
        
    }

    public function getRoles() {
        return ['USER_ROLE'];
    }

    public function getSalt() {
        
    }

    public function setUsername($username) {
        $this->username = $username;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

}
