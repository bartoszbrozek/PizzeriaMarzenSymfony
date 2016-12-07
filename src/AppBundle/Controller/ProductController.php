<?php

namespace AppBundle\Controller;

use AppBundle\Database\Database;
use AppBundle\Entity\Product;
use Symfony\Component\HttpFoundation\Session\Session;

class ProductController extends Product {

    private $session;

    public function __construct() {
        $this->session = new Session();
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

    public function fetchProductIngredients($id_product) {
        try {
            $pdo = new Database();
            $db = $pdo->getDb($pdo->connection());
            $query = $db->prepare('SELECT i.name FROM products p, ingredients i, product_ingredient pr '
                    . 'WHERE pr.id_product = p.id_product '
                    . 'AND pr.id_ingredient = i.id_ingredient AND p.id_product=?');
            $query->bindValue(1, $id_product);
            $query->execute();

            return $query->fetchAll();
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }

    public function fetchIngredients() {
        try {
            $pdo = new Database();
            $db = $pdo->getDb($pdo->connection());

            $query = $db->prepare("SELECT * FROM ingredients i");
            $query->execute();

            // return fetched products
            $ingredients = $query->fetchAll();

            foreach ($ingredients as $ingredient) {
                $keys[] = $ingredient['id_ingredient'];
            }

            foreach ($ingredients as $ingredient) {
                $values[] = $ingredient['name'] . " (" . $ingredient['price'] . " zł)";
            }
            $array = array_combine($values, $keys);

            return $array;
        } catch (PDOException $ex) {
            echo $ex->getMessage();
        }
    }

    public function flushCart() {
        $this->session->remove('cart');
    }

    public function removeElement($id_product) {
        $this->session->remove($id_product);
    }

    public function getOrderDetails($orderID) {
        try {
            $pdo = new Database();
            $db = $pdo->getDb($pdo->connection());

            $query = $db->prepare("SELECT op.id_product, op.price, p.name "
                    . "FROM orders o "
                    . "LEFT JOIN order_product op ON op.id_order = o.id_order "
                    . "LEFT JOIN products p ON p.id_product = op.id_product "
                    . "WHERE o.id_order=?");
            $query->bindValue(1, $orderID);
            $query->execute();
            return $query->fetchAll();
        } catch (PDOException $ex) {
            echo $ex->getMessage();
        }
    }

    public function showOrders() {
        try {
            $pdo = new Database();
            $db = $pdo->getDb($pdo->connection());

            $query = $db->prepare("SELECT o.id_order, o.id_user, o.id_status, s.name as status_name, o.date, s.name AS status_name, s.color, u.login AS user_name, "
                    . "ud.name, ud.lastname "
                    . "FROM orders o "
                    . "LEFT JOIN status s ON s.id_status = o.id_status "
                    . "LEFT JOIN users u ON u.id_user = o.id_user "
                    . "LEFT JOIN user_details ud ON ud.id_user = u.id_user");
            $query->execute();

            $orders = $query->fetchAll();

            $orderDetails = new ProductController();
            $i = 0;
            foreach ($orders as $order) {
                $orders[$i++]['details'] = $orderDetails->getOrderDetails($order['id_order']);
//                echo $order['id_order'];
            }

            // return fetched orders
            // return $query->fetchAll();
            return $orders;
        } catch (PDOException $ex) {
            echo $ex->getMessage();
        }
    }

    public function getFullOrderDetails($orderID) {
        try {
            $pdo = new Database();
            $db = $pdo->getDb($pdo->connection());

            $query = $db->prepare("SELECT o.id_order, o.id_user, o.id_status, s.name as status_name, o.date, s.name AS status_name, s.color "
                    . "FROM orders o "
                    . "LEFT JOIN status s ON s.id_status = o.id_status "
                    . "WHERE o.id_order=?");
            $query->bindValue(1, $orderID);
            $query->execute();
            $orderDetails = $query->fetchAll();

            $query = $db->prepare("SELECT op.id_product, op.id_order, p.name "
                    . "FROM order_product op "
                    . "LEFT JOIN products p ON op.id_product = p.id_product "
                    . "WHERE op.id_order=?");

            $query->bindValue(1, $orderDetails[0]['id_order']);
            $query->execute();

            $orderDetails['order_details'] = $query->fetchAll();

            return $orderDetails;
        } catch (PDOException $ex) {
            echo $ex->getMessage();
        }
    }

}
