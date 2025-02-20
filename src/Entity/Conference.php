<?php

namespace App\Entity;

use App\Repository\ConferenceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: ConferenceRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Conference
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\Length(
        min: 4,
        max: 50,
        minMessage: 'le titre doit avoir au minimum {{ limit }} caractères',
        maxMessage: 'le titre ne doit pas avoir plus  {{ limit }} caractères'
        )]
    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[Assert\Length(
        min: 20,
        max: 250,
        minMessage: 'la description doit avoir au minimum de {{ limit }} caractères',
        maxMessage: 'la description ne doit pas avoir plus de  {{ limit }} caractères'
        )]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $lieu = null;

    #[ORM\Column]
    private ?float $prix = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[Assert\Valid]
    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Image $image = null;

    /**
     * @var Collection<int, categorie>
     */
    #[ORM\ManyToMany(targetEntity: Categorie::class, inversedBy: 'conferences')]
    private Collection $categorie;

    /**
     * @var Collection<int, Reservation>
     */
    // #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'conference')]
    // private Collection $reservations;

    /**
     * @var Collection<int, Commentaire>
     */
    #[ORM\OneToMany(targetEntity: Commentaire::class, mappedBy: 'conference', cascade: ['persist', 'remove'])]
    private Collection $commentaires;

    #[ORM\Column]
    private ?int $nbReservation = 0;

    #[ORM\ManyToOne(inversedBy: 'conferences',cascade: ['persist','remove'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $user = null;


    public function __construct()
    {
        $this->categorie    = new ArrayCollection();
        $this->reservations = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): static
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(?Image $image): static
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection<int, categorie>
     */
    public function getCategorie(): Collection
    {
        return $this->categorie;
    }

    public function addCategorie(categorie $categorie): static
    {
        if (!$this->categorie->contains($categorie)) {
            $this->categorie->add($categorie);
        }

        return $this;
    }

    public function removeCategorie(categorie $categorie): static
    {
        $this->categorie->removeElement($categorie);

        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): static
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setConference($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): static
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getConference() === $this) {
                $reservation->setConference(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Commentaire>
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): static
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires->add($commentaire);
            $commentaire->setConference($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): static
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getConference() === $this) {
                $commentaire->setConference(null);
            }
        }

        return $this;
    }

    // #[ORM\PostPersist]
    // public function ajout(){
    //     dd('message à afficher avant l\'ajout');
    // }


    
    public function getNbReservation(): ?int
    {
        return $this->nbReservation;
    }
    
    public function setNbReservation(int $nbReservation): static
    {
        
        $this->nbReservation = $nbReservation;

        return $this;
    }
       public function increaseReservation(){
       
        $this->nbReservation-- ;
    }
    public function decreaseReservation(){
       
        $this->nbReservation-- ;
    }

    #[Assert\Callback]
    public static function validate(mixed $value, ExecutionContextInterface $context, mixed $payload): void
{
    // somehow you have an array of "fake names"
    $fakeNames = ['fou','abandon'];

    $contenu = explode(' ', $value->getDescription());
   foreach($contenu as $val){
       // check if the name is actually a fake name
       if (in_array($val, $fakeNames)) {
           $context->buildViolation('This name sounds totally fake!')
               ->atPath('description')
               ->addViolation()
           ;
           break;
       }
    }
    $contenu = explode(',', $value->getDescription());
   foreach($contenu as $val){
       // check if the name is actually a fake name
       if (in_array($val, $fakeNames)) {
           $context->buildViolation('This name sounds totally fake!')
               ->atPath('description')
               ->addViolation()
           ;
           break;
       }
   }
}

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function setReservations(?Collection $reservations): static
    {
        $this->reservations = $reservations;

        return $this;
    }
}
