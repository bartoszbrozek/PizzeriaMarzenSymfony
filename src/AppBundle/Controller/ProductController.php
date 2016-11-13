<?php

namespace AppBundle\Controller;

use AppBundle\Database\Database;
use AppBundle\Entity\Product;

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

}
