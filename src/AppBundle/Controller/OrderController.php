<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Database\Database;
use AppBundle\Controller\CartController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class OrderController extends Controller {

    private $errorInfo;
    private $session;
    private $cart;

    public function __construct() {
        $this->session = new Session();
        $this->cart = $this->session->get('cart');
    }

    /**
     * @Route("order")
     */
    public function finalizeOrderAction(Request $request) {

        // Create a new session and get user id
        $session = new Session();
        $id = $session->get('id_user');

        if (empty($id)) {
            $this->errorInfo = 'Zaloguj się aby uzyskać dostęp do swojego konta.';
            return $this->redirectToRoute('login');
        }

        // Create a new database connection
        try {
            $pdo = new Database();
            $db = $pdo->getDb($pdo->connection());
            $query = $db->prepare("SELECT * FROM user_details WHERE id_user=?");

            $query->bindValue(1, $id);
            $query->execute();

            $userData = $query->fetch();
        } catch (PDOException $ex) {
            echo $ex->getMessage();
        }

        // Create new form for updating account details
        $user = new User();

        $form = $this->createFormBuilder($user)
                ->add('name', TextType::class, array(
                    'label' => 'Imię',
                    'data' => $userData['name']
                ))
                ->add('lastName', TextType::class, array(
                    'label' => 'Nazwisko',
                    'data' => $userData['lastName']
                ))
                ->add('phoneNumber', NumberType::class, array(
                    'label' => 'Numer telefonu',
                    'data' => $userData['phoneNumber']
                ))
                ->add('city', TextType::class, array(
                    'label' => 'Miasto',
                    'data' => $userData['city']
                ))
                ->add('postalCode', TextType::class, array(
                    'label' => 'Kod pocztowy',
                    'data' => $userData['postalCode']
                ))
                ->add('street', TextType::class, array(
                    'label' => 'Ulica',
                    'data' => $userData['street']
                ))
                ->add('flatNumber', NumberType::class, array(
                    'label' => 'Numer domu',
                    'data' => $userData['houseNumber']
                ))
                ->add('houseNumber', NumberType::class, array(
                    'label' => 'Numer mieszkania',
                    'data' => $userData['flatNumber']
                ))
                ->add('submit', SubmitType::class, [
                    'attr' => ['class' => 'btn btn-success width-100'],
                    'label' => 'Finalizuj zamówienie'])
                ->getForm();

        $form->handleRequest($request);

        // Check if form is valid and submitted,
        // then insert/update user data
        if ($form->isValid() && $form->isSubmitted()) {

            $id_user = $session->get('id_user');
            $user = $form->getData();

            // CHANGE USER DATA IF USER MADE ANY CHANGES IN FORM
            try {
                $db->beginTransaction();
                $query = $db->prepare("INSERT INTO user_details (id_user, name, lastName, phoneNumber, city, postalCode, street, flatNumber, houseNumber) "
                        . "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?) "
                        . "ON DUPLICATE KEY "
                        . "UPDATE name = ?, lastName = ?, phoneNumber = ?, city = ?, postalCode = ?, street = ?, flatNumber = ?, houseNumber = ?");

                $query->bindValue(1, $id_user);
                $query->bindValue(2, $user->getName());
                $query->bindValue(3, $user->getLastName());
                $query->bindValue(4, $user->getPhoneNumber());
                $query->bindValue(5, $user->getCity());
                $query->bindValue(6, $user->getPostalCode());
                $query->bindValue(7, $user->getStreet());
                $query->bindValue(8, $user->getFlatNumber());
                $query->bindValue(9, $user->getHouseNumber());

                $query->bindValue(10, $user->getName());
                $query->bindValue(11, $user->getLastName());
                $query->bindValue(12, $user->getPhoneNumber());
                $query->bindValue(13, $user->getCity());
                $query->bindValue(14, $user->getPostalCode());
                $query->bindValue(15, $user->getStreet());
                $query->bindValue(16, $user->getFlatNumber());
                $query->bindValue(17, $user->getHouseNumber());

                $query->execute();


                // FINALIZE ORDER - INSERT PRODUCTS TO DB
                $query = $db->prepare("INSERT INTO orders (id_user, date) VALUES (?,NOW())");
                $query->bindValue(1, $id_user);
//                $query->bindValue(2, $date);
                $query->execute();
                $orderID = $db->lastInsertId();

                //Insert order detalis into db
                foreach ($this->cart as $product) {
                    foreach ($product as $specifiedProduct) {
                        try {
                            $query = $db->prepare("INSERT INTO order_product (id_order, id_product, price) VALUES (?,?,?)");
                            $query->bindValue(1, $orderID);
                            $query->bindValue(2, $product[0]['id_product']);
                            $query->bindValue(3, $product[0]['price']);
                            $query->execute();
                        } catch (PDOException $ex) {
                            echo $ex->getMessage();
                        }
                    }
                }


                $db->commit();

                $this->errorInfo = 'Zamówienie zostało zrealizowane.';
                return $this->redirectToRoute('order_finalize');
            } catch (PDOException $ex) {
                $db->rollBack();
                echo $ex->getMessage();
            }
        }
        return $this->render('default/order/order.html.twig', [
                    'cart' => $this->cart,
                    'form' => $form->createView(),
                    'errorInfo' => $this->errorInfo
                        ]
        );
    }

    /**
     * @Route("/order/finalize")
     */
    public function orderFinalizeAction(Request $request) {
        $session = new Session();

        return $this->render('default/order/order_status.html.twig');
    }

    /**
     * @Route("/admin/orders")
     */
    public function showOrdersAction(Request $request) {

        $orders = new ProductController();
        $ordersContainer = $orders->showOrders();

        return $this->render('default/admin/orders/orders.html.twig', [
                    'orders' => $ordersContainer,
                    'errorInfo' => $this->errorInfo
                        ]
        );
    }

    /**
     * @Route("/admin/orders/{id_order}")
     */
    public function showOrderDetailsAction(Request $request, $id_order) {

        $orders = new ProductController();
        $orderDetails = $orders->getFullOrderDetails($id_order);

        $user = new UserController();
        $userDetails = $user->fetchUserData($orderDetails[0]['id_user']);

        if (empty($orderDetails)) {
            $this->errorInfo = "Nie ma takiego zamówienia.";
        }

        return $this->render('default/admin/orders/orderDetails.html.twig', [
                    'orderDetails' => $orderDetails,
                    'userDetails' => $userDetails,
                    'errorInfo' => $this->errorInfo
                        ]
        );
    }

    /**
     * @Route("/admin/orders/{id_order}/next")
     */
    public function nextStatusAction(Request $request, $id_order) {

        $orders = new ProductController();
        $status = $orders->getOrderStatus($id_order);

        $this->errorInfo = $status;

        if (empty($status)) {
            $this->errorInfo = "Nie ma takiego zamówienia.";
        }

        if ($status < 6) {
            $orders->nextStatus($id_order, $status);
        }

        $orderDetails = $orders->getFullOrderDetails($id_order);
        $user = new UserController();
        $userDetails = $user->fetchUserData($orderDetails[0]['id_user']);

        return $this->render('default/admin/orders/orderDetails.html.twig', [
                    'orderDetails' => $orderDetails,
                    'userDetails' => $userDetails,
                    'errorInfo' => $this->errorInfo
                        ]
        );
    }

    /**
     * @Route("/admin/orders/{id_order}/prev")
     */
    public function prevStatusAction(Request $request, $id_order) {

        $orders = new ProductController();
        $status = $orders->getOrderStatus($id_order);

        $this->errorInfo = $status;

        if (empty($status)) {
            $this->errorInfo = "Nie ma takiego zamówienia.";
        }

        if ($status > 1) {
            $orders->prevStatus($id_order, $status);
        }

        $orderDetails = $orders->getFullOrderDetails($id_order);
        $user = new UserController();
        $userDetails = $user->fetchUserData($orderDetails[0]['id_user']);

        return $this->render('default/admin/orders/orderDetails.html.twig', [
                    'orderDetails' => $orderDetails,
                    'userDetails' => $userDetails,
                    'errorInfo' => $this->errorInfo
                        ]
        );
    }

}
