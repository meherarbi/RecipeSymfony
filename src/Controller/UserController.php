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
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * This function allow all user
     *
     * @param UserRepository $userRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
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
    
    /**
     * this function allow to edit the user
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/new_user', name: 'app_new_user', methods: ['GET', 'POST'])]
    public function addUser( Request $request, EntityManagerInterface $manager ) : Response
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
   
    #[Route('/edit_user/{id}', name: 'app_edit_user', methods: ['GET', 'POST'])]
    public function editUser(Request $request, EntityManagerInterface $manager, User $user , UserPasswordHasherInterface $hasher): Response
    {
        

        $form = $this->createForm(UserType::class, $user);
        
        $form->handleRequest($request);
        

        if ($form->isSubmitted() && $form->isValid()) {
            if($hasher->isPasswordValid($user,$form->getData()->getPlainPassword()))

            {
               $user = $form->getData();
            
               $manager->persist($user);
               $manager->flush();
               $this->addFlash(
                'notice',
                'l\'utilisateur est modifié avec succes !'
               );
               return $this->redirectToRoute('app_user');
            }
            else
            {
                $this->addFlash(
                    'warning',
                    'le mot de passe n\'est pas valide !'
                );
            }

            

        }

        return $this->render(
            'pages/user/edit_user.html.twig',
            ['form' => $form->createView()]
        );
    }
    

    #[Route('/delete_user/{id}', name: 'app_edelete_user', methods: ['GET'])]
    public function delete(EntityManagerInterface $manager, User $user)
    {
        $manager->remove($user);
        $manager->flush();
            $this->addFlash(
                'notice',
                'l\'utilisateur est supprimer avec succes !'
            );

            return $this->redirectToRoute('app_user');
    }
}
