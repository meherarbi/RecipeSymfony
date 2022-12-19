<?php

// src/Controller/LoginController.php
namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    /**
     * Undocumented function
     *
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    #[Route('/login', name:'app_login')]
function index(AuthenticationUtils $authenticationUtils): Response
    {
    // get the login error if there is one
    $error = $authenticationUtils->getLastAuthenticationError();

    // last username entered by the user
    $lastUsername = $authenticationUtils->getLastUsername();

    return $this->render('pages/login/login.html.twig', [
        'controller_name' => 'LoginController',
        'last_username' => $lastUsername,
        'error' => $error,
    ]);
}
#[Route('/logout', name:'app_logout', methods:['GET'])]
function logout()
    {
    // controller can be blank: it will never be called!
    throw new \Exception('Don\'t forget to activate logout in security.yaml');
}
/**
 * Undocumented function
 *
 * @param Request $request
 * @param EntityManagerInterface $manager
 * @return void
 */
#[Route('/register', name:'app_register', methods:['GET', 'POST'])]
function register(Request $request, EntityManagerInterface $manager)
    {
    $user = new User();
    $user->setRoles(array('ROLE_USER'));
    $form = $this->createForm(RegistrationType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

        $user = $form->getData();

        $manager->persist($user); // ... perform some action, such as saving the task to the database
        $manager->flush();

        $this->addFlash(
            'notice',
            ' succes !'
        );

        return $this->redirectToRoute('app_login');
    }

    return $this->render('pages/register/register.html.twig', [
        'form' => $form->createView(),
    ]);
}
}
