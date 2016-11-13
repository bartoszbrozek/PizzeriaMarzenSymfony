<?php

namespace AppBundle\Entity;

class Product {

    private $id;
    private $name;
    private $description;
    private $price;
    private $ingredients;

    function getId() {
        return $this->id;
    }

    function getName() {
        return $this->name;
    }

    function getDescription() {
        return $this->description;
    }

    function getPrice() {
        return $this->price;
    }

    function getIngredients() {
        return $this->ingredients;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setDescription($description) {
        $this->description = $description;
    }

    function setPrice($price) {
        $this->price = $price;
    }

    function setIngredients($ingredients) {
        $this->ingredients = $ingredients;
    }

    function fetchIngredients() {
        
    }

}
