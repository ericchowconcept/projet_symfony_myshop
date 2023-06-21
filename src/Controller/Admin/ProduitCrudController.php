<?php

namespace App\Controller\Admin;

use App\Entity\Produit;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;

class ProduitCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Produit::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('titre'),
            TextEditorField::new('description'),
            TextField::new('couleur'),
            ChoiceField::new('taille')->setChoices([
                'Small' => 'small',
                'Medium'=> 'medium',
                'Large'=> 'large' 
            ]),
            ChoiceField::new('collection')->setChoices([
                'Homme' => 'homme',
                'Femme' => 'femme',
            ])->renderExpanded(),

            ImageField::new('photo')->setBasePath('images/produit')->setUploadDir('public/images/produit')->setUploadedFileNamePattern('[slug]-[timestamp].[extension]'),
            MoneyField::new('prix')->setCurrency('EUR'),
            NumberField::new('stock')->setNumDecimals(2),
            DateTimeField::new('date_enregistrement')->setFormat('d/M/Y à H:m:s')->hideOnForm(),
            
        ];
    }
    public function createEntity(string $entityFqcn)
    {
        $article = new $entityFqcn;
        $article->setDateEnregistrement(new \DateTime);
        return $article;
    }
  
}
