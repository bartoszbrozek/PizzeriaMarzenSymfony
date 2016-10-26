<?php

namespace AppBundle\Controller;

use AppBundle\Database\Database;
use AppBundle\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Session\Session;

//use Symfony\Component\Security\Core\User\User;

class SecurityController extends Controller {

    /**
     * @Route("login")
     */
    public function loginAction(Request $request) {
        $user = new User();
        $form = $this->createFormBuilder($user)
                ->add('username', TextType::class)
                ->add('password', PasswordType::class)
                ->add('submit', SubmitType::class, ['label' => 'Zaloguj się'])
                ->getForm();

        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {
            $user = $form->getData();

            try {

                $pdo = new Database();

                $db = $pdo->getDb($pdo->connection());

                $query = $db->prepare("SELECT id_user, login, password FROM users WHERE login=? AND password=? ");

                $query->bindValue(1, $user->getUsername());
                $query->bindValue(2, $user->getPassword());
                $query->execute();
            } catch (PDOException $ex) {
                echo $ex->getMessage();
            }

            if ($query->rowCount() > 0) {
                $userData = $query->fetch();
                $session = new Session();
                // $session->start();
                $session->set('id_user', $userData['id_user']);
                $session->set('login', $userData['login']);

                return $this->redirectToRoute('homepage');
            }
        }
        return $this->render('default/loginForm.html.twig', [
                    'form' => $form->createView()
                        ]
        );
    }

    /**
     * @Route("logout")
     */
    public function logout() {
        $session = new Session();

        $session->invalidate();

        return $this->render('default/index.html.twig');
    }

}
