<?php

namespace App\Controller;

use App\Entity\Contact;
use App\MonMailer;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; 

final class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact.sendMessage')]
    public function sendMessage(MonMailer $mailer, Request $request , EntityManagerInterface $manager): Response
    {
        $contact = new Contact;
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $manager->persist($contact);
            $manager->flush();
            $mailer->sendEmail($contact->getPseudo(), $contact->getMessage());
           return $this->redirectToRoute('app_conference.conferences');
        }
        return $this->render('contact/index.html.twig', [
            'form'=>$form->createView()
        ]);
    }
}
