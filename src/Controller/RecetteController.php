<?php

namespace App\Controller;

use App\Entity\Mark;
use App\Form\MarkType;
use App\Entity\Recette;
use App\Form\RecetteType;
use App\Repository\MarkRepository;
use App\Repository\RecetteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
    $recetteRepository->findBy(['user' => $this->getUser()]),
    $request->query->getInt('page', 1), /*page number*/
    12/*limit per page*/
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
#[Security("is_granted('ROLE_USER') and user === recette.getUser()")]
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
#[Security("is_granted('ROLE_USER') and user === recette.getUser()")]
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

#[Route('/recette/show/{id}', name:'app_show_recette', methods:['GET', 'POST'])]
#[Security("is_granted('ROLE_USER') and user === recette.getUser()")]
function Show(Recette $recette,
Request $request,
MarkRepository $markRepository,
EntityManagerInterface $manager
): Response {
$mark = new Mark();
$form = $this->createForm(MarkType::class, $mark);

$form->handleRequest($request);
if ($form->isSubmitted() && $form->isValid()) {
    $mark->setUser($this->getUser())
        ->setRecette($recette);

    $existingMark = $markRepository->findOneBy([
        'user' => $this->getUser(),
        'recette' => $recette
    ]);

    if (!$existingMark) {
        $manager->persist($mark);
    } else {
        $existingMark->setMark(
            $form->getData()->getMark()
        );
    }

    $manager->flush();

    $this->addFlash(
        'success',
        'Votre note a bien été prise en compte.'
    );

    return $this->redirectToRoute('app_show_recette', ['id' => $recette->getId()]);
}

return $this->render('pages/recette/show.html.twig', [
    'recette' => $recette,
    'form' => $form->createView()
]);
}


}
