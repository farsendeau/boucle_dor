<?php

namespace App\Controller\Admin;

use App\Entity\Gite\Gite;
use App\Form\EquipmentFormType;
use App\Form\GiteImageFormType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\PositiveOrZero;
use Vich\UploaderBundle\Form\Type\VichImageType;

#[IsGranted('ROLE_ADMIN')]
class GiteCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Gite::class;
    }


    public function configureFields(string $pageName): iterable
    {
        $fields =  [
            FormField::addTab('Infos générales'),
            TextField::new('name', 'Nom')
                ->setFormTypeOption('translation_domain', false),
            TextField::new('title', 'Titre')
                ->setFormTypeOption('translation_domain', false),
            TextField::new('summary', 'Résumé')
                ->setFormTypeOption('translation_domain', false),
            TextareaField::new('description', 'Description')
                ->setFormTypeOption('translation_domain', false),
            IntegerField::new('price', 'Prix de la nuitée')
                ->setFormTypeOption('translation_domain', false)
                ->setFormTypeOption('constraints', [new PositiveOrZero()])
                ->setFormTypeOption('attr', ['min' => 0]),
            BooleanField::new('onHomepage', 'Mettre en avant sur la page d\'accueil')
                ->setFormTypeOption('translation_domain', false),
            TextField::new('backgroundImage', 'Image de fond')
                ->setFormType(VichImageType::class)
                ->setFormTypeOptions([
                    'required' => false,
                    'allow_delete' => true,
                    'download_uri' => false,
                    'image_uri' => true,
                    'asset_helper' => true,
                    'constraints' => [new NotNull(['groups' => ['create']])]
                ])
                ->onlyOnForms()
        ];

        $fields[] = CollectionField::new('equipments', 'Equipements')
            ->setEntryType(EquipmentFormType::class)
            ->setFormTypeOptions([
                'by_reference' => false,
                'translation_domain' => false,
            ])
            ->allowAdd()
            ->allowDelete()
            ->onlyOnForms();

        $fields[] = CollectionField::new('giteImages', 'Images du gîte')
            ->setEntryType(GiteImageFormType::class)
            ->setFormTypeOptions([
                'by_reference' => false,
                'translation_domain' => false,
            ])
            ->allowAdd()
            ->allowDelete()
            ->onlyOnForms();

        $fields[] = FormField::addTab('Traductions Anglaises');
        $fields[] = TextField::new('titleEn', 'Titre EN')
            ->setFormTypeOption('translation_domain', false)
            ->onlyOnForms();
        $fields[] = TextField::new('summaryEn', 'Résumé EN')
            ->setFormTypeOption('translation_domain', false)
            ->onlyOnForms();
        $fields[] = TextareaField::new('descriptionEn', 'Description EN')
            ->setFormTypeOption('translation_domain', false)
            ->onlyOnForms();

        return $fields;
    }
}
