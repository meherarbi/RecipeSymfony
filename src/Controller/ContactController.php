<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Service\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request ,EntityManagerInterface $manager , MailerInterface $mailer , MailService $mailService): Response
    {
        $contact = new Contact();
        if($this->getUser()){
            $contact->setFullName($this->getUser()->getFullName())
                    ->setEmail($this->getUser()->getEmail());
        }
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $contact = $form->getData();
    
            $manager->persist($contact); // ... perform some action, such as saving the task to the database
            $manager->flush();

            //email


            $mailService->sendEmail(
                $contact->getEmail(),
                $contact->getSubject(),
                'contact/email.html.twig',
                ['contact' => $contact]
            );


            /* $email = (new Email())
            ->from($contact->getEmail())
            ->to('admin@example.com')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject($contact->getSubject())
            ->text('txt')
            ->html($contact->getMessage());

            $mailer->send($email); */
    
            $this->addFlash(
                'notice',
                'votre message a été envoyé avec succes !'
            );
    
            return $this->redirectToRoute('app_recette');
        }


        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

   
}
