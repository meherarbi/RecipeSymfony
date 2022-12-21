<?php

namespace App\Controller;


use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(UserRepository $userRepository , PaginatorInterface $paginator , Request $request): Response
    {

        $users = $userRepository->findAll();
        $users = $paginator->paginate(
            $userRepository->findAll(),
            $request->query->getInt('page', 1), /*page number*/
            8/*limit per page*/
        );

        return $this->render('pages/user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/new_user', name: 'app_new_user', methods: ['GET', 'POST'])]
    public function addUser(UserRepository $userRepository, Request $request, EntityManagerInterface $manager ) : Response
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $manager->persist($user);
            $manager->flush();
            $this->addFlash(
                'notice',
                'l\'utilisateur est ajouté avec succes !'
            );

            return $this->redirectToRoute('app_user');

        }

        return $this->render(
            'pages/user/new_user.html.twig',
            ['form' => $form->createView()]
        );
    }

   
}
