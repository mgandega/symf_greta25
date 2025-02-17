<?php 
// src/Controller/MailerController.php
namespace App;

use Twig\Environment;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MonMailer
{
    public $mailer;
    public $twig ;
    public function __construct(MailerInterface $mailer,Environment $twig){
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function sendEmail($pseudo, $message)
    {
       $contenu =  $this->twig->render('messages\message.html.twig', compact('pseudo','message'));
        $email = (new Email())
            ->from('monEmail@gmail.com')
            ->to('admin@admin.com')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Time for Symfony Mailer!')
            ->html($contenu);
            

        $this->mailer->send($email);

    //     // ...
    }
}