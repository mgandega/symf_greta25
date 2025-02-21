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

    #[Route('/panier', name: 'app_panier')]
    public function index(ConferenceRepository $repo , Request $request): Response
    {
      $idConference = $request->request->get('idConference'); // $_POST['idConference']
    //   dd($idConference);
    //   $idConference = $request->query->get('idConference');  // $_GET['id_conference']
      $quantite = $request->request->get('quantite'); // $_POST['quantite']
     $conference = $repo->find($idConference);
     $description = $conference->getDescription();
     $titre = $conference->getTitre();
     $prix = $conference->getPrix();
      if($idConference && $quantite){
        $this->traitementPanier($idConference, $quantite, $titre, $description,$prix);
      }
      // récupération du panier
      $panier = $this->getPanier();
      // calcul des prix
      $prixTotal =$this->prixTotal();
    //  dd($request->request->);
        return $this->render('panier/index.html.twig', [
            'panier' => $panier,
            'prixTotal' => $prixTotal
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

        // $personnes = ['pierre','paul','jacques'];
        // array_search('martin',$personnes); // si oui, elle retourne la clé correspondante , si non elle retourne false
        // initialisation du panier si le panier n'existe pas
        // si le panier existe déja elle me retourne le panier (avec ses données)
        $this->createPanier();
        
        $panier = $this->requestStack->getSession()->get('panier');

    //     [
    //         'conferenceId'=>[1,2,3],
    //         'quantite' =>[2,3,1],
    //         'prix'=>[12,40,30]
    //      ];
        // vérifions si le produit existe déja dans le panier
          $cle = array_search($conferenceId, $panier['conferenceId']);

          if($cle !==false){
    //     [
    //         'conferenceId'=>[1,2,3],
    //         'quantite' =>[2,3,1], // $panier['quantite'][1] => 3 (3 + 5 = 8) 
    //         'prix'=>[12,40,30]
    //         'decsription' =>['desc1', 'desc2','desc3']
    //      ];
               // si le produit existe déja dans le panier
               $panier['quantite'][$cle] += $quantite;         
          }else{    
            // si le produit n'existe pas encore dans le panier
                  [
                     'conferenceId'=>[],
                     'quantite' =>[],
                     'prix'=>[]
                 ];
                   $panier['conferenceId'][] = $conferenceId;
                   $panier['quantite'][] = $quantite;
                   $panier['titre'][] = $titre;
                   $panier['prix'][] = $prix;
                   $panier['description'][] = $description;
          }
          // on met à jour le panier 
        $this->requestStack->getSession()->set('panier', $panier); // $panier = ['gh','eefe']
    
      }
      public function getPanier(){
        $session = $this->requestStack->getSession();
        $panier = $session->get('panier');
        return $panier;
      }

      public function prixTotal(){
        $panier = $this->getPanier(); // DRY (Don't Repeat Yourself)
        $total = 0;
        if(!empty($panier['quantite'])){
            foreach($panier['quantite'] as $cle=>$valeur){
            $total +=$panier['quantite'][$cle]*$panier['prix'][$cle];
        }
        }
       
        return $total;
      }


  #[Route('/supprimer/{id}', name: 'supprimer_produit')]
    public function supprimer($id)
    {
       $session = $this->requestStack->getSession();
       $panier = $this->getPanier();
      
       // on verifie si cette clé existe dans le panier
        $cle = array_search($id, $panier['conferenceId']);

        // si cette clé existe supprime moi tous les élements qui ont cette clé
        if($cle !==false){
            array_splice($panier['conferenceId'], $cle, 1);
            array_splice($panier['titre'], $cle, 1);
            array_splice($panier['quantite'], $cle, 1);
            array_splice($panier['prix'], $cle, 1);
            array_splice($panier['description'], $cle, 1);
            $session->set('panier',$panier);
        }
        return $this->redirectToRoute('app_panier_supprimer');
    }

        #[Route('/panier_supprimer', name: 'app_panier_supprimer')]
        public function panier_suppimer(){
             $session = $this->requestStack->getSession();
             $panier = $this->getPanier();
             $prixTotal =$this->prixTotal();

            return $this->render('panier/index.html.twig', [
            'panier' => $panier,
            'prixTotal' => $prixTotal
        ]);

        }

         #[Route('/panier_vider', name: 'app_panier_vider')]
        public function panier_vider(){
            
             $session = $this->requestStack->getSession();
             $panier = $this->getPanier();
             $prixTotal =$this->prixTotal();
             $prixTotal = 0;
            unset($panier);
            $panier = [];



            return $this->render('panier/index.html.twig', [
            'panier' => $panier,
            'prixTotal' => $prixTotal
            ]);
       }
}
