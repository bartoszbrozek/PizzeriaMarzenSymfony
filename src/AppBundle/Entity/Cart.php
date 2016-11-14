<?php

namespace AppBundle\Entity;

class Cart {

    private $id_order;
    private $id_user;
    private $date;
    private $id_status;
    private $price;
    private $id_product;

    function getId_product() {
        return $this->id_product;
    }

    function setId_product($id_product) {
        $this->id_product = $id_product;
    }

    function getId_order() {
        return $this->id_order;
    }

    function getId_user() {
        return $this->id_user;
    }

    function getDate() {
        return $this->date;
    }

    function getId_status() {
        return $this->id_status;
    }

    function getPrice() {
        return $this->price;
    }

    function setId_order($id_order) {
        $this->id_order = $id_order;
    }

    function setId_user($id_user) {
        $this->id_user = $id_user;
    }

    function setDate($date) {
        $this->date = $date;
    }

    function setId_status($id_status) {
        $this->id_status = $id_status;
    }

    function setPrice($price) {
        $this->price = $price;
    }

}
