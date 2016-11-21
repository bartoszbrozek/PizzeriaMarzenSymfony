<?php

namespace AppBundle\Controller;

use AppBundle\Database\Database;
use AppBundle\Entity\Product;
use Symfony\Component\HttpFoundation\Session\Session;

class ProductController extends Product {

    private $session;

    public function __construct() {
        $this->session = new Session();
        ;
    }

    public function showProducts() {
        try {
            $pdo = new Database();
            $db = $pdo->getDb($pdo->connection());

            $query = $db->prepare("SELECT * FROM products");
            $query->execute();

            // return fetched products
            return $query->fetchAll();
        } catch (PDOException $ex) {
            echo $ex->getMessage();
        }
    }

    public function fetchProduct($id_product) {
        try {
            $pdo = new Database();
            $db = $pdo->getDb($pdo->connection());

            $query = $db->prepare("SELECT * FROM products p WHERE id_product=?");
            $query->bindValue(1, $id_product);
            $query->execute();

            // return fetched products
            return $query->fetchAll();
        } catch (PDOException $ex) {
            echo $ex->getMessage();
        }
    }

    public function flushCart() {
        $this->session->remove('cart');
    }

    public function removeElement($id_product) {
//        $this->session->remove('cart', $id_product);
        $this->session->remove($id_product);
    }

}
