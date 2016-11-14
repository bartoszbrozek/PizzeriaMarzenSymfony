<?php

namespace AppBundle\Controller;

use AppBundle\Database\Database;
use AppBundle\Entity\Product;
use Symfony\Component\HttpFoundation\Session\Session;

class ProductController extends Product {

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
        $session = new Session();
        $session->remove('cart');
    }

}
