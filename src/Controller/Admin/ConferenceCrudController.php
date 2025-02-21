<?php

namespace App\Controller\Admin;

use App\Entity\Conference;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

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

            ImageField::new('image', 'mon fichier')
                ->setBasePath('uploads/images')  // Pour l'affichage
                ->setUploadDir('public/uploads/images') // Chemin réel
                ->setUploadedFileNamePattern('[randomhash].[extension]') // monImage.jpeg => h234FGH56VBN6YIBN67HJ.jpeg
                ->setRequired(false),
    ];

    }
public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
{
    if ($entityInstance instanceof Conference) {
        $image = $entityInstance->getImage();

        // Si l'image est une simple chaîne (URL), créer un objet Image
        if (is_string($image)) {
            $newImage = new Image();
            $newImage->setUrl($image);
            $entityInstance->setImage($newImage);
            $image = $newImage; // Mise à jour de la variable image
        }

        $file = $image->getFile() ?? null;
        if ($file instanceof UploadedFile) {
            $filePath = 'uploads/images/' . $file->getClientOriginalName();
            $image->setUrl($filePath);
        }

        if (!$image->getAlt()) {
            $image->setAlt('Image de la conférence');
        }

        $entityManager->persist($image);
    }

    parent::persistEntity($entityManager, $entityInstance);
}


}
