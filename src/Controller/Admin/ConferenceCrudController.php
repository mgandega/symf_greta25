<?php

namespace App\Controller\Admin;

use App\Entity\Image;
use App\Entity\Conference;
use Doctrine\ORM\EntityManagerInterface;
use App\Controller\Admin\ImageCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use Symfony\Component\Form\Extension\Core\Type\FileType;
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
            AssociationField::new('categorie'),
            Field::new('image.file', 'Image')->setFormType(FileType::class)// <-- Ajoute ce champ
            ->setRequired(false)
            ->hideOnIndex(),
            ImageField::new('image.url', 'Mon fichier')
                // ->setBasePath('uploads/images')  // Pour affichage
                // ->setUploadDir('public/uploads/images') // Chemin de stockage
                ->setUploadedFileNamePattern('[randomhash].[extension]') 
                ->setRequired(false)->hideOnForm()->hideOnDetail()
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Conference) {
             $image = $entityInstance->getImage() ?? new Image();
            $entityInstance->setImage($image);

            if (!$image) {
                $image = new Image();
                $entityInstance->setImage($image);
            }

            $file = $image->getFile() ?? null;
           
            // dd($image);
            if ($file instanceof UploadedFile) {
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                $chemin = $_SERVER['DOCUMENT_ROOT'].'/uploads/images';
                $file->move($chemin, $fileName);
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
