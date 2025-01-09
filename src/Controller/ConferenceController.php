<?php

namespace App\Controller;

use App\Entity\Conference;
use App\Form\ConferenceType;
use App\Repository\ConferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ConferenceController extends AbstractController
{
    public $em;
    public function __construct(EntityManagerInterface $em){
        $this->em = $em;
    }

    #[Route('/conference/add', name: 'app_conference.add')]
    public function add(Request $request, ConferenceRepository $repo): Response
    {
        $conference = new Conference();
        // 
        $form = $this->createForm(ConferenceType::class, $conference);

        // cette fonction permet d'hydrater l'objet conférence
        $form->handleRequest($request);

        // si le formulaire est soummis et que le formulaire est valide
        if($form->isSubmitted()){
            // dd($conference);
            // $this->em->getRepository(conference::class);
            $this->em->persist($conference);
            $this->em->flush();
            // redirection vers la page des conferences
            return $this->redirectToRoute('app_conference.conferences');
        }

        return $this->render('conference/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/conferences', name: 'app_conference.conferences')]
    public function conferences(Request $request, ConferenceRepository $repo): Response
    {
        $conferences = $repo->findAll();
        // $this->em->getRepository(Conference::class)->findAll();
        return $this->render('conference/index.html.twig', [
            'conferences' => $conferences,
        ]);
    }
    #[Route('/conferences/details/{id}', name: 'app_conference.details')]
    public function details($id, Request $request, ConferenceRepository $repo): Response
    {
        $conference = $repo->find($id);
        // $this->em->getRepository(Conference::class)->findAll();
        return $this->render('conference/details.html.twig', [
            'conference' => $conference,
        ]);
    }
  
    #[Route('/conferences/edit/{id}', name: 'app_conference.edit')]
    public function edit($id, Request $request, ConferenceRepository $repo): Response
    {
        $conference = $repo->find($id);
        // $this->em->getRepository(conference::class)->find($id);
        // 
        $form = $this->createForm(ConferenceType::class, $conference);

        // cette fonction permet d'hydrater l'objet conférence
        $form->handleRequest($request);

        // si le formulaire est soummis et que le formulaire est valide
        if($form->isSubmitted()){
            // $this->em->getRepository(conference::class);
            $this->em->persist($conference);
            $this->em->flush();
            // redirection vers la page des conferences
            return $this->redirectToRoute('app_conference.conferences');
        }

        return $this->render('conference/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/conferences/supprimer/{id}', name: 'app_conference.supprimer')]
    public function supprimer($id, Request $request, ConferenceRepository $repo): Response
    {
        $conference = $repo->find($id);
        // $this->em->getRepository(Conference::class)->findAll();
        $this->em->remove($conference);
        $this->em->flush();
        return $this->redirectToRoute('app_conference.conferences');
    }
}
