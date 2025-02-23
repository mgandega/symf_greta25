<?php
namespace App\Controller;

use App\Events\MyEvents;
use App\Entity\Categorie;
use App\Entity\Conference;
use Pagerfanta\Pagerfanta;
use App\Entity\Commentaire;
use App\Entity\Reservation;
use App\Form\ConferenceType;
use App\Form\CommentaireType;
use App\Form\ReservationType;
use App\Security\Voter\ConferenceVoter;
use App\Repository\ConferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Context\ExecutionContextInterface;



class ConferenceController extends AbstractController
{
    public $em;
    public function __construct(EntityManagerInterface $em){
        $this->em = $em;
    }

    #[Route('/conference/add', name: 'app_conference.add')]
    #[IsGranted('POST_CREATE')]
    public function add(Request $request, ConferenceRepository $repo ): Response
    {
        $errors = '';
        $conference = new Conference();
        // 
        $form = $this->createForm(ConferenceType::class, $conference);

        // cette fonction permet d'hydrater l'objet conférence
        $form->handleRequest($request);

        // si le formulaire est soummis et que le formulaire est valide
        if($form->isSubmitted() && $form->isValid()){
            // le chemin où on doit stocker l'image (nom de l'image)
            //$_SERVER['DOCUMENT_ROOT'] => /Applications/MAMP/htdocs/symf_greta25/public/;
            $chemin = $this->getParameter('photo_directory');
            // récupération de l'objet file (pour pouvoir récupérer le nom de l'image par exemple)
            $file = $conference->getImage()->getFile();

            // récuperation du nom de l'image (il faut toujours utiliser la méthode             
            //get_class_methods pour voir toutes les methodes de l'objet file . exemple 
             //dd(get_class_methods($file)))
            $nom_image = $file->getClientOriginalName();

            // il faut hydrater les propriété alt et url
            // le alt contienyt le nom de l'image
            $conference->getImage()->setAlt($nom_image);
            // l'url contient le chemin relatif de l'image + le nom de l'image
            $conference->getImage()->setUrl('uploads/images/'.$nom_image);

            // la methode move permet de mettre l'image dans le repertoire image
            $file->move($chemin, $nom_image);
            $conference->setUser($this->getUser());
            $this->em->persist($conference);
           // le PostPersist() se limite ici donc l'objet ne sera pas persisté dans la base de données 
            $this->em->flush();
            // redirection vers la page des conferences
            return $this->redirectToRoute('app_conference.conferences');
        }

        return $this->render('conference/add.html.twig', [
            'form' => $form->createView()
        ]);
    }
    #[Route('/', name: 'app_conference.home')]
    #[Route('/conferences', name: 'app_conference.conferences')]
    public function conferences(Request $request, ConferenceRepository $repo): Response
    {
        // $conferences = $repo->findAll();
        $categories = $this->em->getRepository(Categorie::class)->findAll();
        //pagerFanta
        $query = $repo->createBlogListQueryBuilder();

        $pagerfanta = new Pagerfanta(
            new QueryAdapter($query)
        );
        
        $pagerfanta->setMaxPerPage(4); // 6 par page
        $pagerfanta->setCurrentPage($request->query->getInt('page', 1));

        // end pagerFanta
        // $this->em->getRepository(Conference::class)->findAll();
        return $this->render('conference/index.html.twig', [
            'conferences' => $pagerfanta->getCurrentPageResults(),
            'categories'=>$categories,
            'pager' => $pagerfanta
        ]);
    }
    #[Route('/conference/categorie/{nom}', name: 'app_conference.categorie')]
    public function categorie($nom, Request $request, ConferenceRepository $repo): Response
    {
        $categories = $this->em->getRepository(Categorie::class)->findAll();
        $conferences = $this->em->getRepository(Conference::class)->findByCategorie($nom);
        return $this->render('conference/index.html.twig', [
            'conferences' => $conferences,
            'categories'=>$categories
        ]);
    }
    #[Route('/conferences/details/{id}', name: 'app_conference.details')]
    public function details($id, Request $request, ConferenceRepository $repo, Conference $conference): Response
    {
        $conference = $repo->find($id);
        // $this->em->getRepository(Conference::class)->findAll();
        return $this->render('conference/details.html.twig', [
            'conference' => $conference,
        ]);
    }
    #[Route('/conferences/reservation/{id}', name: 'app_conference.reservation')]
    public function reservation($id, Request $request, ConferenceRepository $repo): Response
    {
      
       $reservation = new Reservation();
       $form = $this->createForm(ReservationType::class, $reservation);
        // $this->em->getRepository(Conference::class)->findAll();
        $form->handleRequest($request);
        if($form->isSubmitted() ){
            $conference = $repo->find($id);
            $reservation->setConference($conference);
            $this->em->persist($reservation);
            $this->em->flush();
            return $this->redirectToRoute('app_conference.conferences');

        }
        return $this->render('reservation/reservation.html.twig', [
            'form' => $form->createView(),
        ]);
    }
  
