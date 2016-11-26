<?php

namespace AppBundle\Controller;

//use AppBundle\Entity\Cart;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
//use Symfony\Component\Form\Extension\Core\Type\HiddenType;
//use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CartController extends Controller {

    private $errorInfo;
    private $session;
    private $products;

    public function __construct() {
        $this->session = new Session();
        $this->products = new ProductController();
    }

    /**
     * @Route("cart/add/{id_product}")
     */
    public function addToCartAction(Request $request, $id_product) {

        $id_user = $this->session->get('id_user');

        if (empty($id_user)) {
            $this->errorInfo = 'Zaloguj się aby uzyskać dostęp do swojego konta.';
            return $this->redirectToRoute('login');
        }

        // $cart = new Cart();

        $product = $this->products->fetchProduct($id_product);

        if (empty($product)) {
            if (empty($id)) {
                $this->errorInfo = 'Taki produkt nie istnieje.';
                return $this->render('default/cart/cart.html.twig', [
                            'errorInfo' => $this->errorInfo
                                ]
                );
            }
        }

        $cart = $this->session->get('cart');

        $cart[] = $product;
        $this->session->set('cart', $cart);


        if ($cart == NULL) {
            $this->errorInfo = 'Twój koszyk jest pusty.';
            return $this->render('default/cart/cart.html.twig', [
                        'errorInfo' => $this->errorInfo
                            ]
            );
        }

        // Everything is ok, redirect to showCartAction
        $this->errorInfo = 'Pizza została dodana.';
        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("cart/")
     */
    public function showCartAction(Request $request) {
        // show cart

        return $this->render('default/cart/cart.html.twig', [
                    'errorInfo' => $this->errorInfo,
                    'cart' => $this->session->get('cart')
                        ]
        );
    }

    /**
     * @Route("cart/flush/")
     */
    public function flushCartAction(Request $request) {
        $this->products->flushCart();

        $this->errorInfo = 'Twój koszyk jest pusty.';
        return $this->render('default/cart/cart.html.twig', [
                    'errorInfo' => $this->errorInfo,
                    'cart' => $this->session->get('cart')
                        ]
        );
    }

    /**
     * @Route("cart/remove/{id_product}")
     */
    public function removeFromCartAction(Request $request, $id_product) {

        $product_position = $this->session->get('cart');

        unset($product_position[$id_product]);

        var_dump($product_position);

        $this->session->set('cart', $product_position);

        $this->errorInfo = 'Poprawnie usunięto element.';
        return $this->render('default/cart/cart.html.twig', [
                    'errorInfo' => $this->errorInfo,
                    'cart' => $this->session->get('cart')
                        ]
        );
    }

}
