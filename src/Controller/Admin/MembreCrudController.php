<?php

namespace App\Controller\Admin;

use App\Entity\Membre;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Validator\Constraints\DateTime;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class MembreCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Membre::class;
    }
    
    public function __construct(public UserPasswordHasherInterface $hasher)
    {
        
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('pseudo'),
            ChoiceField::new('civilite')->setChoices([
                'Homme' => 'homme',
                'Femme' => 'femme',
                'Autres'=> 'autre'

            ]),
            TextField::new('prenom'),
            TextField::new('nom'),
            TextField::new('email'),
            TextField::new('password', 'mot de passe')->setFormType(PasswordType::class)->onlyWhenCreating(),
            CollectionField::new('roles')->setTemplatePath('admin/field/roles.html.twig'),
            DateTimeField::new('date_enregistrement')->setFormat('d/M/Y à H:m:s')->hideOnForm(),
            
        ];
    }

    public function createEntity(string $entityFqcn)
    {
        $produit = new $entityFqcn;
        $produit->setDateEnregistrement(new \DateTime);
        return $produit;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        // *on veut pouvoir hasher le password à la création car on cahce le password à la modification
        // *on verifie qu'il y ait pas d'ID dans l'objet actuellement lié au formulaire 
        // *donc on est en création
        if(!$entityInstance->getId())
        {
            // *on set le password dans l'objet lié au formulaire et d'abord on le hash
            $entityInstance->setPassword($this->hasher->hashPassword(
                $entityInstance, $entityInstance->getPassword()
            ));
        }
        $entityManager->persist($entityInstance);
        $entityManager->flush();
    }
   
}
