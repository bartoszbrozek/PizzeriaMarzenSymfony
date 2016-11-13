<?php

namespace AppBundle\Entity;

//use Symfony\Component\Security\Core\User\UserInterface;

class User {

    private $username;
    private $password;
    private $name;
    private $lastName;
    private $phoneNumber;
    private $city;
    private $postalCode;
    private $street;
    private $flatNumber;
    private $houseNumber;

    function getName() {
        return $this->name;
    }

    function getLastName() {
        return $this->lastName;
    }

    function getPhoneNumber() {
        return $this->phoneNumber;
    }

    function getCity() {
        return $this->city;
    }

    function getPostalCode() {
        return $this->postalCode;
    }

    function getStreet() {
        return $this->street;
    }

    function getFlatNumber() {
        return $this->flatNumber;
    }

    function getHouseNumber() {
        return $this->houseNumber;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setLastName($lastName) {
        $this->lastName = $lastName;
    }

    function setPhoneNumber($phoneNumber) {
        $this->phoneNumber = $phoneNumber;
    }

    function setCity($city) {
        $this->city = $city;
    }

    function setPostalCode($postalCode) {
        $this->postalCode = $postalCode;
    }

    function setStreet($street) {
        $this->street = $street;
    }

    function setFlatNumber($flatNumber) {
        $this->flatNumber = $flatNumber;
    }

    function setHouseNumber($houseNumber) {
        $this->houseNumber = $houseNumber;
    }

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
