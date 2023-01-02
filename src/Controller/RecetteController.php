<?php

namespace App\Controller;

use App\Entity\Recette;
use App\Form\RecetteType;
use App\Repository\RecetteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RecetteController extends AbstractController
{
    /**
     * This function displays all recettes
     *
     * @param RecetteRepository $recetteRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/recette', name:'app_recette', methods:['GET'])]
    #[IsGranted('ROLE_USER')]
function index(RecetteRepository $recetteRepository, PaginatorInterface $paginator, Request $request): Response
    {
   $recettes = $paginator->paginate(
    $recetteRepository->findAll(),
    $request->query->getInt('page', 1), /*page number*/
    5/*limit per page*/
);


    return $this->render('pages/recette/index.html.twig',
        ['recettes' => $recettes]);
}

/**
 *  function that adds a new recette
 *
 * @param Request $request
 * @param EntityManagerInterface $manager
 * @return Response
 */
#[Route('/recette/new', name:'app_new_recette', methods:['GET', 'POST'])]
function new (Request $request, EntityManagerInterface $manager): Response {

    $recettes = new Recette();

    $form = $this->createForm(RecetteType::class, $recettes);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {

        $recettes = $form->getData();

        $manager->persist($recettes); // ... perform some action, such as saving the task to the database
        $manager->flush();

        $this->addFlash(
            'notice',
            'la recette est ajouté avec succes !'
        );

        return $this->redirectToRoute('app_recette');
    }

    return $this->render('pages/recette/new.html.twig',
        ['form' => $form->createView()]);
}

#[Route('/recette/edit/{id}', name:'app_edit_recette', methods:['GET', 'POST'])]
function update(Recette $recette, Request $request, EntityManagerInterface $manager): Response
    {

    $form = $this->createForm(RecetteType::class, $recette);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {

        $recettes = $form->getData();

        $manager->persist($recettes); // ... perform some action, such as saving the task to the database
        $manager->flush();

        $this->addFlash(
            'notice',
            'la recette est modifié avec succes !'
        );

        return $this->redirectToRoute('app_recette');
    }

    return $this->render('pages/recette/edit.html.twig',
        ['form' => $form->createView()]);
}

#[Route('/recette/delete/{id}', name:'app_delete_recette', methods:['GET'])]
function Delete(EntityManagerInterface $manager, Recette $recette)
    {
    $manager->remove($recette);
    $manager->flush();

    $this->addFlash(
        'notice',
        'l\'recette est supprimer avec succes !'
    );
    return $this->redirectToRoute('app_recette');
}

}
