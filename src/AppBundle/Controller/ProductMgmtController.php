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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

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
        $ingredient = new ProductController();
        $ingredients = $ingredient->fetchIngredients();

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
                ->add('ingredients', ChoiceType::class, array(
                    'label' => 'Składniki',
                    'multiple' => true,
                    'expanded' => true,
                    'choices' => $ingredients))
                ->add('submit', SubmitType::class, ['label' => 'Dodaj'])
                ->getForm();

        $form->handleRequest($request);

        // Check if form is valid and submitted,
        // then insert new product
        if ($form->isValid() && $form->isSubmitted()) {
            try {
                $db->beginTransaction();

                //Insert product
                $query = $db->prepare('INSERT INTO products (name, price, description) VALUES (?,?,?)');

                $query->bindValue(1, $product->getName());
                $query->bindValue(2, $product->getPrice());
                $query->bindValue(3, $product->getDescription());

                $query->execute();
                $lastId = $db->lastInsertId();

                //Insert product's ingredients
                foreach ($product->getIngredients() as $ingredient) {
                    $query = $db->prepare('INSERT INTO product_ingredient (id_product, id_ingredient) VALUES (?,?)');
                    $query->bindValue(1, $lastId);
                    $query->bindValue(2, $ingredient);

                    $query->execute();
                }

                $db->commit();

                $this->errorInfo = 'Poprawnie dodano nową pizzę.';
            } catch (PDOException $ex) {
                $db->rollBack();
                echo $ex->getMessage();
            }
        }

        return $this->render('default/admin/products/product_add.html.twig', [
                    'form' => $form->createView(),
                    'errorInfo' => $this->errorInfo
                        ]
        );
    }

    /**
     * @Route("admin/products/edit")
     */
    public function showProductsToEditAction(Request $request) {

        $products = new ProductController();

        return $this->render('default/admin/products/product_show.html.twig', [
                    'errorInfo' => $this->errorInfo,
                    'products' => $products->showProducts()
        ]);
    }

    /**
     * @Route("admin/products/edit/{id_product}")
     */
    public function editProductAction(Request $request, $id_product) {

        // Create new form for updating products
        $product = new Product();
        $productInfo = new ProductController();
        $ingredients = $productInfo->fetchProductIngredients($id_product);
        $productContent = $productInfo->fetchProduct($id_product);

        $form = $this->createFormBuilder($product)
                ->add('name', TextType::class, array(
                    'label' => 'Nazwa',
                    'data' => $productContent[0]['name']
                ))
                ->add('description', TextType::class, array(
                    'label' => 'Opis',
                    'data' => $productContent[0]['description']
                ))
                ->add('price', NumberType::class, array(
                    'label' => 'Cena',
                    'data' => $productContent[0]['price']
                ))
                ->add('ingredients', ChoiceType::class, array(
                    'label' => 'Składniki',
                    'multiple' => true,
                    'expanded' => true,
                    'choices' => $ingredients,
                    //'data' => $productContent[0]['ingredients']
                ))
                ->add('submit', SubmitType::class, ['label' => 'Dodaj'])
                ->getForm();

        $form->handleRequest($request);

        return $this->render('default/admin/products/product_edit.html.twig', [
                    'errorInfo' => $this->errorInfo,
                    'form' => $form->createView()
        ]);
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
