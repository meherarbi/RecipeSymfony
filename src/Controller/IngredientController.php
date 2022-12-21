<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IngredientController extends AbstractController
{
    /**
     * This function displays all Ingredients
     *
     * @param IngredientRepository $ingredientRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/ingredient', name:'app_ingredient', methods:['GET'])]
function index(IngredientRepository $ingredientRepository, PaginatorInterface $paginator, Request $request): Response
    {
    $ingredients = $paginator->paginate(
        $ingredientRepository->findAll(),
        $request->query->getInt('page', 1), /*page number*/
        5/*limit per page*/
    );

    return $this->render('pages/ingredient/index.html.twig',
        ['ingredients' => $ingredients]);
}

/**
 *  function that adds a new ingredient
 *
 * @param Request $request
 * @param EntityManagerInterface $manager
 * @return Response
 */
#[Route('/ingredient/new', name:'app_new', methods:['GET', 'POST'])]
function new (Request $request, EntityManagerInterface $manager): Response {

    $Ingredients = new Ingredient();

    $form = $this->createForm(IngredientType::class, $Ingredients);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {

        $Ingredients = $form->getData();

        $manager->persist($Ingredients); // ... perform some action, such as saving the task to the database
        $manager->flush();

        $this->addFlash(
            'notice',
            'l\'ingredient est ajouté avec succes !'
        );

        return $this->redirectToRoute('app_ingredient');
    }

    return $this->render('pages/ingredient/new.html.twig',
        ['form' => $form->createView()]);
}

#[Route('/ingredient/edit/{id}', name:'app_edit', methods:['GET', 'POST'])]
function update(Ingredient $ingredient, Request $request, EntityManagerInterface $manager): Response
    {

    $form = $this->createForm(IngredientType::class, $ingredient);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {

        $Ingredients = $form->getData();

        $manager->persist($Ingredients); // ... perform some action, such as saving the task to the database
        $manager->flush();

        $this->addFlash(
            'notice',
            'l\'ingredient est modifié avec succes !'
        );

        return $this->redirectToRoute('app_ingredient');
    }

    return $this->render('pages/ingredient/edit.html.twig',
        ['form' => $form->createView()]);
}

#[Route('/ingredient/delete/{id}', name:'app_delete', methods:['GET'])]
function Delete(EntityManagerInterface $manager, Ingredient $ingredient)
    {
    $manager->remove($ingredient);
    $manager->flush();

    $this->addFlash(
        'notice',
        'l\'ingredient est supprimer avec succes !'
    );
    return $this->redirectToRoute('app_ingredient');
}

}
