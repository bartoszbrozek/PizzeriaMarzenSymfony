<?php


namespace AppBundle\Controller;

use AppBundle\Database\Database;

class UserController {

    public function fetchUserData($id_user) {
        try {
            $pdo = new Database();
            $db = $pdo->getDb($pdo->connection());
            $query = $db->prepare("SELECT * FROM user_details WHERE id_user=?");

            $query->bindValue(1, $id_user);
            $query->execute();

            return $query->fetch();
        } catch (PDOException $ex) {
            echo $ex->getMessage();
        }
    }

}
