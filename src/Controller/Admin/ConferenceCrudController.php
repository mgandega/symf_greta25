<?php

namespace App\Controller\Admin;

use App\Entity\Conference;
use App\Entity\Image;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ConferenceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Conference::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('titre'),
            TextEditorField::new('description'),
            TextField::new('lieu'),
            MoneyField::new('prix')->setCurrency('EUR'),
            DateTimeField::new('date'),
            AssociationField::new('categorie')->autocomplete(),

            // IMPORTANT : Utiliser "image.url" au lieu de "image"
            ImageField::new('image.url', 'mon fichier')
                ->setBasePath('uploads/images')  // Pour affichage
                ->setUploadDir('public/uploads/images') // Chemin de stockage
                ->setUploadedFileNamePattern('[randomhash].[extension]') 
                ->setRequired(false),
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Conference) {
            $image = $entityInstance->getImage();

            if (!$image) {
                $image = new Image();
                $entityInstance->setImage($image);
            }

            $file = $image->getFile() ?? null;
            if ($file instanceof UploadedFile) {
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                $file->move('public/uploads/images', $fileName);
                $image->setUrl('uploads/images/' . $fileName);
            }

            if (!$image->getAlt()) {
                $image->setAlt('Image de la conférence');
            }

            $entityManager->persist($image);
        }
        parent::persistEntity($entityManager, $entityInstance);
    }
}