    #[Route('/conferences/edit/{id}', name: 'app_conference.edit')]
    #[IsGranted('POST_EDIT', 'conference')]
    public function edit($id, Request $request, ConferenceRepository $repo, Conference $conference): Response
    {
        // $conference = $repo->find($id);
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
    #[IsGranted('POST_DELETE', 'conference')]
    public function supprimer($id, Request $request, ConferenceRepository $repo, EventDispatcherInterface $dispatcher, Conference $conference): Response
    {
    $conference = $repo->find($id);
    foreach ($conference->getReservations() as $reservation) {
        $this->em->remove($reservation);
    }
    $this->em->remove($conference);
    $this->em->flush();
        return $this->redirectToRoute('app_conference.conferences');
    }
    #[Route('/conference/commentaire/{id}', name: 'app_conference.commenter')]
    public function commenter($id, Request $request, Conference $conference): Response
    {
       
       $commentaires =  $this->em->getRepository(Commentaire::class)->findByConference($conference);
      
        $commentaire = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);
        if($form->isSubmitted()){
           
            $commentaire->setConference($conference);
            $this->em->persist($commentaire);
            $this->em->flush();
            return $this->redirectToRoute('app_conference.details', ['id'=>$id]);
        }
         return $this->render('conference/details.html.twig', ['conference'=>$conference, 'commentaires'=>$commentaires,'form'=>$form->createView()]);
    }
    #[Route('/menu', name: 'menu')]
    public function menu(){
        $categories = $this->em->getRepository(Categorie::class)->findAll();
        return $this->render("partials/menu.html.twig", ['categories'=>$categories]);
    }
    #[Route('/listReservation', name: 'app_reservation')]
    public function listReservation(){
        $reservations = $this->em->getRepository(Reservation::class)->findAll();
        return $this->render("conference/reservation.html.twig", ['reservations'=>$reservations]);
    }
    #[Route('/delete/{id}', name: 'app_reservation.delete')]
    public function delete(Reservation $reservation){
        $this->em->remove($reservation);
        $this->em->flush();
        
        return $this->redirectToRoute("app_reservation");
    }
    #[Route('/filtre/recherche', name: 'filtre.recherche')]
    public function recherche(Request $request){
    //  dd($_POST['prix'], $_POST['date'],$_POST['categorie']);
    $selectedPrix = $request->request->get('prix');
    $selectedDate = $request->request->get('date');
    $selectedCategorie = $request->request->get('categorie');
    $conferences = $this->em->getRepository(Conference::class)->filtreConferences( $selectedPrix,$selectedDate,$selectedCategorie);
    $categories = $this->em->getRepository(Categorie::class)->findAll();
    // return $this->render('conference/index.html.twig', [
    //     'conferences' => $conferences,
    //     'categories'=>$categories
    // ]);
    return $this->render('conference/index.html.twig',compact('conferences','categories','selectedDate', 'selectedPrix','selectedCategorie'));
    }

}
    