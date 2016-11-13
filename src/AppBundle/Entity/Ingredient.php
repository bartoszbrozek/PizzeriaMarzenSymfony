<?php

namespace AppBundle\Entity;

class Ingredient {

    private $id_ingredient;
    private $name;
    private $price;

    function getId_ingredient() {
        return $this->id_ingredient;
    }

    function getName() {
        return $this->name;
    }

    function getPrice() {
        return $this->price;
    }

    function setId_ingredient($id_ingredient) {
        $this->id_ingredient = $id_ingredient;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setPrice($price) {
        $this->price = $price;
    }

}
