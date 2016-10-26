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

        if ($form->isValid() && $form->isSubbmited()) {
            $user = $form->getData();

            try {
                $db = new Database();

                $db->connection();

                $query = $db->prepare("SELECT id_user, login, password FROM users WHERE login=? AND password=? ");
                $query->bindValue(1, $user->getUsername());
                $query->bindValue(2, $user->getPassword());
                $query->execute();
            } catch (PDOException $ex) {
                echo $ex->getMessage();
            }

            if ($query->rowCount() > 0) {
                $userData = $query->fetch();
                $_SESSION['userId'] = $userData['id_user'];
                $_SESSION['login'] = $userData['login'];

                return $this->redirectToRoute('homepage');
            }
        }
        return $this->render('default/loginForm.html.twig', ['form' => $form->createView()]);
    }

}
