<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Entity\Ingredient;
use AppBundle\Database\Database;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class ProductMgmtController extends Controller {

    private $errorInfo;

    // PRODUCTS

    /**
     * @Route("admin/products")
     */
    public function showProductAction(Request $request) {
        return $this->render('default/admin/products/products.html.twig');
    }

    /**
     * @Route("admin/products/add")
     */
    public function addProductAction(Request $request) {
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
        } catch (PDOException $ex) {
            echo $ex->getMessage();
        }

        // Create new form for creating new products
        $product = new Product();

        $form = $this->createFormBuilder($product)
                ->add('name', TextType::class, array(
                    'label' => 'Nazwa',
                ))
                ->add('description', TextType::class, array(
                    'label' => 'Opis',
                ))
                ->add('price', NumberType::class, array(
                    'label' => 'Cena',
                ))
                ->add('submit', SubmitType::class, ['label' => 'Dodaj'])
                ->getForm();

        $form->handleRequest($request);

        // Check if form is valid and submitted,
        // then insert new product
        if ($form->isValid() && $form->isSubmitted()) {
            try {
                $query = $db->prepare('INSERT INTO products (name, price, description) VALUES (?,?,?)');

                $query->bindValue(1, $product->getName());
                $query->bindValue(2, $product->getPrice());
                $query->bindValue(3, $product->getDescription());

                $query->execute();
                $lastId = $db->lastInsertId();

                $this->errorInfo = 'Poprawnie dodano nową pizzę.';
            } catch (PDOException $ex) {
                echo $ex->getMessage();
            }
        }

        return $this->render('default/admin/products/product_add.html.twig', [
                    'form' => $form->createView(),
                    'errorInfo' => $this->errorInfo
                        ]
        );
    }

    // INGREDIENTS

    /**
     * @Route("/admin/products/ingredient")
     */
    public function showIngredientAction(Request $request) {
        return $this->render('default/admin/products/ingredients.html.twig');
    }

    /**
     * @Route("admin/products/ingredient/add")
     */
    public function addIngredientAction(Request $request) {
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
        } catch (PDOException $ex) {
            echo $ex->getMessage();
        }

        // Create new form for creating new ingredients
        $ingredient = new Ingredient();

        $form = $this->createFormBuilder($ingredient)
                ->add('name', TextType::class, array(
                    'label' => 'Nazwa',
                ))
                ->add('price', NumberType::class, array(
                    'label' => 'Cena',
                ))
                ->add('submit', SubmitType::class, ['label' => 'Dodaj'])
                ->getForm();

        $form->handleRequest($request);

        // Check if form is valid and submitted,
        // then insert new ingredient
        if ($form->isValid() && $form->isSubmitted()) {
            try {
                $query = $db->prepare('INSERT INTO ingredients (name, price) VALUES (?,?)');

                $query->bindValue(1, $ingredient->getName());
                $query->bindValue(2, $ingredient->getPrice());

                $query->execute();
                $lastId = $db->lastInsertId();

                $this->errorInfo = 'Poprawnie dodano nowy składnik.';
            } catch (PDOException $ex) {
                echo $ex->getMessage();
            }
        }

        return $this->render('default/admin/products/ingredient/ingredient_add.html.twig', [
                    'form' => $form->createView(),
                    'errorInfo' => $this->errorInfo
                        ]
        );
    }

}
