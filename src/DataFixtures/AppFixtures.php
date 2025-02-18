<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Conference;
use App\Entity\Commentaire;
use App\Entity\Reservation;
use App\Entity\Categorie;
use App\Entity\Image;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // categorie
        // image
        // conference
        // reservation
        // commentaire

       $faker = Factory::create();
       $categories = ["conferences sur symfony","conference sur drupal","conference 
        sur laravel"];
        $cat = []; // [$categorie1, $categories2, categories3]
        for ($i=0; $i <3 ; $i++) { 
           $categorie = new Categorie;
           $categorie->setNom($categories[$i]);
           $manager->persist($categorie);
           $cat[] = $categorie;
        }

       for($i=1; $i<=10; $i++){
           $image = new Image();
           $image->setUrl('https://www.laradiodesentreprises.com/wp-content/uploads/2022/04/_x_organisation-d-evenement.jpeg');
           $image->setAlt('image');
           $this->addReference('image'.$i,  $image);
           $manager->persist($image);
       }    
        $conferences = [];
        for($i=1; $i<=10; $i++){
        $conference = new Conference();
        $conference->setTitre($faker->sentence(3));
        $conference->setDescription($faker->sentence);
        $conference->setLieu($faker->streetAddress());
        $conference->setPrix($faker->numberBetween(100, 300));
        $conference->setDate($faker->datetime('Y-m-d'));
        $conference->setImage($this->getReference('image'.$i, Image::class));
        $conference->addCategorie($faker->randomElement($cat));
        $conference->setUser();
        $manager->persist($conference);
        $conferences[] = $conference; 
       }

       for($i=1; $i<=30; $i++ ){
        $reservation = new Reservation();
        $reservation->setContenu($faker->sentence);
        $reservation->setDate($faker->datetime('Y-m-d'));
        $reservation->setConference($faker->randomElement($conferences));
        $manager->persist($reservation);
       }

       for($i=1; $i<=60; $i++ ){
        $commentaire = new Commentaire();
        $commentaire->setPseudo($faker->name);
        $commentaire->setContenu($faker->sentence);
        $commentaire->setDate($faker->datetime('Y-m-d'));
        $commentaire->setConference($faker->randomElement($conferences));
        $manager->persist($commentaire);
       }
        $manager->flush();
    }
    }
