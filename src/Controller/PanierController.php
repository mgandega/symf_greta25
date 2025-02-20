<?php

namespace App\Controller;

use App\Repository\ConferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class PanierController extends AbstractController
{
    public $request ;
    public function __construct(
        private EntityManagerInterface $em,
        private RequestStack $requestStack
    ) {}

    #[Route('/panier/{id}', name: 'app_panier')]
    public function index($id, Request $request, ConferenceRepository $repo): Response
    {
        $this->request = $request;
      $idConference = $request->request->get('idConference');
      $quantite = $request->request->get('quantite');
     $conference = $repo->find($idConference);
     $description = $conference->getDescription();
     $titre = $conference->getTitre();
     $prix = $conference->getPrix();
      if($idConference && $quantite){
        $this->traitementPanier($idConference, $quantite, $titre, $description,$prix );
      }

    //  dd($request->request->);
        return $this->render('panier/index.html.twig', [
            'controller_name' => 'PanierController',
        ]);
    }

    // initialiser le panier
    public function createPanier(){
    //    dd(get_Class_Methods($this->requestStack));
        $session = $this->requestStack->getSession();
        // si on arrive pour la premiere fois cette condition retournera true
        // si le panier n'existe pas, on le créé
        if (!$session->has('panier')) {
            $session->set('panier', [
                'conferenceId' => [],
                'titre' => [],
                'quantite' => [],
                'prix' => [],
            ]);
        }
        $panier = $session->get('panier');
        return $panier;
    }

    // Ajouter un produit au panier
    public function traitementPanier($conferenceId, $quantite, $titre, $description, $prix ){
        // initialisation du panier
        $this->createPanier();
        
        $panier = $this->requestStack->getSession()->get('panier');
        // vérifions si le produit existe déja dans le panier
          $position = array_search($conferenceId, $panier['conferenceId']);

          if($position !==false){
               // si le produit existe déja dans le panier
               $panier['quantite'][$position] += $quantite;         
          }else{    
            // si le produit n'existe pas encore dans le panier
           
                   $panier['conferenceId'][] = $conferenceId;
                   $panier['quantite'][] = $quantite;
                   $panier['titre'][] = $titre;
                   $panier['prix'][] = $prix;
                   $panier['description'][] = $description;
          }
          // on met à jour le panier
          
          $this->requestStack->getSession()->set('panier', $panier);
    
      }
}
